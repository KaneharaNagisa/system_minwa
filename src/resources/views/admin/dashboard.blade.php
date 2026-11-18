<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理画面ダッシュボード | 長野県民話アーカイブ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell">
<aside class="admin-sidebar" id="admin-navigation" data-admin-sidebar>
    <div class="admin-brand">
        <div class="admin-brand-mark">民話</div>
        <div class="admin-brand-text">
            <strong>民話アーカイブ</strong>
            <small>ADMIN PANEL</small>
        </div>
    </div>
    <nav class="admin-nav" aria-label="管理メニュー">
        <a class="is-active" href="{{ route('admin.dashboard') }}"><span class="admin-nav-icon">⌂</span>ダッシュボード</a>
        <a href="#"><span class="admin-nav-icon">▤</span>民話管理</a>
        <a href="#"><span class="admin-nav-icon">◉</span>市町村管理</a>
        <a href="#"><span class="admin-nav-icon">◇</span>タグ管理</a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-nav" style="width:100%;border:0;background:transparent;text-align:left;cursor:pointer"><span class="admin-nav-icon">→</span>ログアウト</button>
        </form>
    </nav>
</aside>
<div class="admin-sidebar-backdrop" data-admin-sidebar-backdrop></div>

<main class="admin-main">
    <header class="admin-topbar">
        <div class="admin-topbar-left">
            <button class="admin-menu-toggle" type="button" data-admin-sidebar-toggle aria-controls="admin-navigation" aria-expanded="false" aria-label="メニューを開く">
                <span aria-hidden="true">☰</span>
            </button>
            <div class="admin-topbar-title">ダッシュボード</div>
        </div>
        <div class="admin-topbar-user">{{ auth()->user()->name }} 様</div>
    </header>

    <div class="admin-content">
        <h1 class="admin-page-title">管理概要</h1>
        <section class="admin-stats" aria-label="登録件数">
            <article class="admin-stat">
                <div class="admin-stat-label">登録民話</div>
                <div class="admin-stat-value">{{ number_format($storyCount) }}<span style="font-size:.875rem;font-weight:400"> 編</span></div>
            </article>
            <article class="admin-stat">
                <div class="admin-stat-label">市町村</div>
                <div class="admin-stat-value">{{ number_format($municipalityCount) }}<span style="font-size:.875rem;font-weight:400"> 地域</span></div>
            </article>
            <article class="admin-stat">
                <div class="admin-stat-label">テーマタグ</div>
                <div class="admin-stat-value">{{ number_format($tagCount) }}<span style="font-size:.875rem;font-weight:400"> 件</span></div>
            </article>
        </section>

        <section class="admin-card">
            <header class="admin-card-header">
                <h2>最近の民話</h2>
            </header>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>タイトル</th>
                            <th>市町村</th>
                            <th>更新日</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stories as $story)
                            <tr>
                                <td>{{ $story->title }}</td>
                                <td>{{ $story->municipality?->name ?? '未設定' }}</td>
                                <td>{{ $story->updated_at->format('Y/m/d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">登録された民話はありません。</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($stories->hasPages())
                <div class="admin-pagination">{{ $stories->links() }}</div>
            @endif
        </section>
    </div>
</main>
</body>
</html>
