@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance-detail.css') }}">
@endsection

@section('content')
<div class="title-row">
    <div class="title-bar"></div>
    <h2 class="page-title">勤怠詳細</h2>
</div>

<div class="attendance-detail-container">
    <form method="POST" action="{{ $attendance->id ? route('admin.attendance.update', $attendance->id) : route('admin.attendance.store') }}">
        @csrf
        @if ($attendance->id)
            @method('PUT')
        @endif

        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <input type="hidden" name="date" value="{{ $attendance->date }}">

        <div class="detail-row">
            <label>名前</label>
            <span>{{ $user->name }}</span>
        </div>

        <div class="detail-row date-row">
            <label>日付</label>
            <div class="date-pair">
                <span class="year">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</span>
                <span class="month-day">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</span>
            </div>
        </div>

        <div class="detail-row">
            <label>出勤・退勤</label>
            <div class="time-pair">
                <input type="time" name="started_at" value="{{ $attendance->started_at ? $attendance->started_at->format('H:i') : '' }}">
                <span>～</span>
                <input type="time" name="left_at" value="{{ $attendance->left_at ? $attendance->left_at->format('H:i') : '' }}">
            </div>
        </div>

        @php
            $breaks = $attendance->breaks ?? [];
            $maxBreaks = 5; // 最大休憩数を決めておく（例: 5）
        @endphp

        @for ($i = 0; $i < $maxBreaks; $i++)
            @php
                // 休憩0は必ず表示
                // 休憩1以降は「直前の休憩が両方埋まっている場合のみ表示」
                $show = $i === 0 || (!empty($breaks[$i - 1]['start']) && !empty($breaks[$i - 1]['end']));
            @endphp

            @if ($show)
                <div class="detail-row">
                    <label>{{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}</label>
                    <div class="time-pair">
                        <input type="time" name="breaks[{{ $i }}][start]" value="{{ $breaks[$i]['start'] ?? '' }}">
                        <span>～</span>
                        <input type="time" name="breaks[{{ $i }}][end]" value="{{ $breaks[$i]['end'] ?? '' }}">
                    </div>
                </div>
            @endif
        @endfor

        <div class="detail-row">
            <label>備考</label>
            <textarea name="note">{{ $attendance->note }}</textarea>
        </div>

        <div class="button-row">
            <button type="submit" class="submit-button">修正</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('add-break')?.addEventListener('click', function () {
        const container = document.getElementById('break-container');
        const index = container.children.length;
        const row = document.createElement('div');
        row.className = 'break-row';
        row.innerHTML = `
            <input type="time" name="breaks[${index}][start]"> ～
            <input type="time" name="breaks[${index}][end]">
        `;
        container.appendChild(row);
    });
</script>
@endsection
