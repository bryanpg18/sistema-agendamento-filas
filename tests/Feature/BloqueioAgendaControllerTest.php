<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\BloqueioAgenda;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BloqueioAgendaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_remove_a_full_day_block(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->travelTo('2026-10-16 08:00:00');

        $response = $this->actingAs($user)->post(route('bloqueios-agenda.store'), [
            'data' => '2026-10-19',
            'data_fim' => '2026-10-21',
            'motivo' => 'Feriado',
            'dia_inteiro' => '1',
        ]);

        $response->assertRedirect(route('bloqueios-agenda.index'))
            ->assertSessionHas('sucesso', 'Bloqueio de agenda cadastrado com sucesso.');

        $this->assertDatabaseCount('bloqueio_agendas', 3);

        $bloqueio = BloqueioAgenda::whereDate('data', '2026-10-19')->firstOrFail();
        $this->assertNull($bloqueio->horario_inicio);
        $this->assertNull($bloqueio->horario_fim);

        $this->delete(route('bloqueios-agenda.destroy', $bloqueio))
            ->assertRedirect(route('bloqueios-agenda.index'));

        $this->assertDatabaseMissing('bloqueio_agendas', ['id' => $bloqueio->id]);
    }

    public function test_partial_block_hides_overlapping_slots_and_rejects_booking(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente Teste',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
        ]);
        $servico = Servico::create([
            'nome' => 'Consulta',
            'duracao_minutos' => 30,
            'preco' => 50,
            'ativo' => true,
        ]);
        $this->travelTo('2026-10-16 08:00:00');

        $response = $this->actingAs($user)->post(route('bloqueios-agenda.store'), [
            'data' => '2026-10-19',
            'motivo' => 'Almoço',
            'dia_inteiro' => '0',
            'horario_inicio' => '12:00',
            'horario_fim' => '13:00',
        ])->assertRedirect(route('bloqueios-agenda.index'));

        $horariosResponse = $this->getJson(route('agendamentos.horarios-disponiveis', ['data' => '2026-10-19']))
            ->assertOk();

        $this->assertNotContains('12:00', $horariosResponse->json('horarios'));
        $this->assertContains('13:00', $horariosResponse->json('horarios'));

        $this->post(route('agendamentos.store'), [
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-10-19',
            'horario' => '12:00',
        ])->assertSessionHasErrors(['horario' => 'Este período está bloqueado na agenda. Escolha outro horário.']);

        $this->assertDatabaseCount('agendamentos', 0);
    }

    public function test_block_cannot_overlap_an_existing_appointment(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente Teste',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
        ]);
        $servico = Servico::create([
            'nome' => 'Consulta',
            'duracao_minutos' => 60,
            'preco' => 50,
            'ativo' => true,
        ]);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-10-19',
            'horario' => '12:00',
            'status' => 'confirmado',
        ]);
        $this->travelTo('2026-10-16 08:00:00');

        $response = $this->actingAs($user)->post(route('bloqueios-agenda.store'), [
            'data' => '2026-10-19',
            'motivo' => 'Almoço',
            'dia_inteiro' => '0',
            'horario_inicio' => '12:30',
            'horario_fim' => '13:30',
        ]);
        $response->assertSessionHasErrors('horario_inicio');

        $this->assertDatabaseCount('bloqueio_agendas', 0);
    }

    public function test_blocked_slots_are_shown_in_the_schedule_view(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->travelTo('2026-10-16 08:00:00');
        BloqueioAgenda::create([
            'data' => '2026-10-19',
            'horario_inicio' => '12:00',
            'horario_fim' => '13:00',
            'motivo' => 'Almoço',
        ]);

        $response = $this->actingAs($user)
            ->get(route('horarios.index', ['data' => '2026-10-19', 'status' => 'bloqueado']))
            ->assertOk();

        $this->assertSame(['12:00', '12:30'], $response->viewData('horarios')->pluck('hora_formatada')->all());
    }
}
