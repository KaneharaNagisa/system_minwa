<?php

namespace Tests\Feature;

use App\Models\Municipality;
use App\Models\Story;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_management_pages_require_authentication(): void
    {
        $this->get(route('admin.stories.index'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.municipalities.index'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.tags.index'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_user_can_open_each_management_screen(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.stories.index'))
            ->assertOk()
            ->assertSee('民話一覧')
            ->assertSee('タグ一括変更')
            ->assertSee('選択した民話にタグを付与');
        $this->get(route('admin.stories.create'))->assertOk()->assertSee('本文（Markdown）');
        $this->get(route('admin.municipalities.index'))->assertOk()->assertSee('市町村一覧');
        $this->get(route('admin.municipalities.create'))->assertOk()->assertSee('地域名');
        $this->get(route('admin.tags.index'))->assertOk()->assertSee('テーマタグ一覧');
        $this->get(route('admin.tags.create'))->assertOk()->assertSee('タグ名');
    }

    public function test_stories_can_be_created_searched_and_soft_deleted(): void
    {
        $this->actingAs(User::factory()->create());
        $municipality = Municipality::create(['name' => '長野市', 'region_name' => '北信']);
        $tag = Tag::create(['name' => '山の話']);

        $this->post(route('admin.stories.store'), [
            'title' => '山の民話',
            'title_kana' => 'やまのみんわ',
            'municipality_id' => $municipality->id,
            'body_markdown' => '## 本文',
            'tag_ids' => [$tag->id],
            'source_citation_checked' => '1',
        ])->assertRedirect(route('admin.stories.index'));

        $story = Story::firstOrFail();
        $this->assertSame([$tag->id], $story->tags()->pluck('tags.id')->all());
        $this->assertTrue($story->source_citation_checked);
        $this->get(route('admin.stories.index', ['q' => '山の', 'municipality_id' => $municipality->id]))
            ->assertOk()
            ->assertSee('山の民話');
        $this->get(route('admin.stories.index', ['q' => '該当なし']))
            ->assertOk()
            ->assertDontSee('山の民話');

        $this->delete(route('admin.stories.destroy', $story))->assertRedirect(route('admin.stories.index'));
        $this->assertSoftDeleted('stories', ['id' => $story->id]);
        $this->get('/')->assertDontSee('山の民話');
    }

    public function test_related_site_url_is_public_and_admin_memo_is_private(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.stories.store'), [
            'title' => '関連情報の民話',
            'body_markdown' => '本文',
            'related_site_url' => 'https://example.com/minwa',
            'admin_memo' => "## 確認メモ\n\n出典を確認する",
        ])->assertRedirect(route('admin.stories.index'));

        $story = Story::firstOrFail();
        $this->assertSame('https://example.com/minwa', $story->related_site_url);
        $this->assertSame("## 確認メモ\n\n出典を確認する", $story->admin_memo);

        $this->get(route('admin.stories.edit', $story))
            ->assertOk()
            ->assertSee('関連サイトURL')
            ->assertSee('管理者メモ（Markdown）')
            ->assertSee('確認メモ');

        $this->get(route('stories.show', $story))
            ->assertOk()
            ->assertSee('https://example.com/minwa')
            ->assertDontSee('確認メモ');
    }

    public function test_story_list_sorts_and_paginates_at_fifteen_records(): void
    {
        $this->actingAs(User::factory()->create());

        for ($index = 15; $index >= 0; $index--) {
            Story::create([
                'title' => sprintf('一覧テスト%02d', $index),
                'body_markdown' => '本文',
            ]);
        }

        $response = $this->get(route('admin.stories.index', ['sort' => 'title', 'direction' => 'asc']))
            ->assertOk()
            ->assertSee('一覧テスト00')
            ->assertSee('一覧テスト14')
            ->assertDontSee('一覧テスト15')
            ->assertSee('page=2');

        $this->assertLessThan(
            strpos($response->getContent(), '一覧テスト14'),
            strpos($response->getContent(), '一覧テスト00'),
        );
    }

    public function test_multiple_stories_can_receive_tags_in_bulk(): void
    {
        $this->actingAs(User::factory()->create());
        $tag = Tag::create(['name' => '山の話']);
        $otherTag = Tag::create(['name' => '伝説']);
        $first = Story::create(['title' => '民話A', 'body_markdown' => '本文A']);
        $second = Story::create(['title' => '民話B', 'body_markdown' => '本文B']);
        $first->tags()->attach($otherTag);

        $this->post(route('admin.stories.bulk-tags'), [
            'story_ids' => [$first->id, $second->id],
            'tag_ids' => [$tag->id],
        ])->assertRedirect(route('admin.stories.index'))
            ->assertSessionHas('status', '2件の民話にタグを付けました。');

        $this->assertEqualsCanonicalizing(
            [$otherTag->id, $tag->id],
            $first->tags()->pluck('tags.id')->all(),
        );
        $this->assertSame([$tag->id], $second->tags()->pluck('tags.id')->all());
    }

    public function test_story_form_uploads_main_and_inline_images(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('public');

        $this->post(route('admin.stories.store'), [
            'title' => '挿絵つきの民話',
            'body_markdown' => '本文',
            'main_image' => UploadedFile::fake()->image('cover.jpg'),
            'body_images' => [UploadedFile::fake()->image('illustration.jpg')],
        ])->assertRedirect(route('admin.stories.index'));

        $story = Story::firstOrFail();
        $this->assertStringStartsWith('stories/', $story->main_image_path);
        $this->assertStringContainsString('![挿絵](/storage/stories/', $story->body_markdown);
        Storage::disk('public')->assertExists($story->main_image_path);
        $this->assertSame(1, preg_match('/!\[挿絵\]\(\/storage\/([^)]+)\)/', $story->body_markdown, $matches));
        Storage::disk('public')->assertExists($matches[1]);
    }

    public function test_ocr_endpoint_processes_multiple_images_and_returns_text(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson(route('admin.stories.ocr'), [
            'images' => [
                UploadedFile::fake()->image('page-one.png'),
                UploadedFile::fake()->image('page-two.png'),
            ],
        ])->assertOk()->assertJsonStructure(['text']);

        $tooManyImages = [];
        for ($index = 0; $index < 11; $index++) {
            $tooManyImages[] = UploadedFile::fake()->image("page-{$index}.png");
        }

        $this->postJson(route('admin.stories.ocr'), ['images' => $tooManyImages])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('images');
    }

    public function test_municipalities_and_tags_can_be_managed(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.municipalities.store'), ['name' => '松本市', 'region_name' => '中信'])
            ->assertRedirect(route('admin.municipalities.index'));
        $municipality = Municipality::where('name', '松本市')->firstOrFail();
        $this->put(route('admin.municipalities.update', $municipality), ['name' => '新松本市', 'region_name' => '中信'])
            ->assertRedirect(route('admin.municipalities.index'));
        $this->assertDatabaseHas('municipalities', ['id' => $municipality->id, 'name' => '新松本市']);

        $this->post(route('admin.tags.store'), ['name' => '伝説'])
            ->assertRedirect(route('admin.tags.index'));
        $tag = Tag::where('name', '伝説')->firstOrFail();
        $this->put(route('admin.tags.update', $tag), ['name' => '昔話'])
            ->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => '昔話']);

        $this->delete(route('admin.municipalities.destroy', $municipality))->assertRedirect(route('admin.municipalities.index'));
        $this->delete(route('admin.tags.destroy', $tag))->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseMissing('municipalities', ['id' => $municipality->id]);
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
