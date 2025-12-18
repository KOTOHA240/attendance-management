<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Attendance;
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
            ->approvedOrNormal()
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
            ->approvedOrNormal()
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });
        
        $attendances = [];
        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $record = $attendanceData[$key] ?? null;

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

    public function exportStaffCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 表示中の月（staffDetail と同じロジック）
        $targetDate = $request->input('month')
            ? Carbon::createFromFormat('Y-m', $request->input('month'))
            : Carbon::now();

        $period = CarbonPeriod::create(
            $targetDate->copy()->startOfMonth(),
            $targetDate->copy()->endOfMonth()
        );

        // 月内の勤怠データをまとめて取得
        $attendanceData = $user->attendances()
            ->whereMonth('date', $targetDate->month)
            ->whereYear('date', $targetDate->year)
            ->get()
            ->keyBy(fn($item) => $item->date->format('Y-m-d'));

        $response = new StreamedResponse(function () use ($period, $attendanceData) {
            $handle = fopen('php://output', 'w');

            // ヘッダー行
            fputcsv($handle, [
                '日付',
                '出勤',
                '退勤',
                '休憩',
                '勤務時間',
            ]);

            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $attendance = $attendanceData[$key] ?? null;

                fputcsv($handle, [
                    $date->format('Y-m-d'),
                    optional($attendance?->started_at)->format('H:i'),
                    optional($attendance?->left_at)->format('H:i'),
                    $attendance->break_time ?? '',
                    $attendance->work_time ?? '',
                ]);
            }

            fclose($handle);
        });

        $fileName = 'attendance_' . $user->name . '_' . $targetDate->format('Y-m') . '.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set(
            'Content-Disposition',
            "attachment; filename={$fileName}"
        );

        return $response;
    }

    public function save(Request $request)
    {
        if ($request->filled('attendance_id')) {
            // 既存データの修正
            $attendance = Attendance::find($request->attendance_id);
        } else {
            // 新規作成
            $attendance = new Attendance();
            $attendance->user_id = $request->input('user_id'); // 管理者が対象ユーザーを指定
        }

        $attendance->date = Carbon::parse($request->input('date'));
        $attendance->started_at = $request->input('started_at')
            ? Carbon::parse($attendance->date->format('Y-m-d').' '.$request->input('started_at'))
            : null;
        $attendance->left_at = $request->input('left_at')
            ? Carbon::parse($attendance->date->format('Y-m-d').' '.$request->input('left_at'))
            : null;
        $attendance->note = $request->input('note');
        $attendance->breaks = $request->input('breaks', []);
        $attendance->is_pending = false; // 管理者は承認不要なので直接確定
        $attendance->save();

        return redirect()->route('admin.attendance.list', ['date' => $attendance->date->format('Y-m-d')])
            ->with('status', '勤怠データを保存しました');
    }
}
