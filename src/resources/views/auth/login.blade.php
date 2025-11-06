@extends('layouts.simple')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-container">
    <h1>ログイン</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label for="email">メールアドレス</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}">
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">パスワード</label>
        <input type="password" id="password" name="password">
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror
        <button type="submit" class="btn-login">ログインする</button>
    </form>
    <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
</div>
@endsection