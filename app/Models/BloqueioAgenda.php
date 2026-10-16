<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BloqueioAgenda extends Model
{
    protected $fillable = [
        'data',
        'horario_inicio',
        'horario_fim',
        'motivo',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function bloqueiaIntervalo(Carbon $inicio, Carbon $fim): bool
    {
        if ($this->horario_inicio === null || $this->horario_fim === null) {
            return true;
        }

        return substr($this->horario_inicio, 0, 5) < $fim->format('H:i')
            && substr($this->horario_fim, 0, 5) > $inicio->format('H:i');
    }
}
