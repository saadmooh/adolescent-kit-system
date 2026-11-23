<?php

use App\Models\TrainingSession;
use App\Services\ReportAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/sessions', function () {
    return TrainingSession::with(['institution', 'attendances.beneficiary'])->get();
});

Route::get('/sessions/{session}/stats', function (TrainingSession $session, ReportAggregationService $service) {
    return response()->json($service->aggregate($session));
});
