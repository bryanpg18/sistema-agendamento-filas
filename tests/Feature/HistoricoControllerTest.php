<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_history(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('historico.index'))
            ->assertOk()
            ->assertSee('Histórico');
    }
}
