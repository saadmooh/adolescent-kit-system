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

        // المعلومات الأساسية (الميسرون وجهة الاتصال والولاية والتواريخ) مشتقة من الجلسة والمؤسسة
        // ولا تُعدَّل من هذا النموذج، لذا تُستبعد من التحقق لمنع تجاوزها عبر طلب مزيف.
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
        ]);

        $report->update($validated);

        // تحديث إحصائيات الكاش
        $this->aggregationService->syncReportCache($session);

        return redirect()->route('portal.sessions.report', $session)
            ->with('success', 'تم حفظ وتحديث تقرير النشاط بنجاح.');
    }
}
