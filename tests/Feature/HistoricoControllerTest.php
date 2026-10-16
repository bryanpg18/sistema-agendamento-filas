<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_history(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente do Histórico',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
        ]);
        $servico = Servico::create([
            'nome' => 'Consulta',
            'duracao_minutos' => 30,
            'preco' => 100,
        ]);

        $this->actingAs($user);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-10-20',
            'horario' => '10:00',
            'status' => 'confirmado',
        ]);

        $this->get(route('historico.index'))
            ->assertOk()
            ->assertSee('Histórico')
            ->assertSee('Alterações dos agendamentos')
            ->assertSee('Cliente do Histórico')
            ->assertSee('Criado')
            ->assertSee($user->name);
    }
}
