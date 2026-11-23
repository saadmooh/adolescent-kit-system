<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\TrainingSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSessions = TrainingSession::count();
        $totalBeneficiaries = Beneficiary::count();
        $maleCount = Beneficiary::where('gender', 'ذكر')->count();
        $femaleCount = Beneficiary::where('gender', 'أنثى')->count();
        $disabledCount = Beneficiary::where('has_disability', true)->count();
        $chronicCount = Beneficiary::where('has_chronic_disease', true)->count();

        $recentSessions = TrainingSession::with(['institution', 'leadFacilitator1'])
            ->latest('session_date')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSessions',
            'totalBeneficiaries',
            'maleCount',
            'femaleCount',
            'disabledCount',
            'chronicCount',
            'recentSessions'
        ));
    }
}
