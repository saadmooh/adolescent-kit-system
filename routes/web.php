<?php

use App\Http\Controllers\ActivityReportController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\TrainingSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// الصفحة الرئيسية (Public Showcase للزوار العاديين / توجيه آلي للمسجلين)
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin' 
            ? redirect()->route('admin.dashboard')
            : redirect()->route('portal.dashboard');
    }

    $stats = [
        'beneficiaries' => \App\Models\Beneficiary::count(),
        'sessions' => \App\Models\TrainingSession::whereIn('status', ['approved', 'completed'])->count(),
        'institutions' => \App\Models\Institution::count(),
    ];

    return view('home', compact('stats'));
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
// 1. لوحة التحكم الإدارية (Admin Control Panel)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('sessions/{session}/approve', [AdminDashboardController::class, 'approveSession'])->name('sessions.approve');
    
    // إدارة واعتماد قائمة الميسرين والمساعدين
    Route::get('facilitators', [FacilitatorController::class, 'index'])->name('facilitators.index');
    Route::post('facilitators', [FacilitatorController::class, 'store'])->name('facilitators.store');
    Route::put('facilitators/{user}', [FacilitatorController::class, 'update'])->name('facilitators.update');
    Route::delete('facilitators/{user}', [FacilitatorController::class, 'destroy'])->name('facilitators.destroy');
    Route::post('facilitators/{user}/approve', [FacilitatorController::class, 'approveRegistration'])->name('facilitators.approve');
    Route::post('facilitators/{user}/reject', [FacilitatorController::class, 'rejectRegistration'])->name('facilitators.reject');
    Route::post('facilitators/{user}/toggle-status', [FacilitatorController::class, 'toggleActiveStatus'])->name('facilitators.toggleStatus');

    // توجيه جلسات الإدارة إلى جدول الجلسات المعتمد في اللوحة
    Route::get('sessions', [TrainingSessionController::class, 'index'])->name('sessions.index');
    Route::get('sessions/{session}/edit', [PortalController::class, 'editSession'])->name('sessions.edit');
    Route::delete('sessions/{session}', [TrainingSessionController::class, 'destroy'])->name('sessions.destroy');
});

// ==========================================
// 2. بوابة الإدخال الميداني (Field Entry Portal)
// ==========================================
Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalController::class, 'index'])->name('dashboard');
    
    // إنشاء وتعديل جلسة تدريب
    Route::get('sessions/create', [PortalController::class, 'createSession'])->name('sessions.create');
    Route::post('sessions', [PortalController::class, 'storeSession'])->name('sessions.store');
    Route::get('sessions/{session}/edit', [PortalController::class, 'editSession'])->name('sessions.edit');
    Route::put('sessions/{session}', [PortalController::class, 'updateSession'])->name('sessions.update');
    
    // كشف الحضور الميداني التفاعلي وتعديل اليافعين
    Route::get('sessions/{session}/attendance', [PortalController::class, 'attendance'])->name('sessions.attendance');
    Route::post('sessions/{session}/attendance/add', [PortalController::class, 'storeBeneficiary'])->name('sessions.attendance.add');
    Route::put('sessions/{session}/beneficiaries/{beneficiary}', [PortalController::class, 'updateBeneficiary'])->name('sessions.beneficiaries.update');
    Route::delete('sessions/{session}/attendance/{attendance}', [PortalController::class, 'destroyAttendance'])->name('sessions.attendance.destroy');
    Route::post('sessions/{session}/attendance/{attendance}/toggle', [AttendanceController::class, 'toggleStatus'])->name('sessions.attendance.toggle');
    
    // تقرير النشاط والتوثيق الإعلامي
    Route::get('sessions/{session}/report', [PortalController::class, 'report'])->name('sessions.report');
    Route::put('sessions/{session}/report', [ActivityReportController::class, 'update'])->name('sessions.report.update');
});

// رفع المرفقات الإعلامية
Route::post('reports/{report}/media', [MediaController::class, 'store'])->name('reports.media.store')->middleware('auth');
Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy')->middleware('auth');

// الطباعة والتصدير المطابق للأصل (محمية بالمصادقة والتحقق من الصلاحيات)
Route::middleware(['auth'])->group(function () {
    Route::get('sessions/{session}/print-attendance', [ExportController::class, 'printAttendance'])->name('sessions.print.attendance');
    Route::get('sessions/{session}/print-report', [ExportController::class, 'printReport'])->name('sessions.print.report');
});
