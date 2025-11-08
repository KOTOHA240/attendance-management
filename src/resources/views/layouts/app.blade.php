<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header-left">
            <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH Logo" class="logo">
        </div>
        <div class="header-right">
            <nav class="nav-links">
                <a href="{{ route('attendance.index') }}">勤怠</a>
                <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                <a href="{{ route('stamp_correction_request.list') }}">申請</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
