<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Configuracao;
use App\Models\HorarioDisponivel;
use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'data' => ['nullable', 'date'],
            'servico_id' => ['nullable', 'integer', Rule::exists('servicos', 'id')],
            'status' => ['nullable', Rule::in(['disponivel', 'agendado', 'cancelado'])],
        ]);

        $data = $validated['data'] ?? now()->toDateString();
        $servicoId = $validated['servico_id'] ?? null;
        $status = $validated['status'] ?? null;

        $this->gerarHorariosParaData($data);

        $agendamentos = Agendamento::whereDate('data', $data)
            ->when($servicoId, fn ($query) => $query->where('servico_id', $servicoId))
            ->get()
            ->keyBy(fn ($agendamento) => substr($agendamento->horario, 0, 5));

        $horarios = HorarioDisponivel::whereDate('data', $data)
            ->orderBy('horario')
            ->get()
            ->map(function ($horario) use ($agendamentos) {
                $horario->hora_formatada = substr($horario->horario, 0, 5);
                $agendamento = $agendamentos->get($horario->hora_formatada);

                if (! $agendamento) {
                    $horario->status_exibicao = 'disponivel';
                } elseif ($agendamento->status === 'cancelado') {
                    $horario->status_exibicao = 'cancelado';
                } else {
                    $horario->status_exibicao = 'agendado';
                }

                return $horario;
            })
            ->when($status, fn ($horarios) => $horarios->where('status_exibicao', $status))
            ->values();

        $servicos = Servico::where('ativo', true)->orderBy('nome')->get();

        return view('horarios.index', [
            'horarios' => $horarios,
            'servicos' => $servicos,
            'dataSelecionada' => $data,
            'servicoSelecionado' => $servicoId,
            'statusSelecionado' => $status,
        ]);
    }

    private function gerarHorariosParaData(string $data): void
    {
        $dataSelecionada = Carbon::parse($data);

        if ($dataSelecionada->isWeekend()) {
            return;
        }

        $config = Configuracao::obter();
        $horaAtual = $dataSelecionada->copy()->setTimeFromTimeString($config->horario_abertura);
        $horaFim = $dataSelecionada->copy()->setTimeFromTimeString($config->horario_fechamento);

        while ($horaAtual < $horaFim) {
            HorarioDisponivel::query()
                ->whereDate('data', $dataSelecionada->toDateString())
                ->where('horario', $horaAtual->format('H:i'))
                ->firstOrCreate([], [
                    'data' => $dataSelecionada->toDateString(),
                    'horario' => $horaAtual->format('H:i'),
                    'status' => 'disponivel',
                ]);

            $horaAtual->addMinutes($config->duracao_padrao);
        }
    }
}
