<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListaEspera extends Model
{
    protected $fillable = [
        'cliente_id',
        'servico_id',
        'data_inicio',
        'data_fim',
        'horario',
        'status',
        'avisado_em',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'avisado_em' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class);
    }
}
