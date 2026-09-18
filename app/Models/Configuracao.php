<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'configuracoes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome_estabelecimento',
        'telefone',
        'email',
        'horario_abertura',
        'horario_fechamento',
        'duracao_padrao',
    ];

    /**
     * Get the current system configuration or a default instance.
     */
    public static function obter(): self
    {
        return static::first() ?? new static([
            'nome_estabelecimento' => '',
            'telefone' => '',
            'email' => '',
            'horario_abertura' => '08:00',
            'horario_fechamento' => '18:00',
            'duracao_padrao' => 30,
        ]);
    }
}
