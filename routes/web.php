<?php

use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('clientes', ClienteController::class);

    Route::view('/agendamentos', 'agendamentos.index')->name('agendamentos.index');
    Route::view('/horarios', 'horarios.index')->name('horarios.index');
    Route::get('/atendimentos', [AtendimentoController::class, 'index'])->name('atendimentos.index');
    Route::patch('/atendimentos/{agendamento}/iniciar', [AtendimentoController::class, 'iniciar'])->name('atendimentos.iniciar');
    Route::patch('/atendimentos/{agendamento}/finalizar', [AtendimentoController::class, 'finalizar'])->name('atendimentos.finalizar');
    Route::view('/historico', 'historico.index')->name('historico.index');
    Route::view('/relatorios', 'relatorios.index')->name('relatorios.index');
    Route::view('/configuracoes', 'configuracoes.index')->name('configuracoes.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
