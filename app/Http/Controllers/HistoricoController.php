<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\AgendamentoHistorico;
use Illuminate\View\View;

class HistoricoController extends Controller
{
    public function index(): View
    {
        return view('historico.index', [
            'historico' => Agendamento::with(['cliente:id,nome_completo', 'servico:id,nome'])
                ->whereIn('status', ['concluido', 'cancelado'])
                ->latest('data')->latest('horario')->paginate(15, ['*'], 'atendimentos_pagina')->withQueryString(),
            'alteracoes' => AgendamentoHistorico::with([
                'usuario:id,name',
                'agendamento.cliente:id,nome_completo',
            ])->latest()->paginate(20, ['*'], 'alteracoes_pagina')->withQueryString(),
        ]);
    }
}
