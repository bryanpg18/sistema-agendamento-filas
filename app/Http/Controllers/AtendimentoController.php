<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AtendimentoController extends Controller
{
    /** @var array<string, list<string>> */
    private const STATUS_POR_ABA = [
        'em_espera' => ['confirmado', 'em_espera'],
        'em_atendimento' => ['em_atendimento'],
        'concluido' => ['concluido'],
        'cancelado' => ['cancelado'],
    ];

    public function index(Request $request): View
    {
        $statusAtivo = $request->string('status')->toString();

        if (! array_key_exists($statusAtivo, self::STATUS_POR_ABA)) {
            $statusAtivo = 'em_atendimento';
        }

        $contagens = collect(self::STATUS_POR_ABA)
            ->map(fn (array $statuses): int => Agendamento::whereIn('status', $statuses)->count())
            ->all();

        $agendamentos = Agendamento::query()
            ->with(['cliente:id,nome_completo', 'servico:id,nome'])
            ->whereIn('status', self::STATUS_POR_ABA[$statusAtivo])
            ->orderBy('data')
            ->orderBy('horario')
            ->get();

        return view('atendimentos.index', compact('agendamentos', 'contagens', 'statusAtivo'));
    }

    public function iniciar(Agendamento $agendamento): RedirectResponse
    {
        abort_unless(in_array($agendamento->status, self::STATUS_POR_ABA['em_espera'], true), 404);

        $agendamento->update(['status' => 'em_atendimento']);

        return redirect()->route('atendimentos.index', ['status' => 'em_atendimento'])
            ->with('success', 'Atendimento iniciado com sucesso.');
    }

    public function finalizar(Agendamento $agendamento): RedirectResponse
    {
        abort_unless($agendamento->status === 'em_atendimento', 404);

        $agendamento->update(['status' => 'concluido']);

        return redirect()->route('atendimentos.index', ['status' => 'concluido'])
            ->with('success', 'Atendimento finalizado com sucesso.');
    }
}
