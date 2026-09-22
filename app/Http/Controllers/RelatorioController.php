<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['de' => ['nullable', 'date'], 'ate' => ['nullable', 'date', 'after_or_equal:de']]);
        $de = $validated['de'] ?? now()->startOfMonth()->toDateString();
        $ate = $validated['ate'] ?? now()->endOfMonth()->toDateString();
        $agendamentos = Agendamento::query()->whereBetween('data', [$de, $ate]);

        return view('relatorios.index', [
            'de' => $de, 'ate' => $ate,
            'totalAtendimentos' => (clone $agendamentos)->where('status', 'concluido')->count(),
            'totalConcluidos' => (clone $agendamentos)->where('status', 'concluido')->count(),
            'totalCancelados' => (clone $agendamentos)->where('status', 'cancelado')->count(),
            'novosClientes' => Cliente::whereBetween('created_at', [$de, $ate.' 23:59:59'])->count(),
            'porServico' => (clone $agendamentos)->where('agendamentos.status', 'concluido')->join('servicos', 'servicos.id', '=', 'agendamentos.servico_id')->selectRaw('servicos.nome as servico, count(*) as total, coalesce(sum(servicos.preco), 0) as receita')->groupBy('servicos.id', 'servicos.nome')->orderBy('servicos.nome')->get(),
        ]);
    }
}
