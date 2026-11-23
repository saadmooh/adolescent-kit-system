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

// إعادة التوجيه الذكية من الصفحة الرئيسية
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return Auth::user()->role === 'admin' 
        ? redirect()->route('admin.dashboard')
        : redirect()->route('portal.dashboard');
})->name('home');

// مصادقة الدخول والخروج (Real Authentication)
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 1. لوحة التحكم الإدارية (Admin Control Panel)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('sessions/{session}/approve', [AdminDashboardController::class, 'approveSession'])->name('sessions.approve');
    
    // إدارة قائمة الميسرين والمساعدين
    Route::get('facilitators', [FacilitatorController::class, 'index'])->name('facilitators.index');
    Route::post('facilitators', [FacilitatorController::class, 'store'])->name('facilitators.store');
    Route::put('facilitators/{user}', [FacilitatorController::class, 'update'])->name('facilitators.update');
    Route::delete('facilitators/{user}', [FacilitatorController::class, 'destroy'])->name('facilitators.destroy');

    // إدارة كافة الجلسات من قبل الإدارة
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
