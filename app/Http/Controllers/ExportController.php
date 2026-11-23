<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Services\ReportAggregationService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function __construct(
        protected ReportAggregationService $aggregationService
    ) {}

    /**
     * عرض وطباعة قائمة حضور اليافعين مطابقة للنموذج الرسمي
     */
    public function printAttendance(TrainingSession $session)
    {
        $session->load(['institution', 'leadFacilitator1', 'leadFacilitator2']);

        $attendances = $session->attendances()
            ->with('beneficiary')
            ->orderBy('serial_number')
            ->get();

        return view('exports.attendance_print', compact('session', 'attendances'));
    }

    /**
     * عرض وطباعة تقرير النشاط الشامل مطابق للنموذج الرسمي
     */
    public function printReport(TrainingSession $session)
    {
        $session->load(['institution', 'leadFacilitator1', 'leadFacilitator2', 'activityReport.mediaAttachments']);
        $report = $session->activityReport;

        $stats = $this->aggregationService->aggregate($session);

        return view('exports.report_print', compact('session', 'report', 'stats'));
    }
}
