@extends('admin.layout')

@section('title', 'ダッシュボード')

@section('content')
    <h1 class="admin-page-title">管理概要</h1>
    <section class="admin-stats" aria-label="登録件数">
        <article class="admin-stat">
            <div class="admin-stat-label">登録民話</div>
            <div class="admin-stat-value">{{ number_format($storyCount) }}<span class="admin-stat-unit"> 編</span></div>
        </article>
        <article class="admin-stat">
            <div class="admin-stat-label">市町村</div>
            <div class="admin-stat-value">{{ number_format($municipalityCount) }}<span class="admin-stat-unit"> 地域</span></div>
        </article>
        <article class="admin-stat">
            <div class="admin-stat-label">テーマタグ</div>
            <div class="admin-stat-value">{{ number_format($tagCount) }}<span class="admin-stat-unit"> 件</span></div>
        </article>
    </section>

    <section class="admin-card">
        <header class="admin-card-header">
            <h2>最近更新した民話</h2>
            <a class="admin-btn-secondary" href="{{ route('admin.stories.index') }}">民話一覧へ</a>
        </header>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>タイトル</th><th>市町村</th><th>更新日</th><th>操作</th></tr>
                </thead>
                <tbody>
                    @forelse ($stories as $story)
                        <tr>
                            <td>{{ $story->title }}</td>
                            <td>{{ $story->municipality?->name ?? '未設定' }}</td>
                            <td>{{ $story->updated_at->format('Y/m/d') }}</td>
                            <td><a href="{{ route('admin.stories.edit', $story) }}">編集</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="admin-empty">登録された民話はありません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($stories->hasPages())
            <div class="admin-pagination">{{ $stories->links() }}</div>
        @endif
    </section>
@endsection
