@extends('layouts.simple')

@section('content')
<div class="verify-container">
    <h2>メール認証のお願い</h2>

    <p>登録していただいたメールアドレスに認証リンクを送信しました。<br>
    メールをご確認ください。</p>

    <p>メールが届いていない場合は、以下のリンクから再送できます。</p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="resend-button">認証メールを再送する</button>
    </form>

    @if (session('message'))
        <p class="success-message">{{ session('message') }}</p>
    @endif
</div>
@endsection