@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    @if ($attendance->is_pending)
        <p class="warning-text">※承認待ちのため修正はできません。</p>
    @endif

    <form method="POST" action="{{ $attendance->id ? route('attendance.update', $attendance->id) : route('attendance.store') }}">
        @csrf
        @if ($attendance->id)
            @method('PUT')
        @endif

        <input type="hidden" name="date" value="{{ $attendance->date }}">


        <div class="detail-row">
            <label>名前：</label>
            <span>{{ $user->name }}</span>
        </div>

        <div class="detail-row">
            <label>日付：</label>
            <span>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</span>
        </div>

        <div class="detail-row">
            <label>出勤・退勤：</label>
            @if ($attendance->is_pending)
                <span>{{ $attendance->started_at }} ～ {{ $attendance->left_at }}</span>
            @else
                <div class="time-pair">
                    <input type="time" name="started_at" value="{{ $attendance->started_at ? $attendance->started_at->format('H:i') : '' }}">
                    <span>～</span>
                    <input type="time" name="left_at" value="{{ $attendance->left_at ? $attendance->left_at->format('H:i') : '' }}">
                </div>
            @endif
        </div>

        <div class="detail-row">
            <label>休憩：</label>
            @php
                $breaks = $attendance->breaks ?? [];
                $maxBreaks = count($breaks) + 1;
            @endphp

            @for ($i = 0; $i < $maxBreaks; $i++)
                <div class="break-row">
                    <label>{{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}</label>
                    <input type="time" name="breaks[{{ $i }}][start]" value="{{ $breaks[$i]['start'] ?? '' }}">
                    ～
                    <input type="time" name="breaks[{{ $i }}][end]" value="{{ $breaks[$i]['end'] ?? '' }}">
                </div>
            @endfor
        </div>

        <div class="detail-row">
            <label>備考：</label>
            @if ($attendance->is_pending)
                <span>{{ $attendance->note }}</span>
            @else
                <textarea name="note">{{ $attendance->note }}</textarea>
            @endif
        </div>

        @unless ($attendance->is_pending)
            <div class="button-row">
                <button type="submit" class="submit-button">修正</button>
            </div>
        @endunless
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
