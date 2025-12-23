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
            ->whereDate('started_at', today())
            ->first();

        if (! $todayAttendance) {
            $status = '勤務外';
        } elseif ($todayAttendance->left_at) {
            $status = '勤務終了';
        } elseif (!empty($todayAttendance->breaks)) {
            $lastBreak = collect($todayAttendance->breaks)->last();
            if ($lastBreak && empty($lastBreak['end'])) {
                $status = '休憩中';
            } else {
                $status = '勤務中';
            }
        } else {
            $status = '勤務中';
        }

        $now = Carbon::now()->isoFormat('YYYY年M月D日(ddd)');

        return view('attendance.index', compact('status', 'now'));
    }

    public function startWork()
    {
        $user = Auth::user();
        $user->status = '勤務中';
        $user->save();

        $today = now()->format('Y-m-d');

        // 今日の枠レコードを確実に取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('started_at', $today)
            ->first();

        if ($attendance) {
            // 枠レコードを更新
            $attendance->started_at = now();
            $attendance->save();
        } else {
            // 念のため新規作成（通常は不要）
            Attendance::create([
                'user_id' => $user->id,
                'started_at' => now(),
                'breaks' => [],
            ]);
        }

        return redirect()->route('attendance.index');
    }

    public function leaveWork()
    {
        $user = Auth::user();
        $user->status = '勤務終了';
        $user->save();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('started_at', today())
            ->first();

        if ($attendance) {
            $attendance->left_at = now();
            $attendance->save();
        }

        return redirect()->route('attendance.index');
    }

    public function startBreak()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('started_at', today())
            ->firstOrFail();

        $breaks = $attendance->breaks ?? [];

        $breaks[] = [
            'start' => now()->toDateTimeString(),
            'end'   => null,
        ];

        $attendance->breaks = $breaks;
        $attendance->save();

        $user->status = '休憩中';
        $user->save();

        return redirect()->route('attendance.index');
    }

    public function endBreak()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('started_at', today())
            ->firstOrFail();

        $breaks = $attendance->breaks ?? [];
        $lastIndex = count($breaks) - 1;

        if ($lastIndex >= 0 && empty($breaks[$lastIndex]['end'])) {
            $breaks[$lastIndex]['end'] = now()->toDateTimeString();
        }

        $attendance->breaks = $breaks;
        $attendance->save();

        $user->status = '勤務中';
        $user->save();

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
                    'started_at' => $date->copy()->startOfDay(),
                ],
                [
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
            if (!empty($latestRequest->corrected_start_time)) { 
                if ($attendance->started_at) { 
                    $attendance->started_at = $attendance->started_at 
                        ->copy() 
                        ->setTimeFromTimeString($latestRequest->corrected_start_time); 
                } 
            }

            if (!empty($latestRequest->corrected_end_time)) {
                if ($attendance->left_at) {
                    $attendance->left_at = $attendance->left_at 
                        ->copy() 
                        ->setTimeFromTimeString($latestRequest->corrected_end_time); 
                } 
            }

            if (!empty($latestRequest->corrected_breaks)) {
                 $attendance->breaks = $latestRequest->corrected_breaks; 
            }

            if (!empty($latestRequest->note)) { 
                $attendance->note = $latestRequest->note;
            }
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
                $attendance->started_at
                    ? $attendance->started_at->copy()->setTimeFromTimeStrig($request->started_at)
                    :now()->setTimeFirmTimeString($request->started_at);
        }

        if ($request->filled('left_at')) {
            $attendance->left_at =
                $attendance->left_at
                    ? $attendance->started_at->copy()->setTimeFromTimeString($request->left_at)
                    : now()->setTimeFormTimeString($request->left_at);
        }

        $attendance->note = $request->input('note');
        $attendance->breaks = $request->input('breaks', []);
        $attendance->is_pending = true;
        $attendance->save();

        StampCorrectionRequest::create([
            'user_id' => auth()->id(),
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->started_at
                ? $attendance->started_at->toDateString()
                : null,
            'reason' => $request->note,
            'status' => 'pending',
        ]);

        return redirect()->route('stamp_correction_request.list')
            ->with('status', '修正申請を送信しました');
    }
}
