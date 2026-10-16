<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('horarios:gerar 7')->daily();
Schedule::command('agendamentos:enviar-lembretes')
    ->everyMinute()
    ->timezone(config('services.appointment_notifications.timezone'))
    ->withoutOverlapping();
