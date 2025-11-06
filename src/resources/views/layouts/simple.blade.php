<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simple.css') }}">
    @yield('css')
</head>

<body>
    <header class="simple-header">
        <a href="{{ url('/') }}" class="logo-link">
            <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="logo-img">
        </a>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>