<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;


class StampCorrectionRequestController extends Controller
{
    /**
     * 申請一覧画面の表示
     */
    public function list(Request $request)
    {
        $status = $request->input('status', 'pending');

        if (Auth::user()->is_admin) {
            $query = StampCorrectionRequest::with('user');
        } else {
            $query = StampCorrectionRequest::with('user')
                ->where('user_id', Auth::id());
        }

        $requests = $query
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->get();

        return view('stamp_correction_request.list', compact('requests', 'status'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'attendance_id' => ['required', 'exists:attendances,id'],
            'target_date' => ['required', 'date'],
            'corrected_start_time' => ['nullable', 'date_format:H:i'],
            'corrected_end_time' => ['nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:1000'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $attendance = Attendance::where('id', $validated['attendance_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $breaks = collect($request->input('breaks', []))
            ->filter(fn ($b) => !empty($b['start']) && !empty($b['end']))
            ->values()
            ->toArray();

        StampCorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'target_date' => $validated['target_date'],
            'reason' => $request->input('reason'),
            'status' => 'pending',
            'corrected_start_time' => $request->input('corrected_start_time'),
            'corrected_end_time' => $request->input('corrected_end_time'),
            'corrected_breaks' => $breaks,
            'note' => $request->input('note'),
        ]);

        return redirect()->route('attendance.detail', ['id' => $attendance->id])
            ->with('success', '修正申請を提出しました。');

    }

    public function approveDetail($id)
    {
        $requestItem = StampCorrectionRequest::with(['user', 'attendance'])->findOrFail($id);
        $attendance = $requestItem->attendance;

        return view('admin.stamp_correction_request.detail', [
            'request' => $requestItem,
            'attendance' => $attendance,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $requestItem = StampCorrectionRequest::with('attendance')->findOrFail($id);
        $attendance = $requestItem->attendance;

        // 勤怠データを修正後の内容で上書き
        if ($requestItem->corrected_start_time) {
            $attendance->started_at = $requestItem->corrected_start_time;
        }
        if ($requestItem->corrected_end_time) {
            $attendance->left_at = $requestItem->corrected_end_time;
        }
        if ($requestItem->corrected_breaks) {
            $attendance->breaks = $requestItem->corrected_breaks;
        }
        if ($requestItem->note) {
            $attendance->note = $requestItem->note;
        }
        $attendance->save();

        // 申請を承認済みに更新
        $requestItem->status = 'approved';
        $requestItem->save();

        return redirect()->route('stamp_correction_request.list', ['status' => 'approved'])
            ->with('success', '修正申請を承認し、勤怠データに反映しました。');
    }
}