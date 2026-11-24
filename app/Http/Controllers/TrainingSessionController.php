<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use Illuminate\Http\Request;

class TrainingSessionController extends Controller
{
    public function index()
    {
        return redirect()->to(route('admin.dashboard') . '#sessions-table');
    }

    public function destroy(TrainingSession $session)
    {
        $session->delete();

        return redirect()->to(route('admin.dashboard') . '#sessions-table')
            ->with('success', 'تم حذف الدورة التدريبية بنجاح.');
    }
}
