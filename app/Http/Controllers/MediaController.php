<?php

namespace App\Http\Controllers;

use App\Models\ActivityReport;
use App\Models\MediaAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function store(Request $request, ActivityReport $report)
    {
        $validated = $request->validate([
            'category' => 'required|in:institution_gate,facilitators_team,group_photo,in_action,activity_video,extra',
            'file' => 'required|file|max:102400', // حتى 100 ميجابايت (فيديو أو صور)
        ]);

        $file = $request->file('file');
        $path = $file->store("reports/{$report->id}", 'public');

        MediaAttachment::create([
            'activity_report_id' => $report->id,
            'category' => $validated['category'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'تم رفع المرفق الإعلامي بنجاح.');
    }

    public function destroy(MediaAttachment $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return back()->with('success', 'تم حذف المرفق.');
    }
}
