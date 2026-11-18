@extends('admin.layout')

@section('title', '市町村管理')

@section('content')
    <div class="admin-page-heading">
        <h1 class="admin-page-title">市町村一覧</h1>
        <a class="admin-btn-primary admin-btn-inline" href="{{ route('admin.municipalities.create') }}">市町村を登録</a>
    </div>
    <section class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>市町村名</th><th>地域</th><th>登録民話</th><th>操作</th></tr>
                </thead>
                <tbody>
                    @forelse ($municipalities as $municipality)
                        <tr>
                            <td>{{ $municipality->name }}</td>
                            <td>{{ $municipality->region_name }}</td>
                            <td>{{ number_format($municipality->stories_count) }} 編</td>
                            <td class="admin-row-actions">
                                <a href="{{ route('admin.municipalities.edit', $municipality) }}">編集</a>
                                <form method="POST" action="{{ route('admin.municipalities.destroy', $municipality) }}" onsubmit="return confirm('この市町村を削除しますか？関連民話の市町村は未設定になります。');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="admin-link-danger" type="submit">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="admin-empty">市町村が登録されていません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($municipalities->hasPages())
            <div class="admin-pagination">{{ $municipalities->links() }}</div>
        @endif
    </section>
@endsection
