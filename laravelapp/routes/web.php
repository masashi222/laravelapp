<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceUserController;
use App\Http\Middleware\PageAuthenticationMiddleware;
use App\Http\Middleware\PageAuthenticationStaffMiddleware;

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

Route::get('/', function () {
    return view('welcome');
});


// 勤怠修正・登録画面（管理者）
    Route::get('/admin/attendance-update/{id?}/{stampid?}','AttendanceAdminController@update')
    ->middleware(PageAuthenticationMiddleware::class)->name('attendance.update');
    Route::post('/admin/attendance-update/{id?}/{stampid?}','AttendanceAdminController@fix');
    Route::get('/admin/attendance-register/{id?}','AttendanceAdminController@add')
    ->middleware(PageAuthenticationMiddleware::class)->name('attendance.register');
    Route::post('/admin/attendance-register/{id?}','AttendanceAdminController@create');

// 勤怠一覧画面（管理者）
    Route::get('/admin/attendance-record/{id?}','AttendanceAdminController@record')
    ->middleware(PageAuthenticationMiddleware::class)->name('attendance.record');
    Route::get('/admin/attendance-record-last/{id?}','AttendanceAdminController@recordLast')
    ->middleware(PageAuthenticationMiddleware::class);
    Route::get('/admin/attendance-record-next/{id?}','AttendanceAdminController@recordNext')
    ->middleware(PageAuthenticationMiddleware::class);
    Route::post('/admin/attendance-record/{id?}','SalaryConfirmController@create');
    Route::get('/admin/salary-confirm','SalaryConfirmController@confirm');

// 勤怠管理画面（管理者）
    Route::get('/admin/attendance-staff-record','AttendanceAdminController@show')
    ->middleware(PageAuthenticationMiddleware::class);

// 給与計算書
    Route::get('/admin/payroll','AttendanceAdminController@payroll')
    ->middleware(PageAuthenticationMiddleware::class);

// シフト作成（管理者）
    Route::get('/admin/shift-create','ShiftAdminController@show')
    ->middleware(PageAuthenticationMiddleware::class);
    Route::post('/admin/shift-create','ShiftAdminController@create');
    Route::get('/admin/shift-create2','ShiftAdminController@show2')
    ->middleware(PageAuthenticationMiddleware::class);
    Route::post('/admin/shift-create2','ShiftAdminController@create2');

// シフト一覧画面（管理者）
    Route::get('/admin/shift-record','ShiftAdminController@record')
    ->middleware(PageAuthenticationMiddleware::class);

// 従業員一覧（管理者）
    Route::get('/admin/staff-record','StaffController@record')
    ->middleware(PageAuthenticationMiddleware::class);

// 従業員修正（管理者）
    Route::get('/admin/staff-fix/{id?}','StaffController@show')
    ->middleware(PageAuthenticationMiddleware::class);
    Route::post('/admin/staff-fix/{id?}','StaffController@update');

// 従業員登録画面（管理者）
    Route::get('/admin/staff-register','StaffController@add')
    ->middleware(PageAuthenticationMiddleware::class);
    Route::post('/admin/staff-register','StaffController@create');

//　勤怠打刻（管理者）
    Route::get('/admin/pass-create','KeyGenerateController@create');
    Route::get('/admin/stamp-pass','KeyGenerateController@show')
    ->middleware(PageAuthenticationMiddleware::class);

// 勤怠一覧画面
    Route::get('/staff/attendance-record','AttendanceUserController@record')
    ->middleware(PageAuthenticationStaffMiddleware::class);
    Route::get('/staff/attendance-record-last','AttendanceUserController@recordLast')
    ->middleware(PageAuthenticationStaffMiddleware::class);
    Route::get('/staff/attendance-record-next','AttendanceUserController@recordNext')
    ->middleware(PageAuthenticationStaffMiddleware::class);

// シフト作成
    Route::get('/staff/shift-create','ShiftUserController@add')
    ->middleware(PageAuthenticationStaffMiddleware::class);
    Route::post('/staff/shift-create','ShiftUserController@create');

// シフト一覧
    Route::get('/staff/shift-record','ShiftUserController@record')
    ->middleware(PageAuthenticationStaffMiddleware::class);

// 勤怠打刻
    Route::get('/staff/stamp','StampController@show')
    ->middleware(PageAuthenticationStaffMiddleware::class);
    Route::post('/staff/stamp','StampController@stamp');

// ログイン画面
    Auth::routes();
    Route::get('/home', 'HomeController@index')->name('home');

// ログアウト
    Route::get('/logout', 'LogoutController@logout');
