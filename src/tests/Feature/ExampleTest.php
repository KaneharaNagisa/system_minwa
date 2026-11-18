<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_admin_shows_login_for_guests(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertSee('管理者ログイン');
    }

    public function test_admin_shows_dashboard_for_authenticated_users(): void
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('管理概要');
    }
}
