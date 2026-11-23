<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Beneficiary;
use App\Models\TrainingSession;
use App\Services\ReportAggregationService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected ReportAggregationService $aggregationService
    ) {}

    public function index(TrainingSession $session)
    {
        $session->load('institution');
        
        $attendances = $session->attendances()
            ->with('beneficiary')
            ->orderBy('serial_number')
            ->get();

        $stats = $this->aggregationService->aggregate($session);

        return view('attendance.index', compact('session', 'attendances', 'stats'));
    }

    public function storeBeneficiaryAndAttend(Request $request, TrainingSession $session)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'birth_date' => 'required|date',
            'gender' => 'required|in:ذكر,أنثى',
            'municipality' => 'required|string|max:100',
            'education_level' => 'required|string|in:ابتدائي,متوسط,ثانوي,تكوين مهني,غير متمدرس',
            'is_schooled' => 'boolean',
            'has_chronic_disease' => 'boolean',
            'chronic_disease_details' => 'nullable|string|max:255',
            'has_disability' => 'boolean',
            'disability_type' => 'nullable|string|in:حركية,بصرية,الصم البكم,شيء آخر',
            'disability_details' => 'nullable|string|max:255',
        ]);

        $beneficiary = Beneficiary::create([
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'municipality' => $validated['municipality'],
            'education_level' => $validated['education_level'],
            'is_schooled' => $request->boolean('is_schooled', true),
            'has_chronic_disease' => $request->boolean('has_chronic_disease', false),
            'chronic_disease_details' => $validated['chronic_disease_details'] ?? null,
            'has_disability' => $request->boolean('has_disability', false),
            'disability_type' => $validated['disability_type'] ?? null,
            'disability_details' => $validated['disability_details'] ?? null,
        ]);

        $nextSerial = ($session->attendances()->max('serial_number') ?? 0) + 1;

        Attendance::create([
            'training_session_id' => $session->id,
            'beneficiary_id' => $beneficiary->id,
            'serial_number' => $nextSerial,
            'attended' => true,
        ]);

        // تحديث إحصائيات التقرير
        $this->aggregationService->syncReportCache($session);

        return redirect()->route('sessions.attendance.index', $session)
            ->with('success', 'تم تسجيل اليافع وإضافته لقائمة الحضور بنجاح.');
    }

    public function toggleStatus(Request $request, TrainingSession $session, Attendance $attendance)
    {
        $attendance->update([
            'attended' => !$attendance->attended,
        ]);

        $this->aggregationService->syncReportCache($session);

        if ($request->wantsJson()) {
            $stats = $this->aggregationService->aggregate($session);
            return response()->json([
                'success' => true,
                'attended' => $attendance->attended,
                'stats' => $stats,
            ]);
        }

        return back()->with('success', 'تم تعديل حالة الحضور.');
    }
}
