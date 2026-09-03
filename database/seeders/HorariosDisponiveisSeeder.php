<?php

namespace Database\Seeders;

use App\Models\HorarioDisponivel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HorariosDisponiveisSeeder extends Seeder
{
    public function run(): void
    {
        $inicio = Carbon::today();

        for ($i = 0; $i < 30; $i++) {
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
        }
    }
}
