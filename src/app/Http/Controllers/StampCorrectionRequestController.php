<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StampCorrectionRequest;
use App\Models\Attendance;

class StampCorrectionRequestController extends Controller
{
    /**
     * 申請一覧画面の表示
     */

    public function list(Request $request)
    {
        $userId = auth()->id();
        $status = $request->input('status', 'pending');

        $query = StampCorrectionRequest::with('user')
            ->where('user_id', $userId);

        if ($status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        }

        $requests = $query->orderByDesc('created_at')->get();

        return view('stamp_correction_request.list', compact('requests', 'status'));
    }
}

