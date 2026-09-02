<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_real_status_counts_for_chart_and_top_card(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente Teste',
            'cpf' => '123.456.789-10',
            'telefone' => '(11) 99999-9999',
            'email' => 'cliente@example.com',
        ]);
        $servico = Servico::create([
            'nome' => 'Corte',
            'duracao_minutos' => 30,
            'preco' => 50,
        ]);

        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => today()->toDateString(),
            'horario' => '08:00:00',
            'status' => 'em_espera',
        ]);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => today()->toDateString(),
            'horario' => '09:00:00',
            'status' => 'em_espera',
        ]);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => today()->toDateString(),
            'horario' => '10:00:00',
            'status' => 'em_atendimento',
        ]);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => today()->toDateString(),
            'horario' => '11:00:00',
            'status' => 'concluido',
        ]);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => today()->toDateString(),
            'horario' => '12:00:00',
            'status' => 'concluido',
        ]);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => today()->toDateString(),
            'horario' => '13:00:00',
            'status' => 'concluido',
        ]);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => today()->toDateString(),
            'horario' => '14:00:00',
            'status' => 'cancelado',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('emEspera', 2);
        $response->assertViewHas('emAtendimento', 1);
        $response->assertViewHas('concluido', 3);
        $response->assertViewHas('cancelado', 1);
        $response->assertSee('Concluídos');
        $response->assertSee('value: 3', false);
    }
}
