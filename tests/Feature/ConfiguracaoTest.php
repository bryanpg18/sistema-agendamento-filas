<?php

namespace Tests\Feature;

use App\Models\Configuracao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfiguracaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_configuracoes_page(): void
    {
        $response = $this->get(route('configuracoes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_configuracoes(): void
    {
        $response = $this->put(route('configuracoes.update'), [
            'nome_estabelecimento' => 'Barbearia',
            'telefone' => '11988887777',
            'email' => 'contato@teste.com',
            'horario_abertura' => '08:00',
            'horario_fechamento' => '18:00',
            'duracao_padrao' => 30,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_configuracoes_screen(): void
    {
        $user = User::factory()->create();

        Configuracao::create([
            'nome_estabelecimento' => 'Barbearia Central',
            'telefone' => '11988887777',
            'email' => 'contato@barbearia.com',
            'horario_abertura' => '09:00',
            'horario_fechamento' => '19:00',
            'duracao_padrao' => 45,
        ]);

        $response = $this->actingAs($user)->get(route('configuracoes.index'));

        $response->assertOk()
            ->assertViewIs('configuracoes.index')
            ->assertSee('Barbearia Central')
            ->assertSee('11988887777')
            ->assertSee('contato@barbearia.com')
            ->assertSee('09:00')
            ->assertSee('19:00');
    }

    public function test_authenticated_user_can_update_configuracoes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('configuracoes.update'), [
            'nome_estabelecimento' => 'Clínica Saúde',
            'telefone' => '11999998888',
            'email' => 'clinica@saude.com',
            'horario_abertura' => '07:30',
            'horario_fechamento' => '17:30',
            'duracao_padrao' => 40,
        ]);

        $response->assertRedirect(route('configuracoes.index'))
            ->assertSessionHas('sucesso', 'Configurações salvas com sucesso!');

        $this->assertDatabaseHas('configuracoes', [
            'nome_estabelecimento' => 'Clínica Saúde',
            'telefone' => '11999998888',
            'email' => 'clinica@saude.com',
            'horario_abertura' => '07:30',
            'horario_fechamento' => '17:30',
            'duracao_padrao' => 40,
        ]);
    }

    public function test_validation_rules_require_mandatory_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('configuracoes.index'))->put(route('configuracoes.update'), [
            'nome_estabelecimento' => '',
            'telefone' => '',
            'email' => 'email-invalido',
            'horario_abertura' => '18:00',
            'horario_fechamento' => '08:00', // fechamento anterior à abertura
            'duracao_padrao' => 0, // menor que 5
        ]);

        $response->assertRedirect(route('configuracoes.index'));
        $response->assertSessionHasErrors([
            'nome_estabelecimento',
            'telefone',
            'email',
            'horario_fechamento',
            'duracao_padrao',
        ]);
    }
}
