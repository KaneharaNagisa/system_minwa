<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
            'filters' => $request->only(['q', 'municipality_id', 'sort', 'direction']),
        ]);
    }

    public function createStory(): View
    {
        return view('admin.stories.form', $this->storyFormData(new Story));
    }

    public function storeStory(Request $request): RedirectResponse
    {
        $data = $this->validateStory($request);
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);
        $data['source_citation_checked'] = $request->boolean('source_citation_checked');
        $data['expression_rewritten_checked'] = $request->boolean('expression_rewritten_checked');

        if ($request->hasFile('main_image')) {
            $data['main_image_path'] = $request->file('main_image')->storePublicly('stories', 'public');
        }

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
        unset($data['tag_ids']);
        $data['source_citation_checked'] = $request->boolean('source_citation_checked');
        $data['expression_rewritten_checked'] = $request->boolean('expression_rewritten_checked');

        if ($request->hasFile('main_image')) {
            $data['main_image_path'] = $request->file('main_image')->storePublicly('stories', 'public');
        }

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
