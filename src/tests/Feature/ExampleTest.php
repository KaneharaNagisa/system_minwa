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

    public function test_registered_story_count_does_not_change_with_search_filters(): void
    {
        $municipality = Municipality::create([
            'name' => '長野市',
            'region_name' => '北信',
        ]);
        Story::create([
            'municipality_id' => $municipality->id,
            'title' => '検索対象の民話',
            'body_markdown' => '本文',
        ]);
        Story::create([
            'municipality_id' => $municipality->id,
            'title' => 'もう一つの民話',
            'body_markdown' => '本文',
        ]);
        Story::create([
            'municipality_id' => $municipality->id,
            'title' => '削除済みの民話',
            'body_markdown' => '本文',
        ])->delete();

        $this->get('/?q=検索対象')
            ->assertOk()
            ->assertViewHas('registeredStoryCount', 2);

        $this->get('/?q=該当なし')
            ->assertOk()
            ->assertViewHas('registeredStoryCount', 2);
    }

    public function test_prefecture_wide_story_button_filters_the_map(): void
    {
        $prefectureWide = Municipality::create([
            'name' => '長野県全域',
            'region_name' => '全域',
        ]);
        Story::create([
            'municipality_id' => $prefectureWide->id,
            'title' => '県全域の民話',
            'body_markdown' => '本文',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('長野県全域、登録民話1編')
            ->assertSee('県全域の民話');

        $this->get('/?municipality='.$prefectureWide->id)
            ->assertOk()
            ->assertSee('県全域の民話')
            ->assertSee('長野県全域、登録民話1編')
            ->assertSee('aria-current="page"', false);
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
