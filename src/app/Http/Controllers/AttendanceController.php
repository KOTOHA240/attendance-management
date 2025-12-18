<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        if (! $todayAttendance) {
            $user->status = '勤務外';
            $user->save();
        }

        $status = $user->status ?? '勤務外';
        $now = Carbon::now()->isoFormat('YYYY年M月D日(ddd)');

        return view('attendance.index', compact('status', 'now'));
    }

    public function startWork()
    {
        $user = Auth::user();
        $user->status = '勤務中';
        $user->save();

        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            ['started_at' => now()]
        );

        return redirect()->route('attendance.index');
    }

    public function leaveWork()
    {
        $user = Auth::user();
        $user->status = '勤務終了';
        $user->save();

        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            ['left_at' => now()]
        );

        return redirect()->route('attendance.index');
    }

    public function list(Request $request)
    {
        $user = auth()->user();
        $month = $request->input('month', now()->format('Y-m'));
        $targetDate = \Carbon\Carbon::createFromFormat('Y-m', $month);

        $start = $targetDate->copy()->startOfMonth();
        $end   = $targetDate->copy()->endOfMonth();

    // ★ 必ず配列として初期化
        $attendances = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

            $attendance = Attendance::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'date'    => $date->toDateString(),
                ],
                [
                    'started_at' => null,
                    'left_at'    => null,
                    'breaks'     => [],
                    'note'       => '',
                ]
            );

        // ★ Attendanceモデルをそのまま push
            $attendances[] = $attendance;
        }

        $prevMonth = $targetDate->copy()->subMonth();
        $nextMonth = $targetDate->copy()->addMonth();

        return view('attendance.list', compact(
            'attendances',
            'targetDate',
            'prevMonth',
            'nextMonth'
        ));
    }

    public function detail($id)
    {
        $user = auth()->user();

        // 勤怠取得（自分のものだけ）
        $attendance = Attendance::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // breaks が null の場合に備える
        $attendance->breaks = $attendance->breaks ?? [];

        // 最新の修正申請
        $latestRequest = StampCorrectionRequest::where('attendance_id', $attendance->id)
            ->latest()
            ->first();

        // 承認待ち判定（status は pending に統一）
        $isPending = $latestRequest && $latestRequest->status === 'pending';

        if ($isPending) {
            $attendance->started_at = $latestRequest->corrected_start_time ?? $attendance->started_at;
            $attendance->left_at    = $latestRequest->corrected_end_time ?? $attendance->left_at;
            $attendance->note       = $latestRequest->note ?? $attendance->note;

            $attendance->breaks     = $latestRequest->corrected_breaks ?? $attendance->breaks;
        }

        return view('attendance.detail', [
            'attendance'     => $attendance,
            'user'           => $user,
            'latestRequest'  => $latestRequest,
            'isPending'      => $isPending,
        ]);
    }


    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        if ($attendance->user_id !== auth()->id()) {
            abort(403);
        }

        if ($request->filled('started_at')) {
            $attendance->started_at =
                $attendance->date->copy()->setTimeFromTimeString($request->started_at);
        }

        if ($request->filled('left_at')) {
            $attendance->left_at =
                $attendance->date->copy()->setTimeFromTimeString($request->left_at);
        }

        $attendance->note = $request->input('note');
        $attendance->breaks = $request->input('breaks', []);
        $attendance->is_pending = true;
        $attendance->save();

        StampCorrectionRequest::create([
            'user_id' => auth()->id(),
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date,
            'reason' => $request->note,
            'status' => 'pending',
        ]);

        return redirect()->route('stamp_correction_request.list')
            ->with('status', '修正申請を送信しました');
    }
}
