<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\HorarioDisponivel;
use App\Models\Servico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HorarioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_horarios_are_generated_automatically_when_the_page_is_opened(): void
    {
        $user = User::factory()->create();

        $this->travelTo('2026-09-03 09:00:00');

        $this->actingAs($user)
            ->get(route('horarios.index', ['data' => '2026-09-03']))
            ->assertOk()
            ->assertViewHas('horarios', fn ($horarios): bool => $horarios->count() === 18)
            ->assertSee('08:00')
            ->assertSee('16:30');

        $this->assertDatabaseCount('horarios_disponiveis', 18);
    }

    public function test_horarios_are_filtered_by_the_status_of_their_agendamentos(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente de Teste',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
            'email' => 'cliente@example.com',
        ]);
        $servico = Servico::create([
            'nome' => 'Corte',
            'duracao_minutos' => 30,
            'preco' => 50,
        ]);

        HorarioDisponivel::create(['data' => '2026-09-03', 'horario' => '08:00', 'status' => 'disponivel']);
        HorarioDisponivel::create(['data' => '2026-09-03', 'horario' => '08:30', 'status' => 'disponivel']);
        HorarioDisponivel::create(['data' => '2026-09-03', 'horario' => '09:00', 'status' => 'disponivel']);

        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-03',
            'horario' => '08:00',
            'status' => 'confirmado',
        ]);
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-03',
            'horario' => '08:30',
            'status' => 'cancelado',
        ]);

        $this->actingAs($user)
            ->get(route('horarios.index', ['data' => '2026-09-03', 'status' => 'agendado']))
            ->assertOk()
            ->assertViewHas('horarios', fn ($horarios): bool => $horarios->pluck('hora_formatada')->all() === ['08:00']);

        $this->actingAs($user)
            ->get(route('horarios.index', ['data' => '2026-09-03', 'status' => 'cancelado']))
            ->assertOk()
            ->assertViewHas('horarios', fn ($horarios): bool => $horarios->pluck('hora_formatada')->all() === ['08:30']);
    }
}
