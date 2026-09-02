<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nome_completo',
        'cpf',
        'telefone',
        'email',
        'data_nascimento',
        'observacoes',
    ];
}