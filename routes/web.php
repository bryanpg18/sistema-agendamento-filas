<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\BloqueioAgendaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoricoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ListaEsperaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\UsuarioAcessoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clientes', ClienteController::class)->except(['destroy']);

    Route::get('/lista-espera', [ListaEsperaController::class, 'index'])->name('lista-espera.index');
    Route::post('/lista-espera', [ListaEsperaController::class, 'store'])->name('lista-espera.store');
    Route::patch('/lista-espera/{listaEspera}', [ListaEsperaController::class, 'update'])->name('lista-espera.update');
    Route::delete('/lista-espera/{listaEspera}', [ListaEsperaController::class, 'destroy'])->name('lista-espera.destroy');

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
    Route::middleware('admin')->group(function () {
        Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::resource('servicos', ServicoController::class)->except(['show', 'destroy']);
        Route::resource('bloqueios-agenda', BloqueioAgendaController::class)
            ->parameters(['bloqueios-agenda' => 'bloqueioAgenda'])
            ->only(['index', 'store', 'destroy']);
        Route::resource('usuarios-acesso', UsuarioAcessoController::class)
            ->parameters(['usuarios-acesso' => 'user'])
            ->only(['index', 'store', 'update']);
        Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
        Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
        Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');
    });

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
