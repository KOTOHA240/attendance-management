@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/detail.css') }}">
@endsection

@section('content')
<div class="title-row">
    <div class="title-bar"></div>
    <h2 class="page-title">勤怠詳細</h2>
</div>

<div class="attendance-detail-container">
    <div class="detail-table">
        <div class="detail-row">
            <label>名前</label>
            <span>{{ $request->user->name }}</span>
        </div>
        <div class="detail-row">
            <label>日付</label>
            <span>{{ \Carbon\Carbon::parse($request->target_date)->format('Y年n月j日') }}</span>
        </div>
        <div class="detail-row">
            <label>出勤・退勤</label>
            <span>{{ $request->corrected_start_time }} ～ {{ $request->corrected_end_time }}</span>
        </div>
        <div class="detail-row">
            <label>休憩</label>
            <span>{{ $request->corrected_break_start_time }} ～ {{ $request->corrected_break_end_time }}</span>
        </div>
        @if($request->break2_start_time && $request->break2_end_time)
        <div class="detail-row">
            <label>休憩2</label>
            <span>{{ $request->break2_start_time }} ～ {{ $request->break2_end_time }}</span>
        </div>
        @endif
        <div class="detail-row">
            <label>備考</label>
            <span>{{ $request->note }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('stamp_correction_request.approve.execute', $request->id) }}" class="approve-form">
        @csrf
        <button type="submit" class="submit-button">承認</button>
    </form>
</div>
@endsection
