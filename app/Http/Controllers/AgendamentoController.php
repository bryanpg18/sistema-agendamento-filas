<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\BloqueioAgenda;
use App\Models\Cliente;
use App\Models\Configuracao;
use App\Models\Servico;
use App\Notifications\AgendamentoNotificacao;
use App\Services\NotificarListaEspera;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgendamentoController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'busca' => ['nullable', 'string', 'max:100'],
            'data' => ['nullable', 'date'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', Rule::when($request->filled('data_inicio'), ['after_or_equal:data_inicio'])],
            'status' => ['nullable', Rule::in(['confirmado', 'em_espera', 'em_atendimento', 'concluido', 'cancelado'])],
            'servico_id' => ['nullable', 'integer', Rule::exists('servicos', 'id')],
        ]);

        $agendamentos = Agendamento::with(['cliente:id,nome_completo', 'servico:id,nome'])
            ->when($filtros['busca'] ?? null, function ($query, $busca) {
                $documento = preg_replace('/\D+/', '', $busca) ?? '';
                $telefone = $documento;

                $query->whereHas('cliente', function ($clienteQuery) use ($busca, $documento, $telefone) {
                    $clienteQuery->where(function ($query) use ($busca, $documento, $telefone) {
                        $query->where('nome_completo', 'like', "%{$busca}%")
                            ->orWhere('email', 'like', "%{$busca}%");

                        if ($documento !== '') {
                            $query->orWhere('cpf', 'like', "%{$documento}%")
                                ->orWhere('telefone', 'like', "%{$telefone}%");
                        }
                    });
                });
            })
            ->when($filtros['data'] ?? null, fn ($query, $data) => $query->whereDate('data', $data))
            ->when($filtros['data_inicio'] ?? null, fn ($query, $data) => $query->whereDate('data', '>=', $data))
            ->when($filtros['data_fim'] ?? null, fn ($query, $data) => $query->whereDate('data', '<=', $data))
            ->when($filtros['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filtros['servico_id'] ?? null, fn ($query, $servicoId) => $query->where('servico_id', $servicoId))
            ->orderBy('data')
            ->orderBy('horario')
            ->paginate(10)
            ->withQueryString();

        return view('agendamentos.index', [
            'agendamentos' => $agendamentos,
            'servicos' => Servico::orderBy('nome')->get(['id', 'nome']),
            'filtros' => $filtros,
        ]);
    }

    public function create(): View
    {
        return view('agendamentos.create', [
            'clientes' => Cliente::orderBy('nome_completo')->get(),
            'servicos' => Servico::where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedAgendamentoData($request);

        $servico = Servico::findOrFail($validated['servico_id']);

        $agendamento = Agendamento::create($validated + [
            'status' => 'confirmado',
            'preco_servico' => $servico->preco,
        ]);
        $this->notificarCliente($agendamento, 'confirmacao');

        return redirect()->route('agendamentos.index')
            ->with('sucesso', 'Agendamento realizado com sucesso!');
    }

    public function edit(Agendamento $agendamento): View
    {
        return view('agendamentos.edit', [
            'agendamento' => $agendamento,
            'clientes' => Cliente::orderBy('nome_completo')->get(),
            'servicos' => Servico::where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function update(Request $request, Agendamento $agendamento, NotificarListaEspera $notificarListaEspera): RedirectResponse
    {
        $validated = $this->validatedAgendamentoData($request, $agendamento);
        $agendamentoAnterior = clone $agendamento;

        $precoServico = $agendamento->servico_id === (int) $validated['servico_id']
            ? $agendamento->preco_servico
            : Servico::findOrFail($validated['servico_id'])->preco;

        $agendamento->fill($validated + ['preco_servico' => $precoServico]);
        $houveAlteracao = $agendamento->isDirty();
        $mudouHorarioOuServico = $agendamento->isDirty(['cliente_id', 'servico_id', 'data', 'horario']);
        $agendamento->save();

        if ($houveAlteracao) {
            $this->notificarCliente($agendamento, 'alteracao');
        }

        if ($mudouHorarioOuServico && $agendamento->lembrete_enviado_em !== null) {
            Agendamento::query()->whereKey($agendamento->id)->update(['lembrete_enviado_em' => null]);
            $agendamento->lembrete_enviado_em = null;
        }

        if ($mudouHorarioOuServico) {
            $notificarListaEspera->avisarVagaLiberada($agendamentoAnterior);
        }

        return redirect()->route('agendamentos.index')
            ->with('sucesso', 'Agendamento atualizado com sucesso!');
    }

    public function cancelar(Agendamento $agendamento, NotificarListaEspera $notificarListaEspera): RedirectResponse
    {
        abort_if($agendamento->status === 'cancelado', 404);

        $agendamento->update(['status' => 'cancelado']);
        $this->notificarCliente($agendamento, 'cancelamento');
        $notificarListaEspera->avisarVagaLiberada($agendamento);

        return redirect()->route('agendamentos.index')
            ->with('sucesso', 'Agendamento cancelado com sucesso!');
    }

    public function horariosDisponiveis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => ['required', 'date'],
            'agendamento_id' => ['nullable', 'integer', Rule::exists('agendamentos', 'id')],
        ]);

        $data = Carbon::parse($validated['data']);
        $config = Configuracao::obter();

        if ($data->isWeekend()) {
            return response()->json(['horarios' => []]);
        }

        $ocupados = Agendamento::whereDate('data', $data->toDateString())
            ->where('status', '!=', 'cancelado')
            ->when($validated['agendamento_id'] ?? null, fn ($query, $id) => $query->where('id', '!=', $id))
            ->get()
            ->map(fn (Agendamento $agendamento) => substr($agendamento->horario, 0, 5))
            ->all();
        $bloqueios = BloqueioAgenda::whereDate('data', $data->toDateString())->get();

        $horarios = [];
        $horaAtual = $data->copy()->setTimeFromTimeString($config->horario_abertura);
        $horaFim = $data->copy()->setTimeFromTimeString($config->horario_fechamento);

        while ($horaAtual < $horaFim) {
            $hora = $horaAtual->format('H:i');

            $fimHorario = $horaAtual->copy()->addMinutes($config->duracao_padrao);
            $estaBloqueado = $bloqueios->contains(fn (BloqueioAgenda $bloqueio): bool => $bloqueio->bloqueiaIntervalo($horaAtual, $fimHorario));

            if (! in_array($hora, $ocupados, true) && ! $estaBloqueado) {
                $horarios[] = $hora;
            }

            $horaAtual->addMinutes($config->duracao_padrao);
        }

        return response()->json(['horarios' => $horarios]);
    }

    private function validatedAgendamentoData(Request $request, ?Agendamento $agendamento = null): array
    {
        $data = $request->only(['cliente_id', 'servico_id', 'data', 'horario', 'observacoes']);

        $validator = Validator::make($data, [
            'cliente_id' => ['required', 'integer', Rule::exists('clientes', 'id')],
            'servico_id' => ['required', 'integer', Rule::exists('servicos', 'id')->where('ativo', true)],
            'data' => ['required', 'date', 'after_or_equal:today'],
            'horario' => ['required', 'date_format:H:i'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $validator->after(function ($validator) use ($data, $agendamento) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $config = Configuracao::obter();
            $dataAgendamento = Carbon::parse($data['data']);
            $inicio = $dataAgendamento->copy()->setTimeFromTimeString($config->horario_abertura);
            $fim = $dataAgendamento->copy()->setTimeFromTimeString($config->horario_fechamento);
            $horario = $dataAgendamento->copy()->setTimeFromTimeString($data['horario']);

            if ($dataAgendamento->isWeekend()) {
                $validator->errors()->add('data', 'Não há atendimento aos fins de semana.');
            }

            if ($horario < $inicio || $horario >= $fim || $inicio->diffInMinutes($horario) % $config->duracao_padrao !== 0) {
                $validator->errors()->add('horario', 'Selecione um horário disponível dentro do expediente.');
            }

            $fimHorario = $horario->copy()->addMinutes($config->duracao_padrao);
            $horarioBloqueado = BloqueioAgenda::whereDate('data', $data['data'])
                ->get()
                ->contains(fn (BloqueioAgenda $bloqueio): bool => $bloqueio->bloqueiaIntervalo($horario, $fimHorario));

            if ($horarioBloqueado) {
                $validator->errors()->add('horario', 'Este período está bloqueado na agenda. Escolha outro horário.');
            }

            $horarioOcupado = Agendamento::whereDate('data', $data['data'] ?? null)
                ->where('status', '!=', 'cancelado')
                ->when($agendamento, fn ($query) => $query->where('id', '!=', $agendamento->id))
                ->get()
                ->contains(fn (Agendamento $existente) => substr($existente->horario, 0, 5) === ($data['horario'] ?? null));

            if ($horarioOcupado) {
                $validator->errors()->add('horario', 'Este horário já está reservado. Escolha outro.');
            }
        });

        return $validator->validate();
    }

    private function notificarCliente(Agendamento $agendamento, string $evento): void
    {
        $agendamento->loadMissing('cliente');
        $agendamento->cliente->notify(new AgendamentoNotificacao($agendamento, $evento));
    }
}
