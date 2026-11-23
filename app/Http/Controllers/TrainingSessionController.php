<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Http\Request;

class TrainingSessionController extends Controller
{
    public function index()
    {
        $sessions = TrainingSession::with(['institution', 'leadFacilitator1', 'attendances'])
            ->latest('session_date')
            ->paginate(15);

        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        $institutions = Institution::orderBy('name')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])->orderBy('name')->get();

        return view('sessions.create', compact('institutions', 'facilitators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'institution_id' => 'required|exists:institutions,id',
            'venue' => 'nullable|string|max:255',
            'wilaya' => 'required|string|max:100',
            'municipality' => 'required|string|max:100',
            'lead_facilitator_1_id' => 'nullable|exists:users,id',
            'lead_facilitator_2_id' => 'nullable|exists:users,id',
            'assistant_facilitators' => 'nullable|array',
            'assistant_facilitators.*' => 'nullable|string|max:150',
            'transit_from_municipality' => 'nullable|string|max:100',
            'transit_to_municipality' => 'nullable|string|max:100',
        ]);

        $session = TrainingSession::create($validated);

        return redirect()->route('sessions.show', $session)
            ->with('success', 'تم إنشاء الجلسة التدريبية بنجاح.');
    }

    public function show(TrainingSession $session)
    {
        $session->load(['institution', 'leadFacilitator1', 'leadFacilitator2', 'attendances.beneficiary', 'activityReport.mediaAttachments']);

        return view('sessions.show', compact('session'));
    }

    public function edit(TrainingSession $session)
    {
        $institutions = Institution::orderBy('name')->get();
        $facilitators = User::whereIn('role', ['facilitator', 'assistant', 'admin'])->orderBy('name')->get();

        return view('sessions.edit', compact('session', 'institutions', 'facilitators'));
    }

    public function update(Request $request, TrainingSession $session)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'institution_id' => 'required|exists:institutions,id',
            'venue' => 'nullable|string|max:255',
            'wilaya' => 'required|string|max:100',
            'municipality' => 'required|string|max:100',
            'lead_facilitator_1_id' => 'nullable|exists:users,id',
            'lead_facilitator_2_id' => 'nullable|exists:users,id',
            'assistant_facilitators' => 'nullable|array',
            'assistant_facilitators.*' => 'nullable|string|max:150',
            'transit_from_municipality' => 'nullable|string|max:100',
            'transit_to_municipality' => 'nullable|string|max:100',
            'status' => 'required|in:draft,ongoing,completed,approved',
        ]);

        $session->update($validated);

        return redirect()->route('sessions.show', $session)
            ->with('success', 'تم تحديث بيانات الجلسة بنجاح.');
    }

    public function destroy(TrainingSession $session)
    {
        $session->delete();

        return redirect()->route('sessions.index')
            ->with('success', 'تم حذف الجلسة بنجاح.');
    }
}
