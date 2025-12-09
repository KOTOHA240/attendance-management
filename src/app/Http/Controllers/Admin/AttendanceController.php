<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $targetDate = $request->date ? \Carbon\Carbon::parse($request->date) : \Carbon\Carbon::today();
        $prevDate = $targetDate->copy()->subDay();
        $nextDate = $targetDate->copy()->addDay();

        $attendances = \App\Models\Attendance::with('user')
            ->whereDate('date', $targetDate)
            ->get();

        return view('admin.attendance.list', compact('attendances', 'targetDate', 'prevDate', 'nextDate'));
    }

    public function staffDetail($id, Request $request)
    {
        $user = User::findOrFail($id);

        // 表示対象月（クエリパラメータがあればそれを使う）
        $targetDate = $request->input('month')
            ? Carbon::createFromFormat('Y-m', $request->input('month'))
            : Carbon::now();

        // 前月・翌月を計算
        $prevMonth = $targetDate->copy()->subMonth();
        $nextMonth = $targetDate->copy()->addMonth();

        $period = CarbonPeriod::create(
            $targetDate->copy()->startOfMonth(),
            $targetDate->copy()->endOfMonth()
        );

        // 勤怠データを取得（例: 月単位で絞り込み）
        $attendanceData = $user->attendances()
            ->whereMonth('date', $targetDate->month)
            ->whereYear('date', $targetDate->year)
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });
        
        $attendances = [];
        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $record = $attendanceDate[$key] ?? null;

            $attendances[] = [
                'id'         => $record->id ?? null,
                'date'       => $date,
                'started_at' => $attendanceData[$key]->started_at ?? null,
                'left_at'    => $attendanceData[$key]->left_at ?? null,
                'break_time' => $attendanceData[$key]->break_time ?? null,
                'work_time'  => $attendanceData[$key]->work_time ?? null,
            ];
        }

        return view('admin.attendance.staff', compact(
            'user', 'attendances', 'targetDate', 'prevMonth', 'nextMonth'
        ));
    }

    public function detail($userId, $date)
    {
        $user = User::findOrFail($userId);

        $attendance = $user->attendances()->whereDate('date', $date)->first();

        if (!$attendance) {
            $attendance = new \App\Models\Attendance([
                'date' => $date,
                'started_at' => null,
                'left_at' => null,
                'break_time' => null,
                'work_time' => null,
                'note' => null,
            ]);
        }

        return view('admin.attendance.detail', compact('user', 'attendance', 'date'));
    }

    public function store(Request $request)
    {
        $attendance = new \App\Models\Attendance();
        $attendance->user_id   = $request->input('user_id');
        $attendance->date      = $request->input('date');
        $attendance->started_at= $request->input('started_at');
        $attendance->left_at   = $request->input('left_at');
        $attendance->note      = $request->input('note');
        $attendance->breaks    = $request->input('breaks'); // JSONカラムならそのまま保存

        $attendance->save();

        return redirect()->route('admin.attendance.detail', [
            'userId' => $attendance->user_id,
            'date'   => $attendance->date
        ])->with('success', '勤怠情報を新規登録しました');
    }

    public function update(Request $request, $id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);

        $attendance->started_at= $request->input('started_at');
        $attendance->left_at   = $request->input('left_at');
        $attendance->note      = $request->input('note');
        $attendance->breaks    = $request->input('breaks');

        $attendance->save();

        return redirect()->route('admin.attendance.detail', [
            'userId' => $attendance->user_id,
            'date'   => $attendance->date
        ])->with('success', '勤怠情報を更新しました');
    }
}
