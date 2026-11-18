<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理者ログイン | 長野県民話アーカイブ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-login-body">
<main class="admin-login-shell">
    <section class="admin-login-card" aria-labelledby="login-title">
        <header class="admin-login-heading">
            <div class="admin-brand-mark" style="margin-inline:auto">民話</div>
            <h1 id="login-title">管理者ログイン</h1>
            <p>長野県民話アーカイブ 管理画面</p>
        </header>

        <form class="admin-login-form" method="POST" action="{{ route('admin.login') }}">
            @csrf
            @if ($errors->any())
                <div class="admin-error" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="admin-form-group">
                <label class="admin-form-label" for="email">メールアドレス</label>
                <input class="admin-form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label" for="password">パスワード</label>
                <input class="admin-form-control" id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-check">
                    <input type="checkbox" name="remember" value="1">
                    ログイン状態を保持する
                </label>
            </div>
            <button class="admin-btn-primary" type="submit">ログイン</button>
        </form>
    </section>
</main>
</body>
</html>
