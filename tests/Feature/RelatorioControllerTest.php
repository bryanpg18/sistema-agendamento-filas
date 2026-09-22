<?php

namespace Tests\Feature;

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
}
