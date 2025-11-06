<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::post('/attendance/start', [AttendanceController::class, 'startWork'])->name('attendance.start');
    Route::post('/attendance/leave', [AttendanceController::class, 'leaveWork'])->name('attendance.leave');
    Route::post('/attendance/break', [AttendanceController::class, 'startBreak'])->name('attendance.break');
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'list'])->name('stamp_correction_request.list');
});

Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.list');
});

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');
