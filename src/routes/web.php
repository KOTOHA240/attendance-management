<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\Auth\RegisteredUserController;

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

// 会員登録
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');

// ログイン必須（認証不要）のルート
Route::middleware(['auth'])->group(function () {
    // ログイン後に誰でも見られる画面（例：ダッシュボード）
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{date}', [AttendanceController::class, 'detail'])->name('attendance.detail');
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'list'])->name('stamp_correction_request.list');
});

// ログイン＋メール認証必須のルート
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [AttendanceController::class, 'startWork'])->name('attendance.start');
    Route::post('/attendance/leave', [AttendanceController::class, 'leaveWork'])->name('attendance.leave');
    Route::post('/attendance/break', [AttendanceController::class, 'startBreak'])->name('attendance.break');
    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])->name('attendance.endBreak');
    Route::put('/attendance/update/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
});

// 管理者用
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.list');
});

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

// ログイン処理
Route::post('/login', [LoginController::class, 'login'])->name('login');

// 認証メール通知画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');
