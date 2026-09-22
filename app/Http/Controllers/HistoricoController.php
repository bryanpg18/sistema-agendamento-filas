<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use Illuminate\View\View;

class HistoricoController extends Controller
{
    public function index(): View
    {
        return view('historico.index', [
            'historico' => Agendamento::with(['cliente:id,nome_completo', 'servico:id,nome'])
                ->whereIn('status', ['concluido', 'cancelado'])
                ->latest('data')->latest('horario')->paginate(15),
        ]);
    }
}
