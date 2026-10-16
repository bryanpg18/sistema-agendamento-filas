<?php

namespace App\Observers;

use App\Models\Agendamento;
use App\Models\AgendamentoHistorico;
use Illuminate\Support\Facades\Auth;

class AgendamentoObserver
{
    public function created(Agendamento $agendamento): void
    {
        $alteracoes = collect($agendamento->getAttributes())
            ->except(['id', 'created_at', 'updated_at'])
            ->map(fn (mixed $valor): array => ['de' => null, 'para' => $valor])
            ->all();

        $this->registrar($agendamento, 'criado', $alteracoes);
    }

    public function updated(Agendamento $agendamento): void
    {
        $alteracoes = [];

        foreach ($agendamento->getChanges() as $campo => $novoValor) {
            if (in_array($campo, ['updated_at', 'created_at'], true)) {
                continue;
            }

            $alteracoes[$campo] = [
                'de' => $agendamento->getPrevious()[$campo] ?? null,
                'para' => $novoValor,
            ];
        }

        if ($alteracoes === []) {
            return;
        }

        $acao = match ($alteracoes['status']['para'] ?? null) {
            'em_atendimento' => 'iniciado',
            'concluido' => 'concluido',
            'cancelado' => 'cancelado',
            default => 'editado',
        };

        $this->registrar($agendamento, $acao, $alteracoes);
    }

    /**
     * @param  array<string, array{de: mixed, para: mixed}>  $alteracoes
     */
    private function registrar(Agendamento $agendamento, string $acao, array $alteracoes): void
    {
        AgendamentoHistorico::create([
            'agendamento_id' => $agendamento->id,
            'user_id' => Auth::id(),
            'acao' => $acao,
            'alteracoes' => $alteracoes,
        ]);
    }
}
