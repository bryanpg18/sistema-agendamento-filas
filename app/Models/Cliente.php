<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class Cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome_completo',
        'cpf',
        'telefone',
        'email',
        'data_nascimento',
        'observacoes',
    ];
    protected $casts = [
        'data_nascimento' => 'date',
    ];
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    protected function cpfFormatado(): Attribute
    {
        return Attribute::make(
            get: function () {
                $cpf = preg_replace('/\D/', '', (string) $this->cpf);
                if (strlen($cpf) !== 11) {
                    return $this->cpf;
                }
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
            }
        );
    }

    protected function telefoneFormatado(): Attribute
    {
        return Attribute::make(
            get: function () {
                $tel = preg_replace('/\D/', '', (string) $this->telefone);
                if (strlen($tel) === 11) {
                    return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $tel);
                }
                if (strlen($tel) === 10) {
                    return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $tel);
                }
                return $this->telefone;
            }
        );
    }
}