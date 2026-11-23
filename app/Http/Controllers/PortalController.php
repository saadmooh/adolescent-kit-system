<?php

namespace App\Http\Controllers;

use App\Models\ActivityReport;
use App\Models\Attendance;
use App\Models\Beneficiary;
use App\Models\Institution;
use App\Models\KitCatalog;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\ReportAggregationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function __construct(
        protected ReportAggregationService $aggregationService
    ) {}

    /**
     * الواجهة الرئيسية للميسر في الميدان
     */
    public function index()
    {
        $user = Auth::user();

        // الجلسات الخاصة بالميسر الحالي
        $mySessions = TrainingSession::with(['institution', 'attendances'])
            ->where(function ($query) use ($user) {
                if ($user->role !== 'admin') {
                    $query->where('lead_facilitator_1_id', $user->id)
                          ->orWhere('lead_facilitator_2_id', $user->id);
                }
            })
            ->latest('session_date')
            ->paginate(10);

        return view('portal.index', compact('mySessions'));
    }

    /**
     * نموذج إنشاء جلسة جديدة مع الحقول المترابطة ذكياً
     */
    public function createSession()
    {
        $institutions = Institution::orderBy('name')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])->orderBy('name')->get();

        // قائمة الولايات والبلديات الجزائرية للربط التلقائي
        $wilayasData = [
            'الجزائر' => ['باب الوادي', 'بولوغين', 'الجزائر الوسطى', 'سيدي امحمد', 'الحراش', 'القبة', 'بئر مراد رايس', 'درارية', 'زرالدة'],
            'وهران' => ['وهران', 'السانية', 'بئر الجير', 'عين الترك', 'أرزيو', 'بطيوة', 'قديل'],
            'قسنطينة' => ['قسنطينة', 'الخروب', 'عين سمارة', 'حامة بوزيان', 'زيغود يوسف', 'ابن باديس'],
            'سطيف' => ['سطيف', 'العلمة', 'عين أرنات', 'عين الكبيرة', 'عين ولمان', 'بوقاعة'],
            'البليدة' => ['البليدة', 'بوفاريك', 'أولاد يعيش', 'العفرون', 'موزاية', 'بوقرة'],
            'تيزي وزو' => ['تيزي وزو', 'ذراع بن خدة', 'عزازقة', 'تيزي غنيف', 'بوغني', 'واقنون'],
        ];

        return view('portal.sessions.create', compact('institutions', 'facilitators', 'wilayasData'));
    }

    /**
     * حفظ الجلسة الجديدة
     */
    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'institution_id' => 'required|exists:institutions,id',
            'venue' => 'nullable|string|max:255',
            'wilaya' => 'required|string|max:100',
            'municipality' => 'required|string|max:100',
            'lead_facilitator_1_id' => 'required|exists:users,id',
            'lead_facilitator_2_id' => 'nullable|exists:users,id|different:lead_facilitator_1_id',
            'assistant_facilitators' => 'nullable|array',
            'assistant_facilitators.*' => 'nullable|string|max:150',
            'transit_from_municipality' => 'nullable|string|max:100',
            'transit_to_municipality' => 'nullable|string|max:100',
        ], [
            'lead_facilitator_2_id.different' => 'لا يمكن اختيار نفس الشخص كميسر رئيسي ومساعد في آن واحد.',
        ]);

        if (!empty($validated['assistant_facilitators'])) {
            $validated['assistant_facilitators'] = array_values(array_filter($validated['assistant_facilitators'], fn($val) => !empty(trim($val ?? ''))));
        }

        $session = TrainingSession::create($validated);

        // إنشاء مسودة تقرير النشاط تلقائياً
        ActivityReport::create([
            'training_session_id' => $session->id,
            'host_contact_name' => $session->institution->contact_person_name,
            'host_contact_title' => $session->institution->contact_person_title,
            'host_contact_email' => $session->institution->contact_person_email,
            'host_contact_phone' => $session->institution->contact_person_phone,
            'host_wilaya' => $session->wilaya,
            'rapporteur_id' => $session->lead_facilitator_1_id,
            'rapporteur_wilaya' => $session->wilaya,
            'rapporteur_date' => $session->session_date,
        ]);

        return redirect()->route('portal.sessions.attendance', $session)
            ->with('success', 'تم إنشاء الجلسة بنجاح! يمكنك الآن تسجيل كشف حضور اليافعين.');
    }

    /**
     * شاشة الإدخال الميداني السريع للحضور (Tablet / Mobile friendly) مع علاقات الحقول الذكية
     */
    public function attendance(TrainingSession $session)
    {
        $session->load('institution');
        
        $attendances = $session->attendances()
            ->with('beneficiary')
            ->orderBy('serial_number')
            ->get();

        $stats = $this->aggregationService->aggregate($session);

        return view('portal.attendance', compact('session', 'attendances', 'stats'));
    }

    /**
     * حفظ يافع جديد مع العلاقات الذكية بين الحقول (عمر، تمدرس، أمراض، إعاقة)
     */
    public function storeBeneficiary(Request $request, TrainingSession $session)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:ذكر,أنثى',
            'municipality' => 'required|string|max:100',
            'is_schooled' => 'required|boolean',
            'education_level' => 'required|string',
            'has_chronic_disease' => 'required|boolean',
            'chronic_disease_details' => 'nullable|string|max:255',
            'has_disability' => 'required|boolean',
            'disability_type' => 'nullable|string',
            'disability_details' => 'nullable|string|max:255',
        ]);

        // تصحيح منطقي: إذا كان غير متمدرس، يثبت المستوى كغير متمدرس
        $educationLevel = $request->boolean('is_schooled') ? $validated['education_level'] : 'غير متمدرس';

        $beneficiary = Beneficiary::create([
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'municipality' => $validated['municipality'],
            'education_level' => $educationLevel,
            'is_schooled' => $request->boolean('is_schooled'),
            'has_chronic_disease' => $request->boolean('has_chronic_disease'),
            'chronic_disease_details' => $request->boolean('has_chronic_disease') ? ($validated['chronic_disease_details'] ?? null) : null,
            'has_disability' => $request->boolean('has_disability'),
            'disability_type' => $request->boolean('has_disability') ? ($validated['disability_type'] ?? null) : null,
            'disability_details' => $request->boolean('has_disability') ? ($validated['disability_details'] ?? null) : null,
        ]);

        $nextSerial = ($session->attendances()->max('serial_number') ?? 0) + 1;

        Attendance::create([
            'training_session_id' => $session->id,
            'beneficiary_id' => $beneficiary->id,
            'serial_number' => $nextSerial,
            'attended' => true,
        ]);

        $this->aggregationService->syncReportCache($session);

        return redirect()->route('portal.sessions.attendance', $session)
            ->with('success', "تم إضافة اليافع ({$beneficiary->full_name}) وتأكيد حضوره برقم تسلسلي {$nextSerial}.");
    }

    /**
     * استمارة تقرير النشاط الميداني التفاعلي
     */
    public function report(TrainingSession $session)
    {
        $session->load(['institution', 'leadFacilitator1', 'leadFacilitator2']);

        $report = $session->activityReport ?? ActivityReport::create([
            'training_session_id' => $session->id,
            'host_contact_name' => $session->institution->contact_person_name,
            'host_contact_title' => $session->institution->contact_person_title,
            'host_contact_email' => $session->institution->contact_person_email,
            'host_contact_phone' => $session->institution->contact_person_phone,
            'host_wilaya' => $session->wilaya,
            'rapporteur_id' => $session->lead_facilitator_1_id,
            'rapporteur_wilaya' => $session->wilaya,
            'rapporteur_date' => $session->session_date,
        ]);

        $stats = $this->aggregationService->aggregate($session);
        $availableActivities = KitCatalog::activities()->get();
        $availableCompetencies = KitCatalog::competencies()->get();

        return view('portal.report', compact('session', 'report', 'stats', 'availableActivities', 'availableCompetencies'));
    }

    /**
     * شاشة تعديل بيانات جلسة التدريب
     */
    public function editSession(TrainingSession $session)
    {
        $institutions = Institution::orderBy('name')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])->orderBy('name')->get();

        $wilayasData = [
            'الجزائر' => ['باب الوادي', 'بولوغين', 'الجزائر الوسطى', 'سيدي امحمد', 'الحراش', 'القبة', 'بئر مراد رايس', 'درارية', 'زرالدة'],
            'وهران' => ['وهران', 'السانية', 'بئر الجير', 'عين الترك', 'أرزيو', 'بطيوة', 'قديل'],
            'قسنطينة' => ['قسنطينة', 'الخروب', 'عين سمارة', 'حامة بوزيان', 'زيغود يوسف', 'ابن باديس'],
            'سطيف' => ['سطيف', 'العلمة', 'عين أرنات', 'عين الكبيرة', 'عين ولمان', 'بوقاعة'],
            'البليدة' => ['البليدة', 'بوفاريك', 'أولاد يعيش', 'العفرون', 'موزاية', 'بوقرة'],
            'تيزي وزو' => ['تيزي وزو', 'ذراع بن خدة', 'عزازقة', 'تيزي غنيف', 'بوغني', 'واقنون'],
        ];

        return view('portal.sessions.edit', compact('session', 'institutions', 'facilitators', 'wilayasData'));
    }

    /**
     * تحديث بيانات جلسة التدريب
     */
    public function updateSession(Request $request, TrainingSession $session)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'institution_id' => 'required|exists:institutions,id',
            'venue' => 'nullable|string|max:255',
            'wilaya' => 'required|string|max:100',
            'municipality' => 'required|string|max:100',
            'lead_facilitator_1_id' => 'required|exists:users,id',
            'lead_facilitator_2_id' => 'nullable|exists:users,id|different:lead_facilitator_1_id',
            'assistant_facilitators' => 'nullable|array',
            'assistant_facilitators.*' => 'nullable|string|max:150',
            'transit_from_municipality' => 'nullable|string|max:100',
            'transit_to_municipality' => 'nullable|string|max:100',
            'status' => 'required|in:draft,ongoing,completed,approved',
        ], [
            'lead_facilitator_2_id.different' => 'لا يمكن اختيار نفس الشخص كميسر رئيسي ومساعد في آن واحد.',
        ]);

        if (!empty($validated['assistant_facilitators'])) {
            $validated['assistant_facilitators'] = array_values(array_filter($validated['assistant_facilitators'], fn($val) => !empty(trim($val ?? ''))));
        }

        $session->update($validated);

        if ($session->activityReport) {
            $session->activityReport->update([
                'host_wilaya' => $session->wilaya,
                'rapporteur_id' => $session->lead_facilitator_1_id,
                'rapporteur_wilaya' => $session->wilaya,
                'rapporteur_date' => $session->session_date,
            ]);
        }

        return redirect()->route('portal.sessions.edit', $session)
            ->with('success', 'تم تحديث بيانات الجلسة التدريبية بنجاح.');
    }

    /**
     * تعديل بيانات يافع مسجل في كشف الحضور
     */
    public function updateBeneficiary(Request $request, TrainingSession $session, Beneficiary $beneficiary)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:ذكر,أنثى',
            'municipality' => 'required|string|max:100',
            'is_schooled' => 'required|boolean',
            'education_level' => 'required|string',
            'has_chronic_disease' => 'required|boolean',
            'chronic_disease_details' => 'nullable|string|max:255',
            'has_disability' => 'required|boolean',
            'disability_type' => 'nullable|string',
            'disability_details' => 'nullable|string|max:255',
            'serial_number' => 'nullable|integer|min:1|max:70',
        ]);

        $educationLevel = $request->boolean('is_schooled') ? $validated['education_level'] : 'غير متمدرس';

        $beneficiary->update([
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'municipality' => $validated['municipality'],
            'education_level' => $educationLevel,
            'is_schooled' => $request->boolean('is_schooled'),
            'has_chronic_disease' => $request->boolean('has_chronic_disease'),
            'chronic_disease_details' => $request->boolean('has_chronic_disease') ? ($validated['chronic_disease_details'] ?? null) : null,
            'has_disability' => $request->boolean('has_disability'),
            'disability_type' => $request->boolean('has_disability') ? ($validated['disability_type'] ?? null) : null,
            'disability_details' => $request->boolean('has_disability') ? ($validated['disability_details'] ?? null) : null,
        ]);

        if (!empty($validated['serial_number'])) {
            $att = Attendance::where('training_session_id', $session->id)
                ->where('beneficiary_id', $beneficiary->id)
                ->first();
            if ($att) {
                $att->update(['serial_number' => $validated['serial_number']]);
            }
        }

        $this->aggregationService->syncReportCache($session);

        return redirect()->route('portal.sessions.attendance', $session)
            ->with('success', "تم تعديل بيانات اليافع ({$beneficiary->full_name}) بنجاح.");
    }

    /**
     * حذف حضور يافع من الجلسة التدريبية
     */
    public function destroyAttendance(TrainingSession $session, Attendance $attendance)
    {
        $beneficiaryName = $attendance->beneficiary->full_name ?? 'اليافع';
        $attendance->delete();

        $this->aggregationService->syncReportCache($session);

        return redirect()->route('portal.sessions.attendance', $session)
            ->with('success', "تم إزالة ({$beneficiaryName}) من كشف الحضور بنجاح.");
    }
}
