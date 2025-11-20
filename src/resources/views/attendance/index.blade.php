@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">

    {{-- 勤務状態を最上部に表示 --}}
    <div class="attendance-status">
        @if ($status === '勤務外')
            勤務外
        @elseif ($status === '勤務中')
            出勤中
        @elseif ($status === '休憩中')
            休憩中
        @elseif ($status === '勤務終了')
            退勤済
        @endif
    </div>

    {{-- 日付（漢字曜日）と中央の時間表示 --}}
    <div class="attendance-date">{{ $now }}</div>
    <div class="attendance-time">{{ \Carbon\Carbon::now()->format('H:i') }}</div>

    {{-- ボタン表示 --}}
    <div class="attendance-buttons">
        @if ($status === '勤務外')
            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button type="submit" class="btn-start">出勤</button>
            </form>
        @elseif ($status === '勤務中')
            <form method="POST" action="{{ route('attendance.leave') }}">
                @csrf
                <button type="submit" class="btn-leave">退勤</button>
            </form>
            <form method="POST" action="{{ route('attendance.break') }}">
                @csrf
                <button type="submit" class="btn-break">休憩入</button>
            </form>
        @elseif ($status === '休憩中')
            <form method="POST" action="{{ route('attendance.endBreak') }}">
                @csrf
                <button type="submit" class="btn-break-end">休憩戻</button>
            </form>
        @elseif ($status === '勤務終了')
            <p class="finished-message">お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection
