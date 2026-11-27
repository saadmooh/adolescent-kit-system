<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Institution;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\ReportAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * اللوحة الموحّدة للجميع: نفس الصفحة لكل المستخدمين،
 * وتظهر الأقسام حسب صلاحيات الدور المُسنَد dinamically
 */
class DashboardController extends Controller
{
    public function __construct(
        protected ReportAggregationService $aggregationService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $can = fn (string $permission): bool => $user->hasPermission($permission);

        $search = trim((string) $request->input('search')) ?: null;

        // ===== الأنشطة: الخاصة بي، أو كلها بصلاحية sessions.view_all =====
        $sessionsQuery = TrainingSession::with([
            'institution', 'leadFacilitator1', 'leadFacilitator2', 'attendances', 'activityReport',
        ]);

        if (!$can('sessions.view_all')) {
            $sessionsQuery->where(function ($query) use ($user) {
                $query->where('lead_facilitator_1_id', $user->id)
                    ->orWhere('lead_facilitator_2_id', $user->id)
                    ->orWhereJsonContains('assistant_facilitators', $user->name)
                    ->orWhereJsonContains('assistant_facilitators', (string) $user->id);
            });
        }

        if ($search) {
            $sessionsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('wilaya', 'like', "%{$search}%")
                    ->orWhere('municipality', 'like', "%{$search}%");
            });
        }

        $sessions = $sessionsQuery
            ->latest('session_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // بيانات تغذية النموذج متعدد الخطوات
        $institutions = Institution::orderBy('name')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $wilayasData = ReportAggregationService::WILAYAS_DATA;

        $data = [
            'user'         => $user,
            'can'          => $can,
            'permissions'  => $user->roleModel?->permissions->pluck('key')->toArray() ?? [],
            'sessions'     => $sessions,
            'search'       => $search,
            'institutions' => $institutions,
            'facilitators' => $facilitators,
            'wilayasData'  => $wilayasData,
        ];

        // ===== قسم الإحصائيات =====
        if ($can('stats.view')) {
            $data += [
                'totalSessions'      => TrainingSession::count(),
                'completedSessions'  => TrainingSession::where('status', 'completed')->count(),
                'approvedSessions'   => TrainingSession::where('status', 'approved')->count(),
                'totalBeneficiaries' => Beneficiary::count(),
                'maleCount'          => Beneficiary::where('gender', 'ذكر')->count(),
                'femaleCount'        => Beneficiary::where('gender', 'أنثى')->count(),
                'disabledCount'      => Beneficiary::where('has_disability', true)->count(),
                'chronicCount'       => Beneficiary::where('has_chronic_disease', true)->count(),
                'totalInstitutions'  => Institution::count(),
                'totalFacilitators'  => User::whereIn('role', ['facilitator', 'assistant', 'admin'])->count(),
            ];
        }

        // ===== طلبات التسجيل بانتظار الاعتماد =====
        if ($can('users.approve')) {
            $data['pendingUsers'] = User::where('approval_status', 'pending')->latest()->get();
        }

        // ===== إدارة المستخدمين =====
        if ($can('users.view')) {
            $data['allUsers'] = User::where('approval_status', '!=', 'pending')
                ->withCount('leadSessions')
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get();
            $data['roles'] = Role::orderBy('name')->get();
        }

        // ===== إدارة الأدوار والصلاحيات =====
        if ($can('roles.manage')) {
            $data['allRoles']       = Role::with('permissions')->get();
            $data['allPermissions'] = Permission::orderBy('group')->get()->groupBy('group');
        }

        // ===== اعتماد الجلسات =====
        if ($can('sessions.approve')) {
            $data['pendingApprovals'] = TrainingSession::with(['institution', 'leadFacilitator1', 'activityReport'])
                ->where('status', 'completed')
                ->latest('session_date')
                ->take(5)
                ->get();
        }

        return view('dashboard', $data);
    }
}
