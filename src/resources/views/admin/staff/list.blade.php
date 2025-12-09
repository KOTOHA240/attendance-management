@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}">
@endsection

@section('content')
<div class="staff-container">
    <div class="staff-header-wrapper">
        <div class="staff-header">
            <span class="vertical-bar"></span>
            <h2 class="staff-title">スタッフ一覧</h2>
        </div>
    </div>

    <div class="staff-table-wrapper">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <a href="{{ route('admin.attendance.staff', ['id' => $user->id]) }}" class="detail-link">詳細</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">スタッフ情報がありません。</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

