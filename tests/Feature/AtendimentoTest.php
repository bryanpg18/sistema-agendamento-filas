<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtendimentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_atendimentos(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('atendimentos.index'));

        $response->assertOk()
            ->assertViewIs('atendimentos.index');
    }

    public function test_authenticated_user_can_iniciar_atendimento(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente Teste',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
        ]);
        $servico = Servico::create([
            'nome' => 'Consulta',
            'duracao_minutos' => 30,
            'preco' => 100,
        ]);

        $agendamento = Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-18',
            'horario' => '10:00',
            'status' => 'confirmado',
        ]);

        $response = $this->actingAs($user)->patch(route('atendimentos.iniciar', $agendamento));

        $response->assertRedirect(route('atendimentos.index', ['status' => 'em_atendimento']))
            ->assertSessionHas('success', 'Atendimento iniciado com sucesso.');

        $this->assertEquals('em_atendimento', $agendamento->fresh()->status);
    }

    public function test_authenticated_user_can_finalizar_atendimento(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente Teste',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
        ]);
        $servico = Servico::create([
            'nome' => 'Consulta',
            'duracao_minutos' => 30,
            'preco' => 100,
        ]);

        $agendamento = Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-18',
            'horario' => '10:00',
            'status' => 'em_atendimento',
        ]);

        $response = $this->actingAs($user)->patch(route('atendimentos.finalizar', $agendamento));

        $response->assertRedirect(route('atendimentos.index', ['status' => 'concluido']))
            ->assertSessionHas('success', 'Atendimento finalizado com sucesso.');

        $this->assertEquals('concluido', $agendamento->fresh()->status);
    }
}
