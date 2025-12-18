@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="title-row">
    <div class="title-bar"></div>
    <h2 class="page-title">勤怠詳細</h2>
</div>

<div class="attendance-detail-container">
    @if (!$isPending)
    <form method="POST" action="{{ route('stamp_correction_request.store') }}">
        @csrf
    @else
    <div class="readonly-form">
    @endif

        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        <input type="hidden" name="target_date" value="{{ optional($attendance->date)->format('Y-m-d') }}">

        <div class="detail-row">
            <label>名前</label>
            <span>{{ $user->name }}</span>
        </div>

        <div class="detail-row">
            <label>日付</label>
            <span>{{ optional($attendance->date)->format('Y年n月j日') }}</span>
            <input type="hidden" name="date"
                   value="{{ optional($attendance->date)->format('Y-m-d') }}">
        </div>

        <div class="detail-row">
            <label>出勤・退勤</label>
            <div class="time-pair">
                <input type="time"
                    name="corrected_start_time"
                    value="{{ optional($attendance->started_at)->format('H:i') }}"
                    {{ $isPending ? 'readonly' : '' }}>

                <span>～</span>

                <input type="time"
                    name="corrected_end_time"
                    value="{{ optional($attendance->left_at)->format('H:i') }}"
                    {{ $isPending ? 'readonly' : '' }}>
            </div>
        </div>

        @php
            $breaks = $attendance->breaks ?? [];
            $maxBreaks = count($breaks) + 1;
        @endphp

        @for ($i = 0; $i < $maxBreaks; $i++)
            <div class="detail-row">
                <label>{{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}</label>
                <div class="time-pair">
                    <input type="time"
                        name="breaks[{{ $i }}][start]"
                        value="{{ $breaks[$i]['start'] ?? '' }}"
                        {{ $isPending ? 'readonly' : '' }}>

                    <span>～</span>

                    <input type="time"
                        name="breaks[{{ $i }}][end]"
                        value="{{ $breaks[$i]['end'] ?? '' }}"
                        {{ $isPending ? 'readonly' : '' }}>
                </div>
            </div>
        @endfor

        <div class="detail-row">
            <label>備考</label>
            <textarea name="note"
                    {{ $isPending ? 'readonly' : '' }}>{{ $attendance->note }}</textarea>
        </div>

        @if (!$isPending)
            <div class="button-row">
                <button type="submit" class="submit-button">修正</button>
            </div>
        @else
            <div class="pending-message">
                ※ 承認待ちのため修正できません
            </div>
        @endif

    @if (!$isPending)
    </form>
    @else
    </div>
    @endif
</div>
@endsection
