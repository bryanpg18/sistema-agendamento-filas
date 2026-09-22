<?php

namespace Tests\Feature;

use App\Models\Servico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_services(): void
    {
        $this->get(route('servicos.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_service(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('servicos.store'), [
                'nome' => 'Corte de cabelo',
                'duracao_minutos' => 30,
                'preco' => '45.00',
                'ativo' => '1',
            ])
            ->assertRedirect(route('servicos.index'))
            ->assertSessionHas('sucesso', 'Serviço cadastrado com sucesso!');

        $this->assertDatabaseHas('servicos', [
            'nome' => 'Corte de cabelo',
            'duracao_minutos' => 30,
            'preco' => 45,
            'ativo' => true,
        ]);
    }

    public function test_service_requires_name_and_duration(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('servicos.create'))
            ->post(route('servicos.store'), [
                'nome' => '',
                'duracao_minutos' => '',
                'ativo' => '1',
            ])
            ->assertRedirect(route('servicos.create'))
            ->assertSessionHasErrors([
                'nome' => 'Informe o nome do serviço.',
                'duracao_minutos' => 'Informe a duração do serviço.',
            ]);
    }

    public function test_authenticated_user_can_deactivate_a_service(): void
    {
        $user = User::factory()->create();
        $servico = Servico::create([
            'nome' => 'Consulta',
            'duracao_minutos' => 60,
            'preco' => 120,
            'ativo' => true,
        ]);

        $this->actingAs($user)
            ->put(route('servicos.update', $servico), [
                'nome' => 'Consulta',
                'duracao_minutos' => 60,
                'preco' => '120.00',
                'ativo' => '0',
            ])
            ->assertRedirect(route('servicos.index'));

        $this->assertDatabaseHas('servicos', [
            'id' => $servico->id,
            'ativo' => false,
        ]);
    }
}
