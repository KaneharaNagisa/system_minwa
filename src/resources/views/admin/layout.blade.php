<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | 長野県民話アーカイブ 管理画面</title>
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
        <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}"><span class="admin-nav-icon">⌂</span>ダッシュボード</a>
        <a class="{{ request()->routeIs('admin.stories.*') ? 'is-active' : '' }}" href="{{ route('admin.stories.index') }}"><span class="admin-nav-icon">▤</span>民話管理</a>
        <a class="{{ request()->routeIs('admin.municipalities.*') ? 'is-active' : '' }}" href="{{ route('admin.municipalities.index') }}"><span class="admin-nav-icon">◉</span>市町村管理</a>
        <a class="{{ request()->routeIs('admin.tags.*') ? 'is-active' : '' }}" href="{{ route('admin.tags.index') }}"><span class="admin-nav-icon">◇</span>タグ管理</a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-nav-logout"><span class="admin-nav-icon">→</span>ログアウト</button>
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
            <div class="admin-topbar-title">@yield('title')</div>
        </div>
        <div class="admin-topbar-user">{{ auth()->user()->name }} 様</div>
    </header>

    <div class="admin-content">
        @if (session('status'))
            <div class="admin-notice" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="admin-error" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        @yield('content')
    </div>
</main>
<div class="admin-loading-overlay" data-admin-loading hidden role="status" aria-live="assertive" tabindex="-1">
    <div class="admin-loading-content">
        <span class="admin-loading-spinner" aria-hidden="true"></span>
        <p class="admin-loading-message" data-admin-loading-message>処理中です。しばらくお待ちください。</p>
    </div>
</div>
</body>
</html>
