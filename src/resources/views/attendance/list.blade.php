@extends('layouts.app')

@section('content')
<div class="attendance-container">
    <h2>勤怠一覧</h2>
    <table>
        <thead>
            <tr>
                <th>日付</th>
                <th>状態</th>
                <th>出勤</th>
                <th>休憩</th>
                <th>退勤</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->created_at->format('Y-m-d') }}</td>
                    <td>{{ $attendance->status }}</td>
                    <td>{{ $attendance->started_at }}</td>
                    <td>{{ $attendance->break_started_at }}</td>
                    <td>{{ $attendance->left_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
