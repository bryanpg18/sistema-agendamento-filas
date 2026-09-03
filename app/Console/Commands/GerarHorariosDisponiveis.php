<?php

namespace App\Console\Commands;

use App\Models\HorarioDisponivel;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GerarHorariosDisponiveis extends Command
{
    protected $signature = 'horarios:gerar {dias=7}';

    protected $description = 'Gera horários disponíveis a partir de hoje';

    public function handle(): void
    {
        $diasParaGerar = (int) $this->argument('dias');
        $inicio = Carbon::today();

        for ($i = 0; $i < $diasParaGerar; $i++) {
            $data = $inicio->copy()->addDays($i);

            if ($data->isWeekend()) {
                continue;
            }

            $horaAtual = $data->copy()->setTime(8, 0);
            $horaFim = $data->copy()->setTime(17, 0);

            while ($horaAtual < $horaFim) {
                HorarioDisponivel::query()
                    ->whereDate('data', $data->toDateString())
                    ->where('horario', $horaAtual->format('H:i'))
                    ->firstOrCreate([], [
                        'data' => $data->toDateString(),
                        'horario' => $horaAtual->format('H:i'),
                        'status' => 'disponivel',
                    ]);

                $horaAtual->addMinutes(30);
            }

            $this->info("Horários gerados para {$data->format('d/m/Y')}");
        }
    }
}
