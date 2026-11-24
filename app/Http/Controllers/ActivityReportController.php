<?php

namespace App\Http\Controllers;

use App\Models\ActivityReport;
use App\Models\TrainingSession;
use App\Services\ReportAggregationService;
use App\Traits\AuthorizesSessionAccess;
use Illuminate\Http\Request;

class ActivityReportController extends Controller
{
    use AuthorizesSessionAccess;

    public function __construct(
        protected ReportAggregationService $aggregationService
    ) {}

    public function update(Request $request, TrainingSession $session)
    {
        $this->authorizeSessionAccess($session);

        $report = $session->activityReport;

        $validated = $request->validate([
            'selected_activities' => 'nullable|array',
            'selected_competencies' => 'nullable|array',
            'facilities' => 'nullable|string',
            'difficulties' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'engagement_notes' => 'nullable|string',
            'attendance_notes' => 'nullable|string',
            'general_remarks' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'rapporteur_id' => 'nullable|exists:users,id',
            'rapporteur_wilaya' => 'nullable|string|max:100',
            'rapporteur_date' => 'nullable|date',
            'host_contact_name' => 'nullable|string|max:150',
            'host_contact_title' => 'nullable|string|max:150',
            'host_contact_phone' => 'nullable|string|max:50',
            'host_contact_email' => 'nullable|email|max:150',
            'host_wilaya' => 'nullable|string|max:100',
            'host_approval_date' => 'nullable|date',
        ]);

        $report->update($validated);

        // تحديث إحصائيات الكاش
        $this->aggregationService->syncReportCache($session);

        return redirect()->route('portal.sessions.report', $session)
            ->with('success', 'تم حفظ وتحديث تقرير النشاط بنجاح.');
    }
}
