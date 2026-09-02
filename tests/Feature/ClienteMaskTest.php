<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteMaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_store_and_update_normalize_cpf_and_telefone(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('clientes.store'), [
            'nome_completo' => 'Cliente de Teste',
            'cpf' => '123.456.789-01',
            'telefone' => '(11) 98888-7777',
            'email' => 'cliente@example.com',
            'data_nascimento' => '1990-01-01',
            'observacoes' => 'Cadastro inicial',
        ])->assertRedirect(route('clientes.index'));

        $cliente = Cliente::where('cpf', '12345678901')->firstOrFail();

        $this->assertSame('11988887777', $cliente->telefone);

        $this->actingAs($user)->put(route('clientes.update', $cliente), [
            'nome_completo' => 'Cliente de Teste Atualizado',
            'cpf' => '987.654.321-00',
            'telefone' => '(21) 97777-6666',
            'email' => 'cliente.atualizado@example.com',
            'data_nascimento' => '1991-02-02',
            'observacoes' => 'Cadastro atualizado',
        ])->assertRedirect(route('clientes.index'));

        $cliente->refresh();

        $this->assertSame('98765432100', $cliente->cpf);
        $this->assertSame('21977776666', $cliente->telefone);
    }
}
