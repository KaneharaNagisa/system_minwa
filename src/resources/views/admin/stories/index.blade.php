@extends('admin.layout')

@section('title', '民話管理')

@section('content')
    <div class="admin-page-heading">
        <h1 class="admin-page-title">民話一覧</h1>
        <div class="admin-page-actions">
            <button class="admin-btn-secondary admin-btn-inline" type="button" data-open-bulk-tags>タグ一括変更</button>
            <a class="admin-btn-primary admin-btn-inline" href="{{ route('admin.stories.create') }}">民話を登録</a>
        </div>
    </div>

    <section class="admin-card">
        <form class="admin-filter" method="GET" action="{{ route('admin.stories.index') }}">
            <div class="admin-filter-field">
                <label class="admin-form-label" for="story-search">タイトル</label>
                <input class="admin-form-control" id="story-search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="タイトルで検索">
            </div>
            <div class="admin-filter-field">
                <label class="admin-form-label" for="municipality-filter">市町村</label>
                <select class="admin-form-control" id="municipality-filter" name="municipality_id" data-searchable-select data-search-placeholder="市町村を検索">
                    <option value="">すべて</option>
                    @foreach ($municipalities as $municipality)
                        <option value="{{ $municipality->id }}" @selected(($filters['municipality_id'] ?? '') == $municipality->id)>{{ $municipality->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-filter-field">
                <label class="admin-form-label" for="story-sort">並び替え</label>
                <select class="admin-form-control" id="story-sort" name="sort">
                    <option value="updated_at" @selected(($filters['sort'] ?? 'updated_at') === 'updated_at')>更新日</option>
                    <option value="title" @selected(($filters['sort'] ?? '') === 'title')>タイトル</option>
                    <option value="municipality" @selected(($filters['sort'] ?? '') === 'municipality')>市町村</option>
                </select>
            </div>
            <div class="admin-filter-field">
                <label class="admin-form-label" for="story-direction">順序</label>
                <select class="admin-form-control" id="story-direction" name="direction">
                    <option value="desc" @selected(($filters['direction'] ?? 'desc') === 'desc')>降順</option>
                    <option value="asc" @selected(($filters['direction'] ?? '') === 'asc')>昇順</option>
                </select>
            </div>
            <div class="admin-filter-actions">
                <button class="admin-btn-primary admin-btn-inline" type="submit">検索</button>
                <a class="admin-btn-secondary" href="{{ route('admin.stories.index') }}">解除</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" aria-label="すべての民話を選択" data-select-all-stories></th>
                        <th>タイトル</th><th>市町村</th><th>テーマタグ</th><th>更新日</th><th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stories as $story)
                        <tr>
                            <td><input type="checkbox" name="story_ids[]" value="{{ $story->id }}" form="bulk-tags-form" aria-label="{{ $story->title }}を選択" data-story-checkbox></td>
                            <td>{{ $story->title }}</td>
                            <td>{{ $story->municipality?->name ?? '未設定' }}</td>
                            <td>{{ $story->tags->pluck('name')->join('、') ?: '—' }}</td>
                            <td>{{ $story->updated_at->format('Y/m/d') }}</td>
                            <td class="admin-row-actions">
                                <a href="{{ route('admin.stories.edit', $story) }}">編集</a>
                                <form method="POST" action="{{ route('admin.stories.destroy', $story) }}" onsubmit="return confirm('この民話を削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="admin-link-danger" type="submit">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="admin-empty">条件に一致する民話はありません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($stories->hasPages())
            <div class="admin-pagination">{{ $stories->links() }}</div>
        @endif
    </section>

        <dialog class="admin-bulk-dialog" data-bulk-tags-dialog>
            <form id="bulk-tags-form" method="POST" action="{{ route('admin.stories.bulk-tags') }}">
                @csrf
                <div class="admin-bulk-dialog-heading">
                    <h2>選択した民話にタグを付与</h2>
                    <button class="admin-dialog-close" type="button" data-close-bulk-tags aria-label="閉じる">×</button>
                </div>
                <p class="admin-bulk-selected-count" data-bulk-selected-count>選択した民話：0件</p>
                <label class="admin-form-label" for="bulk-tag-select">付与するタグ</label>
                <select id="bulk-tag-select" class="admin-form-control admin-bulk-tag-select" name="tag_ids[]" multiple size="6" aria-label="付与するタグ">
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
                <div class="admin-bulk-dialog-actions">
                    <button class="admin-btn-secondary" type="button" data-close-bulk-tags>キャンセル</button>
                    <button class="admin-btn-primary" type="submit">選択した民話にタグを付ける</button>
                </div>
            </form>
        </dialog>
@endsection
