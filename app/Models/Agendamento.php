<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'servico_id',
        'data',
        'horario',
        'status',
        'observacoes',
        'preco_servico',
    ];

    protected $casts = [
        'data' => 'date',
        'preco_servico' => 'decimal:2',
        'lembrete_enviado_em' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(AgendamentoHistorico::class);
    }
}
