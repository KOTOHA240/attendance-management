@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">

    <div class="attendance-date">{{ $now }}</div>
    <div class="attendance-time">{{ \Carbon\Carbon::now()->format('H:i') }}</div>

    @if ($status === '勤務外')
        <div class="attendance-status">勤務外</div>
        <div class="attendance-buttons">
            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button type="submit" class="btn-start">出勤打刻</button>
            </form>
        </div>

    @elseif ($status === '勤務中')
        <div class="attendance-status">現在の状態：勤務中</div>
        <div class="attendance-buttons">
            <form method="POST" action="{{ route('attendance.break') }}">
                @csrf
                <button type="submit" class="btn-break">休憩開始</button>
            </form>
            <form method="POST" action="{{ route('attendance.leave') }}">
                @csrf
                <button type="submit" class="btn-leave">退勤</button>
            </form>
        </div>

    @elseif ($status === '休憩中')
        <div class="attendance-status">現在の状態：休憩中</div>
        <div class="attendance-buttons">
            <form method="POST" action="{{ route('attendance.leave') }}">
                @csrf
                <button type="submit" class="btn-leave">退勤</button>
            </form>
        </div>

    @elseif ($status === '勤務終了')
        <div class="attendance-message">お疲れ様でした。</div>
    @endif
</div>
@endsection