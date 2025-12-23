@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance.css') }}">
@endsection

@section('content')
<div class="admin-attendance-container">
    <div class="date-header">
        <h2>{{ $targetDate->format('Y年m月d日') }} の勤怠一覧</h2>
        <div class="date-nav">
            <a href="{{ route('admin.attendance.list', ['date' => $prevDate->format('Y-m-d')]) }}" class="nav-link">← 前日</a>
            <span class="current-date">
                <img src="{{ asset('images/calender.png') }}" alt="カレンダーアイコン" class="date-icon">
                {{ $targetDate->format('Y/m/d') }}
            </span>
            <a href="{{ route('admin.attendance.list', ['date' => $nextDate->format('Y-m-d')]) }}" class="nav-link">翌日 →</a>
        </div>
    </div>

    <div class="table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->user->name }}</td>
                        <td>{{ $attendance->started_at ? $attendance->started_at->format('H:i') : '' }}</td>
                        <td>{{ $attendance->left_at ? $attendance->left_at->format('H:i') : '' }}</td>
                        <td>{{ $attendance->break_time ?? '' }}</td>
                        <td>{{ $attendance->work_time ?? '' }}</td>
                        <td>
                            <a href="{{ route('admin.attendance.detail', [
                                'userId' => $attendance->user_id,
                                'date' => optional($attendance->started_at)->format('Y-m-d')
                                ]) }}" class="detail-link">詳細</a>
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

