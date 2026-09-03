<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioDisponivel extends Model
{
    use HasFactory;

    protected $table = 'horarios_disponiveis';

    protected $fillable = [
        'data',
        'horario',
        'status',
    ];

    protected $casts = [
        'data' => 'date',
    ];
}
