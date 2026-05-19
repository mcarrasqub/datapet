<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout_and_session_ends(): void
    {
        $user = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_user_cannot_access_protected_routes_after_logout(): void
    {
        $user = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($user)->post('/logout');

        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
