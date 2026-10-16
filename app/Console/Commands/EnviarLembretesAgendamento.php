<?php

namespace App\Console\Commands;

use App\Models\Agendamento;
use App\Notifications\AgendamentoNotificacao;
use Illuminate\Console\Command;
use Throwable;

class EnviarLembretesAgendamento extends Command
{
    protected $signature = 'agendamentos:enviar-lembretes';

    protected $description = 'Envia lembretes dos agendamentos marcados para amanhã';

    public function handle(): void
    {
        $dataHoraLembrete = now(config('services.appointment_notifications.timezone'))
            ->addDay()
            ->startOfMinute();

        $agendamentos = Agendamento::query()
            ->with(['cliente', 'servico'])
            ->whereDate('data', $dataHoraLembrete->toDateString())
            ->whereTime('horario', $dataHoraLembrete->format('H:i:s'))
            ->whereIn('status', ['confirmado', 'em_espera'])
            ->whereNull('lembrete_enviado_em')
            ->orderBy('id')
            ->get();

        foreach ($agendamentos as $agendamento) {
            $notificacao = new AgendamentoNotificacao($agendamento, 'lembrete');

            if ($notificacao->via($agendamento->cliente) === []) {
                continue;
            }

            $marcado = Agendamento::query()
                ->whereKey($agendamento->id)
                ->whereNull('lembrete_enviado_em')
                ->update(['lembrete_enviado_em' => now()]);

            if ($marcado !== 1) {
                continue;
            }

            try {
                $agendamento->cliente->notify($notificacao);
            } catch (Throwable $exception) {
                Agendamento::query()
                    ->whereKey($agendamento->id)
                    ->whereNotNull('lembrete_enviado_em')
                    ->update(['lembrete_enviado_em' => null]);

                throw $exception;
            }

            $this->info("Lembrete enfileirado para o agendamento #{$agendamento->id}.");
        }
    }
}
