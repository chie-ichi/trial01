<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('title')
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a href="/" class="header__logo">Atte</a>
            @if (Auth::check())
            <nav class="header__nav">
                <ul class="header__nav-list">
                    <li class="header__nav-list-item">
                        <a href="/" class="header__nav-link">ホーム</a>
                    </li>
                    <li class="header__nav-list-item">
                        <a href="/attendance" class="header__nav-link">日付一覧</a>
                    </li>
                    <li class="header__nav-list-item">
                        <form action="/logout" method="post">
                            @csrf
                            <button class="header__nav-button">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
            @endif
        </div>
    </header>

    <main class="main-contents">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer__inner">
            <small class="footer__copyright">Atte, inc.</small>
        </div>
    </footer>

</body>
</html>