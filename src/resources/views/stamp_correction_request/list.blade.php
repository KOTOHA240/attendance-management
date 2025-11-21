@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection

@section('content')
<div class="request-list-container">
    <div class="title-row">
        <div class="title-bar"></div>
        <h2 class="page-title">申請一覧</h2>
    </div>

    <div class="tab-selector">
        <a href="{{ route('stamp_correction_request.list', ['status' => 'pending']) }}"
           class="tab {{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('stamp_correction_request.list', ['status' => 'approved']) }}"
           class="tab {{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <div class="tab-underline"></div>

    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($requests as $request)
                <tr>
                    <td>{{ $request->status_label }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->target_date)->format('Y/m/d') }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</td>
                    <td><a href="{{ route('attendance.detail', \Carbon\Carbon::parse($request->target_date)->format('Y-m-d')) }}" class="detail-link">詳細</a></td>
                </tr>
            @empty
                <tr><td colspan="6">申請はありません。</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
