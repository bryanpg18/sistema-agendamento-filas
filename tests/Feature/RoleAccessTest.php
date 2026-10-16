<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendant_is_forbidden_from_administrative_pages(): void
    {
        $attendant = User::factory()->create(['role' => 'atendente']);

        $this->actingAs($attendant)
            ->get(route('servicos.index'))
            ->assertForbidden();

        $this->get(route('bloqueios-agenda.index'))->assertForbidden();
        $this->get(route('relatorios.index'))->assertForbidden();
        $this->get(route('configuracoes.index'))->assertForbidden();
        $this->get(route('usuarios-acesso.index'))->assertForbidden();
    }

    public function test_attendant_can_access_daily_service_pages_and_does_not_see_admin_navigation(): void
    {
        $attendant = User::factory()->create(['role' => 'atendente']);

        $this->actingAs($attendant)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Perfis de acesso')
            ->assertDontSee('Configurações');

        $this->get(route('atendimentos.index'))->assertOk();
        $this->get(route('horarios.index'))->assertOk();
    }

    public function test_attendant_cannot_delete_a_client(): void
    {
        $attendant = User::factory()->create(['role' => 'atendente']);
        $cliente = Cliente::create([
            'nome_completo' => 'Cliente Teste',
            'cpf' => '12345678901',
            'telefone' => '11999999999',
        ]);

        $this->actingAs($attendant)
            ->delete(route('clientes.destroy', $cliente))
            ->assertForbidden();

        $this->assertModelExists($cliente);
    }

    public function test_administrator_can_change_another_users_access_profile(): void
    {
        $administrator = User::factory()->create(['role' => 'admin']);
        $attendant = User::factory()->create(['role' => 'atendente']);

        $this->actingAs($administrator)
            ->get(route('usuarios-acesso.index'))
            ->assertOk()
            ->assertSee('Perfis de acesso')
            ->assertSee($attendant->email);

        $this->patch(route('usuarios-acesso.update', $attendant), ['role' => 'admin'])
            ->assertRedirect(route('usuarios-acesso.index'))
            ->assertSessionHas('sucesso', 'Perfil de acesso atualizado.');

        $this->assertDatabaseHas('users', [
            'id' => $attendant->id,
            'role' => 'admin',
        ]);
    }

    public function test_last_administrator_cannot_remove_their_own_admin_access(): void
    {
        $administrator = User::factory()->create(['role' => 'admin']);

        $this->actingAs($administrator)
            ->from(route('usuarios-acesso.index'))
            ->patch(route('usuarios-acesso.update', $administrator), ['role' => 'atendente'])
            ->assertRedirect(route('usuarios-acesso.index'))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $administrator->id,
            'role' => 'admin',
        ]);
    }

    public function test_console_command_promotes_an_existing_user_to_administrator(): void
    {
        $user = User::factory()->create(['role' => 'atendente']);

        $this->artisan('users:make-admin', ['email' => $user->email])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'admin',
        ]);
    }
}
