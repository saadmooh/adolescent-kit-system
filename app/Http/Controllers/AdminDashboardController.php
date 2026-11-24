<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Institution;
use App\Models\KitCatalog;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalSessions = TrainingSession::count();
        $completedSessions = TrainingSession::where('status', 'completed')->count();
        $approvedSessions = TrainingSession::where('status', 'approved')->count();
        
        $totalBeneficiaries = Beneficiary::count();
        $maleCount = Beneficiary::where('gender', 'ذكر')->count();
        $femaleCount = Beneficiary::where('gender', 'أنثى')->count();
        $disabledCount = Beneficiary::where('has_disability', true)->count();
        $chronicCount = Beneficiary::where('has_chronic_disease', true)->count();

        $totalInstitutions = Institution::count();
        $totalFacilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])->count();
        $activitiesCount = KitCatalog::where('type', 'activity')->count();
        $competenciesCount = KitCatalog::where('type', 'competency')->count();

        // طلبات التسجيل الجديدة المعلقة بانتظار قبول وموافقة المدير
        $pendingUsers = User::where('approval_status', 'pending')
            ->latest()
            ->get();

        // قائمة الميسرين لعرضها وإدارتها مباشرة من لوحة التحكم
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])
            ->withCount('leadSessions')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        // الجلسات التي بانتظار الاعتماد الإداري
        $pendingApprovals = TrainingSession::with(['institution', 'leadFacilitator1', 'activityReport'])
            ->where('status', 'completed')
            ->latest('session_date')
            ->take(5)
            ->get();

        // كافة الجلسات الأخيرة
        $recentSessions = TrainingSession::with(['institution', 'leadFacilitator1', 'attendances'])
            ->latest('session_date')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalSessions',
            'completedSessions',
            'approvedSessions',
            'totalBeneficiaries',
            'maleCount',
            'femaleCount',
            'disabledCount',
            'chronicCount',
            'totalInstitutions',
            'totalFacilitators',
            'activitiesCount',
            'competenciesCount',
            'pendingUsers',
            'facilitators',
            'pendingApprovals',
            'recentSessions'
        ));
    }

    public function approveSession(TrainingSession $session)
    {
        $session->update([
            'status' => 'approved',
            'institution_approved' => true,
            'institution_approved_at' => now(),
        ]);

        return back()->with('success', "تم اعتماد الجلسة رقم #{$session->id} بنجاح.");
    }
}
