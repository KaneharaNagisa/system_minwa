<?php

namespace Tests\Feature;

use App\Models\Municipality;
use App\Models\Story;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\MinwaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_tags_are_included_in_the_database_and_minwa_seeders(): void
    {
        $this->seed();
        $this->seed(MinwaSeeder::class);

        $this->assertDatabaseHas('tags', ['name' => 'まんが日本昔ばなし']);
        $this->assertDatabaseHas('tags', ['name' => '民話の部屋']);
        $this->assertDatabaseCount('tags', 2);

        $mangaStory = Story::where('source', 'まんが日本昔ばなし〜データベース〜（長野県）')->firstOrFail();
        $this->assertSame(
            ['まんが日本昔ばなし'],
            $mangaStory->tags()->pluck('tags.name')->all(),
        );

        $minwaStory = Story::where('source', '民話の部屋（長野県）')->firstOrFail();
        $this->assertSame(['民話の部屋'], $minwaStory->tags()->pluck('tags.name')->all());

        $storyWithBothSources = Story::where('title', 'あさこ・ゆうこ')->firstOrFail();
        $this->assertEqualsCanonicalizing(
            ['まんが日本昔ばなし', '民話の部屋'],
            $storyWithBothSources->tags()->pluck('tags.name')->all(),
        );
    }

    public function test_all_nagano_municipalities_are_available_in_story_selects(): void
    {
        $this->actingAs(User::factory()->create());
        $mapMunicipalities = json_decode(
            file_get_contents(resource_path('data/nagano-municipalities.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $this->assertCount(77, $mapMunicipalities);
        $this->assertDatabaseCount('municipalities', 78);

        $response = $this->get(route('admin.stories.create'))
            ->assertOk()
            ->assertSee('data-searchable-select', false)
            ->assertSee('data-search-placeholder="市町村を検索"', false);

        foreach ($mapMunicipalities as $municipality) {
            $response->assertSee($municipality['name']);
        }
    }

    public function test_minwa_seeder_registers_manga_stories_with_bodies_and_source_links_without_duplicates(): void
    {
        $this->seed(MinwaSeeder::class);
        $this->seed(MinwaSeeder::class);

        $this->assertDatabaseCount('stories', 67);

        $stories = Story::where('source', 'like', '%まんが日本昔ばなし〜データベース〜（長野県）%')->get();
        $this->assertCount(40, $stories);
        $this->assertCount(40, $stories->pluck('related_site_url')->unique());
        foreach ($stories as $story) {
            $this->assertNotEmpty($story->body_markdown);
            $this->assertStringStartsWith('http://nihon.syoukoukai.com/modules/stories/index.php?lid=', $story->related_site_url);
        }

        $sharedStory = $stories->firstWhere('title', 'あさこ・ゆうこ');
        $this->assertNotNull($sharedStory);
        $this->assertStringContainsString('https://minwanoheya.jp/area/nagano_009/', $sharedStory->body_markdown);
    }

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
        $this->get(route('admin.stories.create'))
            ->assertOk()
            ->assertSee('本文（Markdown）')
            ->assertDontSee('data-ocr-uploader', false)
            ->assertDontSee('OCR')
            ->assertSee('data-story-upload-form', false)
            ->assertSee('data-admin-loading', false)
            ->assertSee('data-admin-loading-message', false);
        $this->get(route('admin.municipalities.index'))->assertOk()->assertSee('市町村一覧');
        $this->get(route('admin.municipalities.create'))->assertOk()->assertSee('地域名');
        $this->get(route('admin.tags.index'))->assertOk()->assertSee('テーマタグ一覧');
        $this->get(route('admin.tags.create'))->assertOk()->assertSee('タグ名');
    }

    public function test_stories_can_be_created_searched_and_soft_deleted(): void
    {
        $this->actingAs(User::factory()->create());
        $municipality = Municipality::where('name', '長野市')->firstOrFail();
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

    public function test_stories_can_be_filtered_by_prefecture_wide_municipality(): void
    {
        $this->actingAs(User::factory()->create());
        $prefectureWide = Municipality::where('name', '長野県全域')->firstOrFail();
        $city = Municipality::where('name', '長野市')->firstOrFail();
        Story::create([
            'municipality_id' => $prefectureWide->id,
            'title' => '県全域の民話',
            'body_markdown' => '本文',
        ]);
        Story::create([
            'municipality_id' => $city->id,
            'title' => '長野市の民話',
            'body_markdown' => '本文',
        ]);

        $this->get(route('admin.stories.index', ['municipality_id' => $prefectureWide->id]))
            ->assertOk()
            ->assertSee('長野県全域')
            ->assertSee('県全域の民話')
            ->assertDontSee('長野市の民話');
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

    public function test_multiple_related_images_can_be_managed_in_the_admin_only(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('public');

        $this->post(route('admin.stories.store'), [
            'title' => '資料写真つきの民話',
            'body_markdown' => '本文',
            'related_images' => [
                UploadedFile::fake()->image('landscape.jpg'),
                UploadedFile::fake()->image('object.jpg'),
            ],
        ])->assertRedirect(route('admin.stories.index'));

        $story = Story::firstOrFail();
        $images = $story->relatedImages;
        $this->assertCount(2, $images);
        $this->assertSame(['landscape.jpg', 'object.jpg'], $images->pluck('original_name')->all());
        foreach ($images as $image) {
            Storage::disk('public')->assertExists($image->path);
        }

        $this->get(route('admin.stories.edit', $story))
            ->assertOk()
            ->assertSee('関連画像')
            ->assertSee('landscape.jpg')
            ->assertSee('object.jpg')
            ->assertSee('data-open-related-image', false)
            ->assertSee('data-related-image-dialog', false)
            ->assertSee('data-prev-related-image', false)
            ->assertSee('data-next-related-image', false)
            ->assertSee('data-related-image-count', false)
            ->assertSee('画像全体を確認できます');
        $this->get(route('stories.show', $story))
            ->assertOk()
            ->assertDontSee('関連画像')
            ->assertDontSee('landscape.jpg')
            ->assertDontSee('object.jpg');

        $deletedImage = $images->first();
        $this->put(route('admin.stories.update', $story), [
            'title' => $story->title,
            'body_markdown' => $story->body_markdown,
            'delete_related_image_ids' => [$deletedImage->id],
            'related_images' => [UploadedFile::fake()->image('new-image.jpg')],
        ])->assertRedirect(route('admin.stories.index'));

        $this->assertDatabaseMissing('story_images', ['id' => $deletedImage->id]);
        Storage::disk('public')->assertMissing($deletedImage->path);
        $this->assertSame(
            ['object.jpg', 'new-image.jpg'],
            $story->fresh()->relatedImages->pluck('original_name')->all(),
        );
    }

    public function test_municipalities_and_tags_can_be_managed(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.municipalities.store'), ['name' => 'テスト市', 'region_name' => '中信'])
            ->assertRedirect(route('admin.municipalities.index'));
        $municipality = Municipality::where('name', 'テスト市')->firstOrFail();
        $this->put(route('admin.municipalities.update', $municipality), ['name' => '新テスト市', 'region_name' => '中信'])
            ->assertRedirect(route('admin.municipalities.index'));
        $this->assertDatabaseHas('municipalities', ['id' => $municipality->id, 'name' => '新テスト市']);

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
