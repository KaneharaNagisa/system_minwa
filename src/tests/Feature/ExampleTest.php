<?php

namespace Tests\Feature;

use App\Models\Municipality;
use App\Models\Story;
use App\Models\User;
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

    public function test_municipality_map_displays_all_areas_and_filters_stories(): void
    {
        $municipality = Municipality::create([
            'name' => '長野市',
            'region_name' => '北信',
        ]);
        $story = Story::create([
            'municipality_id' => $municipality->id,
            'title' => '地図選択テスト',
            'body_markdown' => '民話本文',
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('全77市町村')
            ->assertSee('長野市、登録民話1編');
        $this->assertSame(77, substr_count($response->getContent(), 'class="nagano-map-area'));

        $this->get('/?municipality='.$municipality->id)
            ->assertOk()
            ->assertSee('地図選択テスト')
            ->assertSee('長野市、登録民話1編、選択中');

        $this->get('/?municipality=area-20204')
            ->assertOk()
            ->assertDontSee('地図選択テスト')
            ->assertSee('岡谷市、登録民話0編、選択中');
    }

    public function test_admin_shows_login_for_guests(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertSee('管理者ログイン');
    }

    public function test_admin_shows_dashboard_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('管理概要');
    }
}
