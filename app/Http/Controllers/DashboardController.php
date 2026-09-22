<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;

class DashboardController extends Controller
{
    public function index()
    {
        $emEspera = Agendamento::where('status', 'em_espera')->count();
        $emAtendimento = Agendamento::where('status', 'em_atendimento')->count();
        $concluido = Agendamento::whereDate('data', today())->where('status', 'concluido')->count();
        $cancelado = Agendamento::where('status', 'cancelado')->count();

        $clientesCount = Cliente::count();
        $agendamentosHoje = Agendamento::whereDate('data', today())->count();
        $atendimentosAndamento = $emAtendimento;
        $concluidosHoje = $concluido;
        $proximosAgendamentos = Agendamento::with('cliente:id,nome_completo')
            ->whereDate('data', today())
            ->where('status', '!=', 'cancelado')
            ->orderBy('horario')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'clientesCount',
            'agendamentosHoje',
            'atendimentosAndamento',
            'concluidosHoje',
            'emEspera',
            'emAtendimento',
            'concluido',
            'cancelado',
            'proximosAgendamentos'
        ));
    }
}
