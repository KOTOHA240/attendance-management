<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    /**
     * 申請一覧画面の表示
     */
    public function list()
    {
        $user = Auth::user();

        // ログインユーザーの申請一覧を取得（最新順）
        $requests = StampCorrectionRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('stamp_correction_request.list', compact('requests'));
    }
}

