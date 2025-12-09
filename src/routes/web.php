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

// 管理者ログイン画面（認証不要）
Route::get('/admin/login', [\App\Http\Controllers\Admin\LoginController::class, 'showLoginForm'])
    ->name('admin.login');

// 管理者ログイン処理（認証不要）
Route::post('/admin/login', [\App\Http\Controllers\Admin\LoginController::class, 'login'])
    ->name('admin.login.post');

// 管理者ログアウト（認証必須）
Route::post('/admin/logout', [\App\Http\Controllers\Admin\LoginController::class, 'logout'])
    ->name('admin.logout');

// 管理者専用画面（認証必須）
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('attendance/list', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])
        ->name('admin.attendance.list');

    Route::get('staff/list', [\App\Http\Controllers\Admin\StaffController::class, 'index'])
        ->name('admin.staff.list');
    
    Route::get('attendance/staff/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'staffDetail'])
        ->name('admin.attendance.staff');
    
    Route::get('attendance/{userId}/{date}', [\App\Http\Controllers\Admin\AttendanceController::class, 'detail'])
        ->name('admin.attendance.detail');
    
    Route::post('attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])
        ->name('admin.attendance.store');

    Route::put('attendance/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'update'])
        ->name('admin.attendance.update');
});

// ログイン処理
Route::post('/login', [LoginController::class, 'login'])->name('login');

// 認証メール通知画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');
