<?php

namespace App\Providers;

use App\Models\Agendamento;
use App\Observers\AgendamentoObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        App::setLocale('pt_BR');
        Agendamento::observe(AgendamentoObserver::class);
    }
}
