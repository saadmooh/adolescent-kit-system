<?php

use App\Models\TrainingSession;
use App\Services\ReportAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// حماية واجهات الـ API: منع التسريب غير المصرح به لبيانات اليافعين والمستفيدين
Route::middleware(['auth'])->group(function () {
    Route::get('/sessions', function () {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return TrainingSession::with(['institution', 'attendances.beneficiary'])->get();
        }

        return TrainingSession::where('lead_facilitator_1_id', $user->id)
            ->orWhere('lead_facilitator_2_id', $user->id)
            ->with(['institution', 'attendances.beneficiary'])
            ->get();
    });

    Route::get('/sessions/{session}/stats', function (TrainingSession $session, ReportAggregationService $service) {
        $user = auth()->user();
        $isAllowed = ($user->role === 'admin')
            || ($session->lead_facilitator_1_id === $user->id)
            || ($session->lead_facilitator_2_id === $user->id);

        if (!$isAllowed) {
            abort(403, 'غير مصرح لك بالوصول لإحصائيات هذه الجلسة.');
        }

        return response()->json($service->aggregate($session));
    });
});
