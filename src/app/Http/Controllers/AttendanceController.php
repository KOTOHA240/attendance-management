<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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

        // 現在の状態は users.status から取得
        $status = $user->status ?? '勤務外';

        // 日付（漢字曜日）
        $now = \Carbon\Carbon::now()->isoFormat('YYYY年M月D日(ddd)');

        return view('attendance.index', compact('status', 'now'));
    }

    public function startWork(Request $request)
    {
        $user = Auth::user();
        $user->status = '勤務中';
        $user->save();

        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            [
                'date' => today(),
                'started_at' => now(),
            ]
        );

        return redirect()->route('attendance.index');
    }

    public function list(Request $request)
    {
        $userId = auth()->id();

        $monthParam = $request->input('month', now()->format('Y-m'));
        $targetDate = Carbon::createFromFormat('Y-m', $monthParam);

        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // 勤怠データを取得して日付でキー化
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy('date');

        // 月の日付一覧を生成し、勤怠データをマージ
        $days = new \Illuminate\Support\Collection();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $attendance = $attendances->get($date->toDateString());

            $days->push([
                'date' => $date->copy(),
                'started_at' => optional($attendance)->started_at ? Carbon::parse($attendance->started_at) : null,
                'left_at' => optional($attendance)->left_at ? Carbon::parse($attendance->left_at) : null,
                'break_started_at' => optional($attendance)->break_started_at ? Carbon::parse($attendance->break_started_at) : null,
                'break_ended_at' => optional($attendance)->break_ended_at ? Carbon::parse($attendance->break_ended_at) : null,
                'break_time' => optional($attendance)->break_time,
                'work_time' => optional($attendance)->work_time,
                'id' => optional($attendance)->id,
            ]);
        }

        // 前月・翌月のCarbonインスタンスを渡す
        $prevMonth = $targetDate->copy()->subMonth();
        $nextMonth = $targetDate->copy()->addMonth();

        return view('attendance.list', [
            'attendances' => $days,
            'targetDate' => $targetDate,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
        ]);
    }


    public function leaveWork(Request $request)
    {
        $user = Auth::user();
        $user->status = '勤務終了';
        $user->save();

        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            [
                'date' => today(),           // ← 追加
                'left_at' => now(),
            ]
        );

        return redirect()->route('attendance.index');
    }


    public function startBreak(Request $request)
    {
        $user = Auth::user();
        $user->status = '休憩中';
        $user->save();

        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            [
                'date' => today(),
                'break_started_at' => now(),
            ]
        );

        return redirect()->route('attendance.index');
    }

    public function endBreak(Request $request)
    {
        $user = Auth::user();
        $user->status = '勤務中';
        $user->save();

        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()],
            [
                'date' => today(),
                'break_ended_at' => now(),
            ]
        );

        return redirect()->route('attendance.index');
    }

    public function detail($date)
    {
        $userId = auth()->id();
        $targetDate = Carbon::createFromFormat('Y-m-d', $date);

        // 該当日の勤怠データを取得（なければ null）
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $targetDate)
            ->first();

            // 存在しない場合は空のインスタンスを作成（保存はしない）
        if (! $attendance) {
            $attendance = new Attendance([
                'user_id' => $userId,
                'started_at' => null,
                'left_at' => null,
                'breaks' => [],
                'note' => null,
                'is_pending' => false,
            ]);
            $attendance->date = $targetDate->toDateString();
        } else {
            $attendance->breaks = $attendance->breaks ?? [];
        }

        $user = auth()->user();

        return view('attendance.detail', compact('attendance', 'user'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);

        if (! $attendance || $attendance->user_id !== auth()->id()) {
            abort(403);
        }

        // 勤怠データを更新（バリデーションは省略）
        $attendance->started_at = $request->input('started_at')
            ? Carbon::createFromFormat('H:i', $request->input('started_at'))->setDateFrom($attendance->date) : null;
        $attendance->left_at = $request->input('left_at')
            ? Carbon::createFromFormat('H:i', $request->input('left_at'))->setDateFrom($attendance->date)
            : null;
        $attendance->note = $request->input('note');
        $attendance->breaks = $request->input('breaks', []);
        $attendance->is_pending = true;
        $attendance->save();

        if (!StampCorrectionRequest::where('attendance_id', $attendance->id)->exists()) {
            StampCorrectionRequest::create([
                'user_id'       => auth()->id(),
                'attendance_id' => $attendance->id,
                'target_date'   => $attendance->date,
                'reason'        => $request->input('note'),
                'is_approved'   => false,
            ]);

        }

        return redirect()->route('stamp_correction_request.list')->with('status', '修正申請を送信しました');
    }

    public function store(Request $request)
    {
        $attendance = new Attendance();
        $attendance->user_id = auth()->id();
        $attendance->date = $request->input('date');
        $attendance->started_at = $request->input('started_at')
            ? Carbon::createFromFormat('H:i', $request->input('started_at'))->setDateFrom($attendance->date) : null; // 統一
        $attendance->left_at = $request->input('left_at')
            ? Carbon::createFromFormat('H:i', $request->input('left_at'))->setDateFrom($attendance->date)
            : null;// 統一
        $attendance->note = $request->input('note');
        $attendance->breaks = $request->input('breaks', []);
        $attendance->is_pending = true;
        $attendance->save();

        // 申請レコードを作成
        StampCorrectionRequest::create([
            'user_id'       => auth()->id(),
            'attendance_id' => $attendance->id,
            'target_date'   => $attendance->date,
            'reason'        => $request->input('note'),
            'is_approved'   => false,
        ]);

        return redirect()->route('stamp_correction_request.list')
                        ->with('status', '勤怠申請を送信しました');
    }
}
