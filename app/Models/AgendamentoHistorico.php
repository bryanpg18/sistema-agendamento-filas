<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendamentoHistorico extends Model
{
    protected $fillable = [
        'agendamento_id',
        'user_id',
        'acao',
        'alteracoes',
    ];

    protected $casts = [
        'alteracoes' => 'array',
    ];

    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
