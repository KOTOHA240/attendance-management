<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $latest = Attendance::where('user_id', Auth::id())->latest()->first();
        $status = $latest?->status ?? '勤務外';
        $now = Carbon::now()->format('Y年n月j日(D) H:i');

        return view('attendance.index', compact('status', 'now'));
    }

    public function startWork(Request $request)
    {
        $user = Auth::user();
        $user->status = '勤務中';
        $user->save();

        return redirect()->route('attendance.index');
    }

    public function list()
    {
        $attendances = Attendance::where('user_id', auth()->id())->get();
        return view('attendance.list', compact('attendances'));
    }

    public function leaveWork(Request $request)
    {
        $user = Auth::user();
        $user->status = '退勤済み';
        $user->save();

        return redirect()->route('attendance.index');
    }

    public function startBreak(Request $request)
    {
        $user = Auth::user();
        $user->status = '休憩中';
        $user->save();

        return redirect()->route('attendance.index');
    }
}
