<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController;

/*
|--------------------------------------------------------------------------
| 認証不要（ゲスト）
|--------------------------------------------------------------------------
*/

// 会員登録
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

// ログイン
Route::post('/login', [LoginController::class, 'login'])
    ->name('login');

// 認証メール通知画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


/*
|--------------------------------------------------------------------------
| 一般ユーザー（ログイン必須）
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // 勤怠一覧・詳細
    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');

    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])
        ->name('attendance.detail');

    // 打刻修正申請
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'list'])
        ->name('stamp_correction_request.list');

    Route::post('/stamp_correction_request', [StampCorrectionRequestController::class, 'store'])
        ->name('stamp_correction_request.store');
});


/*
|--------------------------------------------------------------------------
| 一般ユーザー（ログイン + メール認証必須）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 勤怠打刻
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance/start', [AttendanceController::class, 'startWork'])
        ->name('attendance.start');

    Route::post('/attendance/leave', [AttendanceController::class, 'leaveWork'])
        ->name('attendance.leave');

    Route::post('/attendance/break', [AttendanceController::class, 'startBreak'])
        ->name('attendance.break');

    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])
        ->name('attendance.endBreak');

    // ※ 勤怠の直接保存は原則不要（申請経由に統一するなら削除OK）
    // Route::post('/attendance/store', [AttendanceController::class, 'store'])
    //     ->name('attendance.store');

    Route::put('/attendance/update/{id}', [AttendanceController::class, 'update'])
        ->name('attendance.update');
});


/*
|--------------------------------------------------------------------------
| 管理者（認証不要）
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('/admin/login', [AdminLoginController::class, 'login'])
    ->name('admin.login.post');


/*
|--------------------------------------------------------------------------
| 管理者（ログイン必須 + admin）
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // ログアウト
    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->name('admin.logout');

    // 勤怠管理
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])
        ->name('admin.attendance.list');

    Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'staffDetail'])
        ->name('admin.attendance.staff');

    Route::get('/attendance/{userId}/{date}', [AdminAttendanceController::class, 'detail'])
        ->name('admin.attendance.detail');

    Route::post('/attendance', [AdminAttendanceController::class, 'store'])
        ->name('admin.attendance.store');

    Route::post('admin/attendance/save', [AdminAttendanceController::class, 'save'])
        ->name('admin.attendance.save');

    // スタッフ管理
    Route::get('/staff/list', [StaffController::class, 'index'])
        ->name('admin.staff.list');

    // 打刻修正申請（承認）
    Route::get('/stamp_correction_request/approve/{id}', [StampCorrectionRequestController::class, 'approveDetail'])
        ->name('stamp_correction_request.approve');

    Route::get('/stamp_correction_request/{id}/approve', [StampCorrectionRequestController::class, 'approve'])
        ->name('stamp_correction_request.approve.execute');
    
    Route::get(
        '/admin/attendance/staff/{id}/csv',
        [AdminAttendanceController::class, 'exportStaffCsv']
    )->name('admin.attendance.staff.csv');
});
