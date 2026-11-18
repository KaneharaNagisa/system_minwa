@extends('admin.layout')

@section('title', '民話管理')

@section('content')
    <div class="admin-page-heading">
        <h1 class="admin-page-title">民話一覧</h1>
        <a class="admin-btn-primary admin-btn-inline" href="{{ route('admin.stories.create') }}">民話を登録</a>
    </div>

    <section class="admin-card">
        <form class="admin-filter" method="GET" action="{{ route('admin.stories.index') }}">
            <div class="admin-filter-field">
                <label class="admin-form-label" for="story-search">タイトル</label>
                <input class="admin-form-control" id="story-search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="タイトルで検索">
            </div>
            <div class="admin-filter-field">
                <label class="admin-form-label" for="municipality-filter">市町村</label>
                <select class="admin-form-control" id="municipality-filter" name="municipality_id">
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
                    <tr><th>タイトル</th><th>市町村</th><th>テーマタグ</th><th>更新日</th><th>操作</th></tr>
                </thead>
                <tbody>
                    @forelse ($stories as $story)
                        <tr>
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
                        <tr><td colspan="5" class="admin-empty">条件に一致する民話はありません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($stories->hasPages())
            <div class="admin-pagination">{{ $stories->links() }}</div>
        @endif
    </section>
@endsection
