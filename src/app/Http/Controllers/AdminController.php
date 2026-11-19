<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        if (! Auth::check()) {
            return view('admin.login');
        }

        $stories = Story::query()
            ->with(['municipality', 'tags'])
            ->latest('updated_at')
            ->paginate(15);

        return view('admin.dashboard', [
            'stories' => $stories,
            'storyCount' => Story::count(),
            'municipalityCount' => Municipality::count(),
            'tagCount' => Tag::count(),
        ]);
    }

    public function stories(Request $request): View
    {
        $sort = $request->input('sort', 'updated_at');
        $direction = $request->input('direction', 'desc');
        $sort = in_array($sort, ['title', 'municipality', 'updated_at'], true) ? $sort : 'updated_at';
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        $stories = Story::query()
            ->with(['municipality', 'tags'])
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q')->trim().'%'))
            ->when($request->filled('municipality_id'), fn ($query) => $query->where('municipality_id', $request->integer('municipality_id')))
            ->when($sort === 'municipality', fn ($query) => $query->orderBy(
                Municipality::query()->select('name')->whereColumn('municipalities.id', 'stories.municipality_id'),
                $direction,
            ))
            ->when($sort !== 'municipality', fn ($query) => $query->orderBy($sort, $direction))
            ->paginate(15)
            ->withQueryString();

        return view('admin.stories.index', [
            'stories' => $stories,
            'municipalities' => Municipality::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
            'filters' => $request->only(['q', 'municipality_id', 'sort', 'direction']),
        ]);
    }

    public function bulkTagStories(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'story_ids' => ['required', 'array', 'min:1'],
            'story_ids.*' => ['integer', 'distinct', 'exists:stories,id'],
            'tag_ids' => ['required', 'array', 'min:1'],
            'tag_ids.*' => ['integer', 'distinct', 'exists:tags,id'],
        ], [
            'story_ids.required' => 'タグを付ける民話を選択してください。',
            'story_ids.min' => 'タグを付ける民話を選択してください。',
            'tag_ids.required' => '付けるタグを選択してください。',
            'tag_ids.min' => '付けるタグを選択してください。',
        ]);

        DB::transaction(function () use ($validated) {
            foreach (Story::query()->whereKey($validated['story_ids'])->get() as $story) {
                $story->tags()->syncWithoutDetaching($validated['tag_ids']);
            }
        });

        $count = count(array_unique($validated['story_ids']));

        return redirect()->route('admin.stories.index')
            ->with('status', "{$count}件の民話にタグを付けました。");
    }

    public function createStory(): View
    {
        return view('admin.stories.form', $this->storyFormData(new Story));
    }

    public function ocrImages(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['required', 'image', 'max:10240'],
        ], [
            'images.required' => 'OCRする画像を選択してください。',
            'images.array' => '画像を選択してください。',
            'images.max' => '画像は一度に10枚まで選択できます。',
            'images.*.image' => '画像ファイルを選択してください。',
            'images.*.max' => '画像は1枚あたり10MB以下にしてください。',
        ]);
        $executable = (new ExecutableFinder)->find('tesseract');

        if ($executable === null) {
            return response()->json([
                'message' => 'OCR機能を利用できません。サーバーに日本語OCR環境が設定されていません。',
            ], 503);
        }

        $recognizedTexts = [];

        foreach ($validated['images'] as $image) {
            $process = new Process([
                $executable,
                $image->getRealPath(),
                'stdout',
                '-l',
                'jpn+eng',
                '--psm',
                '3',
            ]);
            $process->setTimeout(45);
            $process->run();

            if (! $process->isSuccessful()) {
                logger()->error('Local OCR processing failed.', [
                    'exit_code' => $process->getExitCode(),
                    'error' => $process->getErrorOutput(),
                ]);

                return response()->json([
                    'message' => '画像の読み取りに失敗しました。画像を確認して再度お試しください。',
                ], 422);
            }

            $text = trim($process->getOutput());

            if ($text !== '') {
                $recognizedTexts[] = $text;
            }
        }

        return response()->json(['text' => implode("\n\n", $recognizedTexts)]);
    }

    public function storeStory(Request $request): RedirectResponse
    {
        $data = $this->validateStory($request);
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['body_images'], $data['main_image'], $data['tag_ids']);
        $data['source_citation_checked'] = $request->boolean('source_citation_checked');
        $data['expression_rewritten_checked'] = $request->boolean('expression_rewritten_checked');

        if ($request->hasFile('main_image')) {
            $data['main_image_path'] = $this->storeStoryImage($request->file('main_image'));
        }
        $this->appendBodyImages($data, $request);

        $story = Story::create($data);
        $story->tags()->sync($tagIds);

        return redirect()->route('admin.stories.index')->with('status', '民話を登録しました。');
    }

    public function editStory(Story $story): View
    {
        $story->load('tags');

        return view('admin.stories.form', $this->storyFormData($story));
    }

    public function updateStory(Request $request, Story $story): RedirectResponse
    {
        $data = $this->validateStory($request);
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['body_images'], $data['main_image'], $data['tag_ids']);
        $data['source_citation_checked'] = $request->boolean('source_citation_checked');
        $data['expression_rewritten_checked'] = $request->boolean('expression_rewritten_checked');

        if ($request->hasFile('main_image')) {
            $data['main_image_path'] = $this->storeStoryImage($request->file('main_image'));
        }
        $this->appendBodyImages($data, $request);

        $story->update($data);
        $story->tags()->sync($tagIds);

        return redirect()->route('admin.stories.index')->with('status', '民話を更新しました。');
    }

    public function destroyStory(Story $story): RedirectResponse
    {
        $story->delete();

        return redirect()->route('admin.stories.index')->with('status', '民話を削除しました。');
    }

    public function municipalities(): View
    {
        $municipalities = Municipality::query()
            ->withCount('stories')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.municipalities.index', compact('municipalities'));
    }

    public function createMunicipality(): View
    {
        return view('admin.municipalities.form', ['municipality' => new Municipality]);
    }

    public function storeMunicipality(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:municipalities,name'],
            'region_name' => ['required', 'string', 'max:255'],
        ]);
        Municipality::create($data);

        return redirect()->route('admin.municipalities.index')->with('status', '市町村を登録しました。');
    }

    public function editMunicipality(Municipality $municipality): View
    {
        return view('admin.municipalities.form', compact('municipality'));
    }

    public function updateMunicipality(Request $request, Municipality $municipality): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('municipalities', 'name')->ignore($municipality)],
            'region_name' => ['required', 'string', 'max:255'],
        ]);
        $municipality->update($data);

        return redirect()->route('admin.municipalities.index')->with('status', '市町村を更新しました。');
    }

    public function destroyMunicipality(Municipality $municipality): RedirectResponse
    {
        $municipality->delete();

        return redirect()->route('admin.municipalities.index')->with('status', '市町村を削除しました。');
    }

    public function tags(): View
    {
        $tags = Tag::query()->withCount('stories')->orderBy('name')->paginate(15);

        return view('admin.tags.index', compact('tags'));
    }

    public function createTag(): View
    {
        return view('admin.tags.form', ['tag' => new Tag]);
    }

    public function storeTag(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name'],
        ]);
        Tag::create($data);

        return redirect()->route('admin.tags.index')->with('status', 'タグを登録しました。');
    }

    public function editTag(Tag $tag): View
    {
        return view('admin.tags.form', compact('tag'));
    }

    public function updateTag(Request $request, Tag $tag): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('tags', 'name')->ignore($tag)],
        ]);
        $tag->update($data);

        return redirect()->route('admin.tags.index')->with('status', 'タグを更新しました。');
    }

    public function destroyTag(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('status', 'タグを削除しました。');
    }

    private function validateStory(Request $request): array
    {
        return $request->validate([
            'municipality_id' => ['nullable', 'integer', 'exists:municipalities,id'],
            'title' => ['required', 'string', 'max:255'],
            'title_kana' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'body_markdown' => ['required', 'string'],
            'main_image_path' => ['nullable', 'string', 'max:2048'],
            'main_image' => ['nullable', 'image', 'max:10240'],
            'body_images' => ['nullable', 'array'],
            'body_images.*' => ['image', 'max:10240'],
            'spot_name' => ['nullable', 'string', 'max:255'],
            'spot_address' => ['nullable', 'string'],
            'spot_map_url' => ['nullable', 'url', 'max:2048'],
            'source' => ['nullable', 'string'],
            'source_citation_checked' => ['sometimes', 'boolean'],
            'expression_rewritten_checked' => ['sometimes', 'boolean'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);
    }

    private function storyFormData(Story $story): array
    {
        return [
            'story' => $story,
            'municipalities' => Municipality::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
        ];
    }

    private function appendBodyImages(array &$data, Request $request): void
    {
        $images = [];

        foreach ($request->file('body_images', []) as $image) {
            $path = $this->storeStoryImage($image);
            $images[] = '![挿絵](/storage/'.$path.')';
        }

        if ($images !== []) {
            $data['body_markdown'] = rtrim($data['body_markdown'])."\n\n".implode("\n\n", $images);
        }
    }

    private function storeStoryImage(UploadedFile $image): string
    {
        $path = $image->storePublicly('stories', 'public');

        if ($path === false) {
            throw new RuntimeException('画像を保存できませんでした。');
        }

        return $path;
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "{$seconds}秒後に再試行してください。",
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。']);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.dashboard');
    }
}
