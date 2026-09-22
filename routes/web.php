<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoricoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\ServicoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clientes', ClienteController::class);

    Route::resource('servicos', ServicoController::class)->except(['show', 'destroy']);

    // Agendamentos
    Route::get('/agendamentos/horarios-disponiveis', [AgendamentoController::class, 'horariosDisponiveis'])->name('agendamentos.horarios-disponiveis');
    Route::patch('/agendamentos/{agendamento}/cancelar', [AgendamentoController::class, 'cancelar'])->name('agendamentos.cancelar');
    Route::resource('agendamentos', AgendamentoController::class)->except(['show', 'destroy']);

    // Horários
    Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');

    // Atendimentos
    Route::get('/atendimentos', [AtendimentoController::class, 'index'])->name('atendimentos.index');
    Route::patch('/atendimentos/{agendamento}/iniciar', [AtendimentoController::class, 'iniciar'])->name('atendimentos.iniciar');
    Route::patch('/atendimentos/{agendamento}/finalizar', [AtendimentoController::class, 'finalizar'])->name('atendimentos.finalizar');

    Route::get('/historico', [HistoricoController::class, 'index'])->name('historico.index');
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');

    // Configurações
    Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
    Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
