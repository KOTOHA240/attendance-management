@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff-attendance.css') }}">
@endsection

@section('content')
<div class="attendance-list-wrapper">
    <div class="title-row">
        <div class="title-bar"></div>
        <h2 class="page-title">{{ $user->name }} さんの勤怠一覧</h2>
    </div>

    <div class="month-selector">
        <a href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $prevMonth->format('Y-m')]) }}" class="month-nav prev-month">← 前月</a>
        <span class="current-month">{{ $targetDate->format('Y年m月') }}</span>
        <a href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $nextMonth->format('Y-m')]) }}" class="month-nav next-month">翌月 →</a>
    </div>

    <div class="table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>勤務</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance['date']->isoFormat('MM/DD(dd)') }}</td>
                        <td>{{ $attendance['started_at'] ? $attendance['started_at']->format('H:i') :  '' }}</td>
                        <td>{{ $attendance['left_at'] ? $attendance['left_at']->format('H:i') : '' }}</td>
                        <td>{{ $attendance['break_time'] ?? '' }}</td>
                        <td>{{ $attendance['work_time'] ?? '' }}</td>
                        <td>
                            <a href="{{ route('admin.attendance.detail', ['userId' => $user->id, 'date' => $attendance['date']->format('Y-m-d')]) }}" class="detail-link">詳細</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">勤怠データがありません。</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

