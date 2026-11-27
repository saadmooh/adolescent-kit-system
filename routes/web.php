<?php

use App\Http\Controllers\ActivityReportController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TrainingSessionController;
use Illuminate\Support\Facades\Route;

// الصفحة الرئيسية (متاحة للجميع: للزوار والمستخدمين المسجلين على حد سواء)
Route::get('/', function () {
    return view('home');
})->name('home');

// ==========================================
// مصادقة الدخول والتسجيل (مع شرط موافقة المدير)
// ==========================================
$loginPath = config('app.admin_login_path', 'sys-access-auth-8492');

// مسار الدخول والتسجيل عبر الرابط المخصص
Route::get($loginPath, [AuthController::class, 'showLoginForm'])->name('login');
Route::post($loginPath, [AuthController::class, 'login'])->name('login.post');
Route::get($loginPath . '/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post($loginPath . '/register', [AuthController::class, 'register'])->name('register.post');

// مسارات مباشرة متوافقة
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login.direct');
Route::post('login', [AuthController::class, 'login'])->name('login.direct.post');
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register.direct');
Route::post('register', [AuthController::class, 'register'])->name('register.direct.post');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// اللوحة الموحّدة (نفس الصفحة لجميع المستخدمين)
// الأقسام تظهر حسب صلاحيات الدور المُسنَد
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // مسارات قديمة → تحويل للوحة الموحّدة (للروابط المحفوظة)
    Route::get('/admin/dashboard', fn () => redirect()->route('dashboard'))->name('admin.dashboard');
});

// ==========================================
// 1. إدارة النظام (المستخدمون والأدوار والجلسات) — محمية بالصلاحيات
// ==========================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // اعتماد الجلسات
    Route::post('sessions/{session}/approve', [AdminDashboardController::class, 'approveSession'])
        ->middleware('permission:sessions.approve')
        ->name('sessions.approve');

    // إدارة واعتماد قائمة الميسرين والمساعدين
    Route::get('facilitators', [FacilitatorController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('facilitators.index');
    Route::post('facilitators', [FacilitatorController::class, 'store'])
        ->middleware('permission:users.manage')
        ->name('facilitators.store');
    Route::put('facilitators/{user}', [FacilitatorController::class, 'update'])
        ->middleware('permission:users.manage')
        ->name('facilitators.update');
    Route::delete('facilitators/{user}', [FacilitatorController::class, 'destroy'])
        ->middleware('permission:users.manage')
        ->name('facilitators.destroy');
    Route::post('facilitators/{user}/approve', [FacilitatorController::class, 'approveRegistration'])
        ->middleware('permission:users.approve')
        ->name('facilitators.approve');
    Route::post('facilitators/{user}/reject', [FacilitatorController::class, 'rejectRegistration'])
        ->middleware('permission:users.approve')
        ->name('facilitators.reject');
    Route::post('facilitators/{user}/toggle-status', [FacilitatorController::class, 'toggleActiveStatus'])
        ->middleware('permission:users.manage')
        ->name('facilitators.toggleStatus');

    // إدارة الأدوار والصلاحيات ديناميكياً
    Route::get('roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.manage')
        ->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])
        ->middleware('permission:roles.manage')
        ->name('roles.store');
    Route::put('roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:roles.manage')
        ->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:roles.manage')
        ->name('roles.destroy');

    // توجيه جلسات الإدارة إلى جدول الجلسات المعتمد
    Route::get('sessions', [TrainingSessionController::class, 'index'])
        ->middleware('permission:sessions.view_all')
        ->name('sessions.index');
    Route::get('sessions/{session}/edit', [PortalController::class, 'editSession'])
        ->middleware('permission:sessions.edit')
        ->name('sessions.edit');
    Route::delete('sessions/{session}', [TrainingSessionController::class, 'destroy'])
        ->middleware('permission:sessions.delete')
        ->name('sessions.destroy');
});

// ==========================================
// 2. بوابة الإدخال الميداني (Field Entry Portal)
// ==========================================
Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
    // التحويل للوحة الموحّدة
    Route::get('/', fn () => redirect()->route('dashboard'))->name('dashboard');

    // إنشاء وتعديل وحذف جلسة تدريب
    Route::get('sessions/create', [PortalController::class, 'createSession'])
        ->middleware('permission:sessions.create')
        ->name('sessions.create');
    Route::post('sessions', [PortalController::class, 'storeSession'])
        ->middleware('permission:sessions.create')
        ->name('sessions.store');
    Route::get('sessions/{session}/edit', [PortalController::class, 'editSession'])
        ->middleware('permission:sessions.edit')
        ->name('sessions.edit');
    Route::put('sessions/{session}', [PortalController::class, 'updateSession'])
        ->middleware('permission:sessions.edit')
        ->name('sessions.update');
    Route::delete('sessions/{session}', [PortalController::class, 'destroySession'])
        ->middleware('permission:sessions.delete')
        ->name('sessions.destroy');

    // كشف الحضور الميداني التفاعلي وتعديل اليافعين
    Route::get('sessions/{session}/attendance', [PortalController::class, 'attendance'])
        ->middleware('permission:attendance.view')
        ->name('sessions.attendance');
    Route::post('sessions/{session}/attendance/add', [PortalController::class, 'storeBeneficiary'])
        ->middleware('permission:attendance.manage')
        ->name('sessions.attendance.add');
    Route::put('sessions/{session}/beneficiaries/{beneficiary}', [PortalController::class, 'updateBeneficiary'])
        ->middleware('permission:attendance.manage')
        ->name('sessions.beneficiaries.update');
    Route::delete('sessions/{session}/attendance/{attendance}', [PortalController::class, 'destroyAttendance'])
        ->middleware('permission:attendance.manage')
        ->name('sessions.attendance.destroy');
    Route::post('sessions/{session}/attendance/{attendance}/toggle', [AttendanceController::class, 'toggleStatus'])
        ->middleware('permission:attendance.manage')
        ->name('sessions.attendance.toggle');

    // تقرير النشاط والتوثيق الإعلامي
    Route::get('sessions/{session}/report', [PortalController::class, 'report'])
        ->middleware('permission:reports.view')
        ->name('sessions.report');
    Route::put('sessions/{session}/report', [ActivityReportController::class, 'update'])
        ->middleware('permission:reports.manage')
        ->name('sessions.report.update');
});

// رفع المرفقات الإعلامية
Route::post('reports/{report}/media', [MediaController::class, 'store'])
    ->name('reports.media.store')
    ->middleware(['auth', 'permission:media.upload']);
Route::delete('media/{media}', [MediaController::class, 'destroy'])
    ->name('media.destroy')
    ->middleware(['auth', 'permission:media.upload']);

// الطباعة والتصدير المطابق للأصل (محمية بالصلاحيات)
Route::middleware(['auth', 'permission:exports.print'])->group(function () {
    Route::get('sessions/{session}/print-attendance', [ExportController::class, 'printAttendance'])->name('sessions.print.attendance');
    Route::get('sessions/{session}/print-report', [ExportController::class, 'printReport'])->name('sessions.print.report');
});
