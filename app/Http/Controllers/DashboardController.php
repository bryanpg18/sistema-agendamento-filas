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
        $concluido = Agendamento::where('status', 'concluido')->count();
        $cancelado = Agendamento::where('status', 'cancelado')->count();

        $clientesCount = Cliente::count();
        $agendamentosHoje = Agendamento::whereDate('data', today())->count();
        $atendimentosAndamento = $emAtendimento;
        $concluidosHoje = $concluido;

        return view('dashboard', compact(
            'clientesCount',
            'agendamentosHoje',
            'atendimentosAndamento',
            'concluidosHoje',
            'emEspera',
            'emAtendimento',
            'concluido',
            'cancelado'
        ));
    }
}
