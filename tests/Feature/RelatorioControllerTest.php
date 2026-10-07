<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatorioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_reports(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('relatorios.index'))
            ->assertOk()
            ->assertSee('Relatórios');
    }

    public function test_report_counts_non_cancelled_appointments_separately_from_completed_and_cancelled(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente de Relatório',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
        ]);
        $servico = Servico::create([
            'nome' => 'Consulta',
            'duracao_minutos' => 30,
            'preco' => 50,
            'ativo' => true,
        ]);

        foreach (['confirmado', 'em_atendimento', 'concluido', 'cancelado'] as $status) {
            Agendamento::create([
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data' => '2026-10-05',
                'horario' => match ($status) {
                    'confirmado' => '08:00',
                    'em_atendimento' => '08:30',
                    'concluido' => '09:00',
                    default => '09:30',
                },
                'status' => $status,
            ]);
        }

        $this->actingAs($user)
            ->get(route('relatorios.index', ['de' => '2026-10-01', 'ate' => '2026-10-31']))
            ->assertViewHas('totalAtendimentos', 3)
            ->assertViewHas('totalConcluidos', 1)
            ->assertViewHas('totalCancelados', 1);
    }
}
