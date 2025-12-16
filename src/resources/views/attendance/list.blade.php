@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="attendance-list-wrapper">
    <div class="title-row">
        <div class="title-bar"></div>
        <h2 class="page-title">勤怠一覧</h2>
    </div>

    <div class="month-selector">
        <a href="{{ route('attendance.list', ['month' => $prevMonth->format('Y-m')]) }}" class="month-nav prev-month">← 前月</a>
        <span class="current-month">
            <img src="{{ asset('images/calender.png') }}" alt="カレンダーアイコン" class="month-icon">
            {{ $targetDate->format('Y/m') }}
        </span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth->format('Y-m')]) }}" class="month-nav next-month">翌月 →</a>
    </div>

    <div class="table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->isoFormat('MM/DD(dd)') }}</td>
                        <td>{{ optional($attendance->started_at)->format('H:i') }}</td>
                        <td>{{ optional($attendance->left_at)->format('H:i') }}</td>
                        <td>{{ $attendance->break_time ?? '' }}</td>
                        <td>{{ $attendance->work_time ?? '' }}</td>
                        <td>
                            <a href="{{ route('attendance.detail', $attendance->id) }}"  class="detail-link">
                                詳細
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
