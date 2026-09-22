<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Configuracao;
use App\Models\Servico;
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
        $agendamentos = Agendamento::with(['cliente:id,nome_completo', 'servico:id,nome'])
            ->when($request->busca, function ($query, $busca) {
                $query->whereHas('cliente', fn ($q) => $q->where('nome_completo', 'like', "%{$busca}%"));
            })
            ->when($request->data, fn ($query, $data) => $query->whereDate('data', $data))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('data')
            ->orderBy('horario')
            ->paginate(10)
            ->withQueryString();

        return view('agendamentos.index', [
            'agendamentos' => $agendamentos,
            'buscaSelecionada' => $request->busca,
            'dataSelecionada' => $request->data,
            'statusSelecionado' => $request->status,
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

        Agendamento::create($validated + ['status' => 'confirmado']);

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

    public function update(Request $request, Agendamento $agendamento): RedirectResponse
    {
        $validated = $this->validatedAgendamentoData($request, $agendamento);

        $agendamento->update($validated);

        return redirect()->route('agendamentos.index')
            ->with('sucesso', 'Agendamento atualizado com sucesso!');
    }

    public function cancelar(Agendamento $agendamento): RedirectResponse
    {
        abort_if($agendamento->status === 'cancelado', 404);

        $agendamento->update(['status' => 'cancelado']);

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

        $horarios = [];
        $horaAtual = $data->copy()->setTimeFromTimeString($config->horario_abertura);
        $horaFim = $data->copy()->setTimeFromTimeString($config->horario_fechamento);

        while ($horaAtual < $horaFim) {
            $hora = $horaAtual->format('H:i');

            if (! in_array($hora, $ocupados, true)) {
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
}
