<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Services\ReportAggregationService;
use App\Traits\AuthorizesSessionAccess;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use AuthorizesSessionAccess;

    public function __construct(
        protected ReportAggregationService $aggregationService
    ) {}

    public function toggleStatus(Request $request, TrainingSession $session, Attendance $attendance)
    {
        $this->authorizeSessionAccess($session);

        if ($attendance->training_session_id !== $session->id) {
            abort(404, 'سجل الحضور لا يتبع لهذه الجلسة.');
        }

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
