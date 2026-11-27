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
use App\Traits\AuthorizesSessionAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    use AuthorizesSessionAccess;

    public function __construct(
        protected ReportAggregationService $aggregationService
    ) {}

    /**
     * الواجهة الرئيسية للميسر في الميدان: قائمة الأنشطة مرتبة تنازلياً (الجديد أولاً) مع البحث والمودال
     */
    public function index()
    {
        $user = Auth::user();

        // الجلسات الخاصة بالميسر الحالي مرتبة تنازلياً (الأحدث هو الأول دائماً)
        $mySessions = TrainingSession::with(['institution', 'leadFacilitator1', 'leadFacilitator2', 'attendances', 'activityReport'])
            ->where(function ($query) use ($user) {
                if ($user->role !== 'admin') {
                    $query->where('lead_facilitator_1_id', $user->id)
                          ->orWhere('lead_facilitator_2_id', $user->id)
                          ->orWhereJsonContains('assistant_facilitators', $user->name);
                }
            })
            ->latest('session_date')
            ->latest('id')
            ->get();

        // بيانات المؤسسات والميسرين والولايات لتغذية Multi-step Form Modal مباشرة
        $institutions = Institution::orderBy('name')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $wilayasData = ReportAggregationService::WILAYAS_DATA;

        return view('portal.index', compact('mySessions', 'institutions', 'facilitators', 'wilayasData'));
    }

    /**
     * حذف النشاط / الجلسة التدريبية
     */
    public function destroySession(TrainingSession $session)
    {
        $this->authorizeSessionAccess($session);

        if ($session->status === 'approved' && !auth()->user()->hasPermission('sessions.approve')) {
            return back()->withErrors(['error' => 'لا يمكنك حذف جلسة تم اعتمادها ومصادقتها رسمياً من طرف الإدارة.']);
        }

        $title = $session->title;
        $session->delete();

        return redirect()->route('portal.dashboard')
            ->with('success', "تم حذف النشاط ({$title}) بنجاح.");
    }

    /**
     * نموذج إنشاء جلسة جديدة مع الحقول المترابطة ذكياً
     */
    public function createSession()
    {
        $institutions = Institution::orderBy('name')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])->orderBy('name')->get();

        // قائمة الولايات والبلديات الجزائرية للربط التلقائي
        $wilayasData = ReportAggregationService::WILAYAS_DATA;

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
            'institution_name' => 'required_without:institution_id|nullable|string|max:255',
            'institution_id' => 'required_without:institution_name|nullable|exists:institutions,id',
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
            'institution_name.required_without' => 'يرجى إدخال اسم المؤسسة / الهيئة المستقبلة.',
        ]);

        // خانة الإدخال المباشر: تحويل اسم المؤسسة المُدخل إلى سجل مؤسسة (إنشاء تلقائي عند عدم وجوده)
        if (!empty(trim((string) ($validated['institution_name'] ?? '')))) {
            $validated['institution_id'] = Institution::firstOrCreate([
                'name' => trim($validated['institution_name']),
            ])->id;
        }
        unset($validated['institution_name']);

        if (!empty($validated['assistant_facilitators'])) {
            $assistants = array_filter($validated['assistant_facilitators'], fn($val) => !empty(trim((string) $val)));
            $validated['assistant_facilitators'] = array_values(array_unique($assistants));
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
        $this->authorizeSessionAccess($session);

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
        $this->authorizeSessionAccess($session);

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:ذكر,أنثى',
            'municipality' => 'required|string|max:100',
            'is_schooled' => 'required|boolean',
            'education_level' => 'required|string|in:ابتدائي,متوسط,ثانوي,تكوين مهني,غير متمدرس',
            'has_chronic_disease' => 'required|boolean',
            'chronic_disease_details' => 'required_if:has_chronic_disease,1|nullable|string|max:255',
            'has_disability' => 'required|boolean',
            'disability_type' => 'required_if:has_disability,1|nullable|in:حركية,بصرية,الصم البكم,شيء آخر',
            'disability_details' => 'nullable|string|max:255',
        ], [
            'education_level.in' => 'يرجى تحديد المستوى الدراسي من القائمة المعتمدة.',
            'chronic_disease_details.required_if' => 'يرجى تحديد نوع وتفاصيل المرض المزمن عند الإشارة إلى وجوده.',
            'disability_type.required_if' => 'يرجى تحديد نوع الإعاقة عند الإشارة إلى وجودها.',
            'disability_type.in' => 'يرجى تحديد نوع الإعاقة من القائمة المعتمدة.',
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
        $this->authorizeSessionAccess($session);

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
        $this->authorizeSessionAccess($session);

        $institutions = Institution::orderBy('name')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])->orderBy('name')->get();

        $wilayasData = ReportAggregationService::WILAYAS_DATA;

        return view('portal.sessions.edit', compact('session', 'institutions', 'facilitators', 'wilayasData'));
    }

    /**
     * تحديث بيانات جلسة التدريب
     */
    public function updateSession(Request $request, TrainingSession $session)
    {
        $this->authorizeSessionAccess($session);

        // حماية أمنية: منع تصعيد الصلاحيات واعتماد الجلسة إلا لمن يملك صلاحية الاعتماد
        $statusRule = Auth::user()->hasPermission('sessions.approve')
            ? 'required|in:draft,ongoing,completed,approved'
            : 'required|in:draft,ongoing,completed';

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'institution_name' => 'required_without:institution_id|nullable|string|max:255',
            'institution_id' => 'required_without:institution_name|nullable|exists:institutions,id',
            'venue' => 'nullable|string|max:255',
            'wilaya' => 'required|string|max:100',
            'municipality' => 'required|string|max:100',
            'lead_facilitator_1_id' => 'required|exists:users,id',
            'lead_facilitator_2_id' => 'nullable|exists:users,id|different:lead_facilitator_1_id',
            'assistant_facilitators' => 'nullable|array',
            'assistant_facilitators.*' => 'nullable|string|max:150',
            'transit_from_municipality' => 'nullable|string|max:100',
            'transit_to_municipality' => 'nullable|string|max:100',
            'status' => $statusRule,
        ], [
            'lead_facilitator_2_id.different' => 'لا يمكن اختيار نفس الشخص كميسر رئيسي ومساعد في آن واحد.',
            'institution_name.required_without' => 'يرجى إدخال اسم المؤسسة / الهيئة المستقبلة.',
            'status.in' => 'غير مصرح لك باعتماد الجلسة رسمياً؛ فقط إدارة النظام تملك صلاحية الاعتماد.',
        ]);

        // خانة الإدخال المباشر: تحويل اسم المؤسسة المُدخل إلى سجل مؤسسة (إنشاء تلقائي عند عدم وجوده)
        if (!empty(trim((string) ($validated['institution_name'] ?? '')))) {
            $validated['institution_id'] = Institution::firstOrCreate([
                'name' => trim($validated['institution_name']),
            ])->id;
        }
        unset($validated['institution_name']);

        if (!empty($validated['assistant_facilitators'])) {
            $assistants = array_filter($validated['assistant_facilitators'], fn($val) => !empty(trim((string) $val)));
            $validated['assistant_facilitators'] = array_values(array_unique($assistants));
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
        $this->authorizeSessionAccess($session);

        // حماية IDOR: التحقق من أن اليافع مسجل فعلياً في كشف حضور هذه الجلسة
        if (!$session->attendances()->where('beneficiary_id', $beneficiary->id)->exists()) {
            abort(404, 'اليافع المحدد غير مسجل في كشف حضور هذه الجلسة.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:ذكر,أنثى',
            'municipality' => 'required|string|max:100',
            'is_schooled' => 'required|boolean',
            'education_level' => 'required|string|in:ابتدائي,متوسط,ثانوي,تكوين مهني,غير متمدرس',
            'has_chronic_disease' => 'required|boolean',
            'chronic_disease_details' => 'required_if:has_chronic_disease,1|nullable|string|max:255',
            'has_disability' => 'required|boolean',
            'disability_type' => 'required_if:has_disability,1|nullable|in:حركية,بصرية,الصم البكم,شيء آخر',
            'disability_details' => 'nullable|string|max:255',
            'serial_number' => 'nullable|integer|min:1|max:70',
        ], [
            'education_level.in' => 'يرجى تحديد المستوى الدراسي من القائمة المعتمدة.',
            'chronic_disease_details.required_if' => 'يرجى تحديد نوع وتفاصيل المرض المزمن عند الإشارة إلى وجوده.',
            'disability_type.required_if' => 'يرجى تحديد نوع الإعاقة عند الإشارة إلى وجودها.',
            'disability_type.in' => 'يرجى تحديد نوع الإعاقة من القائمة المعتمدة.',
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
        $this->authorizeSessionAccess($session);

        // حماية IDOR: التحقق من أن سجل الحضور يتبع لهذه الجلسة
        if ($attendance->training_session_id !== $session->id) {
            abort(404, 'سجل الحضور لا يتبع لهذه الجلسة.');
        }

        $beneficiaryName = $attendance->beneficiary->full_name ?? 'اليافع';
        $attendance->delete();

        $this->aggregationService->syncReportCache($session);

        return redirect()->route('portal.sessions.attendance', $session)
            ->with('success', "تم إزالة ({$beneficiaryName}) من كشف الحضور بنجاح.");
    }
}
