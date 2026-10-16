<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\BloqueioAgenda;
use App\Models\Configuracao;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BloqueioAgendaController extends Controller
{
    public function index(): View
    {
        return view('bloqueios-agenda.index', [
            'bloqueios' => BloqueioAgenda::query()->orderBy('data')->orderBy('horario_inicio')->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'data' => ['required', 'date', 'after_or_equal:today'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data'],
            'motivo' => ['required', 'string', 'max:255'],
            'dia_inteiro' => ['required', 'boolean'],
            'horario_inicio' => ['nullable', 'required_unless:dia_inteiro,1', 'date_format:H:i'],
            'horario_fim' => ['nullable', 'required_unless:dia_inteiro,1', 'date_format:H:i', 'after:horario_inicio'],
        ], [
            'data.after_or_equal' => 'A data do bloqueio deve ser hoje ou uma data futura.',
            'horario_inicio.required_unless' => 'Informe o início do bloqueio ou marque o dia inteiro.',
            'horario_fim.required_unless' => 'Informe o fim do bloqueio ou marque o dia inteiro.',
            'horario_fim.after' => 'O fim do bloqueio deve ser posterior ao início.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $diaInteiro = $request->boolean('dia_inteiro');
            $config = Configuracao::obter();
            $dataInicio = Carbon::parse($request->string('data')->toString());
            $dataFim = Carbon::parse($request->input('data_fim') ?: $request->string('data')->toString());

            if ($dataInicio->diffInDays($dataFim) > 365) {
                $validator->errors()->add('data_fim', 'O período máximo de bloqueio é de 366 dias.');

                return;
            }

            $bloqueiosPorData = BloqueioAgenda::whereDate('data', '>=', $dataInicio->toDateString())
                ->whereDate('data', '<=', $dataFim->toDateString())
                ->get()
                ->groupBy(fn (BloqueioAgenda $bloqueio): string => $bloqueio->data->toDateString());
            $agendamentosNoPeriodo = Agendamento::query()
                ->with('servico:id,duracao_minutos')
                ->whereDate('data', '>=', $dataInicio->toDateString())
                ->whereDate('data', '<=', $dataFim->toDateString())
                ->where('status', '!=', 'cancelado')
                ->get();

            for ($data = $dataInicio->copy(); $data->lte($dataFim); $data->addDay()) {
                $inicio = $diaInteiro ? null : $data->copy()->setTimeFromTimeString($request->string('horario_inicio')->toString());
                $fim = $diaInteiro ? null : $data->copy()->setTimeFromTimeString($request->string('horario_fim')->toString());

                if (! $diaInteiro) {
                    $abertura = $data->copy()->setTimeFromTimeString($config->horario_abertura);
                    $fechamento = $data->copy()->setTimeFromTimeString($config->horario_fechamento);

                    if ($inicio < $abertura || $fim > $fechamento) {
                        $validator->errors()->add('horario_inicio', 'O bloqueio deve estar dentro do expediente configurado.');

                        return;
                    }
                }

                $bloqueiosExistentes = $bloqueiosPorData->get($data->toDateString(), collect());
                $bloqueioConflitante = $bloqueiosExistentes->contains(
                    fn (BloqueioAgenda $bloqueio): bool => $diaInteiro || $bloqueio->bloqueiaIntervalo($inicio, $fim),
                );

                if ($bloqueioConflitante) {
                    $validator->errors()->add($diaInteiro ? 'data' : 'horario_inicio', 'Este período já possui um bloqueio de agenda.');

                    return;
                }

                $agendamentos = $agendamentosNoPeriodo->filter(
                    fn (Agendamento $agendamento): bool => substr((string) $agendamento->getRawOriginal('data'), 0, 10) === $data->toDateString(),
                );

                $conflitoComAgendamento = $diaInteiro && $agendamentos->isNotEmpty();

                if (! $diaInteiro) {
                    $conflitoComAgendamento = $agendamentos->contains(function (Agendamento $agendamento) use ($data, $inicio, $fim, $config): bool {
                        $inicioAgendamento = $data->copy()->setTimeFromTimeString($agendamento->horario);
                        $fimAgendamento = $inicioAgendamento->copy()->addMinutes($agendamento->servico?->duracao_minutos ?? $config->duracao_padrao);

                        return $inicioAgendamento < $fim && $fimAgendamento > $inicio;
                    });
                }

                if ($conflitoComAgendamento) {
                    $validator->errors()->add($diaInteiro ? 'data' : 'horario_inicio', 'Não é possível bloquear um período que já possui agendamento.');

                    return;
                }
            }
        });

        $validated = $validator->validate();
        $diaInteiro = (bool) $validated['dia_inteiro'];

        $dataFim = Carbon::parse($validated['data_fim'] ?? $validated['data']);

        DB::transaction(function () use ($validated, $diaInteiro, $dataFim): void {
            for ($data = Carbon::parse($validated['data']); $data->lte($dataFim); $data->addDay()) {
                BloqueioAgenda::create([
                    'data' => $data->toDateString(),
                    'horario_inicio' => $diaInteiro ? null : $validated['horario_inicio'],
                    'horario_fim' => $diaInteiro ? null : $validated['horario_fim'],
                    'motivo' => $validated['motivo'],
                ]);
            }
        });

        return redirect()->route('bloqueios-agenda.index')
            ->with('sucesso', 'Bloqueio de agenda cadastrado com sucesso.');
    }

    public function destroy(BloqueioAgenda $bloqueioAgenda): RedirectResponse
    {
        $bloqueioAgenda->delete();

        return redirect()->route('bloqueios-agenda.index')
            ->with('sucesso', 'Bloqueio de agenda removido com sucesso.');
    }
}
