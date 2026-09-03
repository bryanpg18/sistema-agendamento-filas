<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendamentoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_agendamentos_list_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('agendamentos.index'))
            ->assertOk()
            ->assertSee('Agendamentos');
    }

    public function test_create_page_lists_clientes_e_servicos(): void
    {
        $user = User::factory()->create();
        $cliente = $this->criarCliente();
        $servico = $this->criarServico();

        $this->actingAs($user)
            ->get(route('agendamentos.create'))
            ->assertOk()
            ->assertSee($cliente->nome_completo)
            ->assertSee($servico->nome);
    }

    public function test_agendamento_pode_ser_criado(): void
    {
        $user = User::factory()->create();
        $cliente = $this->criarCliente();
        $servico = $this->criarServico();

        $this->actingAs($user)
            ->post(route('agendamentos.store'), [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data' => '2026-09-04',
                'horario' => '09:00',
                'observacoes' => 'Primeira visita',
            ])
            ->assertRedirect(route('agendamentos.index'));

        $this->assertDatabaseHas('agendamentos', [
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'status' => 'confirmado',
        ]);
    }

    public function test_nao_permite_agendar_em_horario_ja_ocupado(): void
    {
        $user = User::factory()->create();
        $cliente = $this->criarCliente();
        $servico = $this->criarServico();

        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-04',
            'horario' => '09:00',
            'status' => 'confirmado',
        ]);

        $this->actingAs($user)
            ->post(route('agendamentos.store'), [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data' => '2026-09-04',
                'horario' => '09:00',
            ])
            ->assertSessionHasErrors('horario');

        $this->assertDatabaseCount('agendamentos', 1);
    }

    public function test_permite_agendar_em_horario_que_estava_cancelado(): void
    {
        $user = User::factory()->create();
        $cliente = $this->criarCliente();
        $servico = $this->criarServico();

        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-04',
            'horario' => '09:00',
            'status' => 'cancelado',
        ]);

        $this->actingAs($user)
            ->post(route('agendamentos.store'), [
                'cliente_id' => $cliente->id,
                'servico_id' => $servico->id,
                'data' => '2026-09-04',
                'horario' => '09:00',
            ])
            ->assertRedirect(route('agendamentos.index'))
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseCount('agendamentos', 2);
    }

    public function test_agendamento_pode_ser_cancelado(): void
    {
        $user = User::factory()->create();
        $cliente = $this->criarCliente();
        $servico = $this->criarServico();

        $agendamento = Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-04',
            'horario' => '09:00',
            'status' => 'confirmado',
        ]);

        $this->actingAs($user)
            ->patch(route('agendamentos.cancelar', $agendamento))
            ->assertRedirect(route('agendamentos.index'));

        $this->assertDatabaseHas('agendamentos', [
            'id' => $agendamento->id,
            'status' => 'cancelado',
        ]);
    }

    public function test_endpoint_de_horarios_disponiveis_exclui_horarios_ja_ocupados(): void
    {
        $user = User::factory()->create();
        $cliente = $this->criarCliente();
        $servico = $this->criarServico();

        Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-04',
            'horario' => '08:00',
            'status' => 'confirmado',
        ]);

        $esperado = $this->horariosEsperados('2026-09-04', ['08:00']);

        $this->actingAs($user)
            ->getJson(route('agendamentos.horarios-disponiveis', ['data' => '2026-09-04']))
            ->assertOk()
            ->assertJson(['horarios' => $esperado]);
    }

    public function test_endpoint_de_horarios_disponiveis_ignora_o_proprio_agendamento_ao_editar(): void
    {
        $user = User::factory()->create();
        $cliente = $this->criarCliente();
        $servico = $this->criarServico();

        $agendamento = Agendamento::create([
            'cliente_id' => $cliente->id,
            'servico_id' => $servico->id,
            'data' => '2026-09-04',
            'horario' => '08:00',
            'status' => 'confirmado',
        ]);

        $this->actingAs($user)
            ->getJson(route('agendamentos.horarios-disponiveis', [
                'data' => '2026-09-04',
                'agendamento_id' => $agendamento->id,
            ]))
            ->assertOk()
            ->assertJson(['horarios' => $this->horariosEsperados('2026-09-04', [])]);
    }

    public function test_endpoint_de_horarios_disponiveis_retorna_vazio_no_fim_de_semana(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('agendamentos.horarios-disponiveis', ['data' => '2026-09-06']))
            ->assertOk()
            ->assertJson(['horarios' => []]);
    }

    private function criarCliente(): Cliente
    {
        return Cliente::create([
            'nome_completo' => 'Cliente de Teste',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
            'email' => 'cliente@example.com',
        ]);
    }

    private function criarServico(): Servico
    {
        return Servico::create([
            'nome' => 'Corte',
            'duracao_minutos' => 30,
            'preco' => 50,
        ]);
    }

    /**
     * @param  list<string>  $ocupados
     * @return list<string>
     */
    private function horariosEsperados(string $data, array $ocupados): array
    {
        $horarios = [];
        $atual = Carbon::parse($data)->setTime(8, 0);
        $fim = Carbon::parse($data)->setTime(17, 0);

        while ($atual < $fim) {
            $hora = $atual->format('H:i');

            if (! in_array($hora, $ocupados, true)) {
                $horarios[] = $hora;
            }

            $atual->addMinutes(30);
        }

        return $horarios;
    }
}
