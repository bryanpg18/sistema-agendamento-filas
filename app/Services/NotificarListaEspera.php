<?php

namespace App\Services;

use App\Models\Agendamento;
use App\Models\ListaEspera;
use App\Notifications\ListaEsperaNotificacao;

class NotificarListaEspera
{
    public function avisarVagaLiberada(Agendamento $agendamento): void
    {
        $agendamento->loadMissing(['cliente', 'servico']);
        $data = $agendamento->data->toDateString();
        $horario = substr((string) $agendamento->horario, 0, 5);

        $candidatos = ListaEspera::query()
            ->with('cliente')
            ->where('status', 'aguardando')
            ->where('servico_id', $agendamento->servico_id)
            ->whereDate('data_inicio', '<=', $data)
            ->where(function ($query) use ($data): void {
                $query->whereNull('data_fim')->orWhereDate('data_fim', '>=', $data);
            })
            ->where(function ($query) use ($horario): void {
                $query->whereNull('horario')->orWhere('horario', $horario);
            })
            ->orderBy('created_at')
            ->get();

        $listaEspera = $candidatos->first(fn (ListaEspera $candidato): bool => $this->possuiCanalConfigurado($candidato));

        if ($listaEspera === null) {
            return;
        }

        $listaEspera->cliente->notify(new ListaEsperaNotificacao(
            $listaEspera->cliente->nome_completo,
            $agendamento->servico->nome,
            $agendamento->data->format('d/m/Y'),
            $horario,
        ));

        $listaEspera->update([
            'status' => 'avisado',
            'avisado_em' => now(),
        ]);
    }

    private function possuiCanalConfigurado(ListaEspera $listaEspera): bool
    {
        $cliente = $listaEspera->cliente;

        return ($cliente->receber_notificacoes_email && filled($cliente->email))
            || (
                $cliente->receber_notificacoes_whatsapp
                && filled($cliente->telefone)
                && filled(config('services.whatsapp.access_token'))
                && filled(config('services.whatsapp.phone_number_id'))
                && filled(config('services.whatsapp.graph_version'))
                && filled(config('services.whatsapp.template_name'))
            );
    }
}
