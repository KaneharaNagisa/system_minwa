@extends('admin.layout')

@section('title', 'タグ管理')

@section('content')
    <div class="admin-page-heading">
        <h1 class="admin-page-title">テーマタグ一覧</h1>
        <a class="admin-btn-primary admin-btn-inline" href="{{ route('admin.tags.create') }}">タグを登録</a>
    </div>
    <section class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>タグ名</th><th>利用民話数</th><th>操作</th></tr>
                </thead>
                <tbody>
                    @forelse ($tags as $tag)
                        <tr>
                            <td>{{ $tag->name }}</td>
                            <td>{{ number_format($tag->stories_count) }} 編</td>
                            <td class="admin-row-actions">
                                <a href="{{ route('admin.tags.edit', $tag) }}">編集</a>
                                <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('このタグを削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="admin-link-danger" type="submit">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="admin-empty">タグが登録されていません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tags->hasPages())
            <div class="admin-pagination">{{ $tags->links() }}</div>
        @endif
    </section>
@endsection
