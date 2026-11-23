<?php

namespace App\Http\Controllers;

use App\Models\ActivityReport;
use App\Models\MediaAttachment;
use App\Traits\AuthorizesSessionAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    use AuthorizesSessionAccess;

    public function store(Request $request, ActivityReport $report)
    {
        // حماية BOLA / IDOR: التحقق من صلاحية المستخدم على الجلسة التابع لها التقرير
        $session = $report->trainingSession;
        $this->authorizeSessionAccess($session);

        $validated = $request->validate([
            'category' => 'required|in:institution_gate,facilitators_team,group_photo,in_action,activity_video,extra',
        ]);

        $category = $validated['category'];

        // حماية من رفع الملفات الخبيثة (Strict MIME and Extension Whitelist)
        if ($category === 'activity_video') {
            $request->validate([
                'file' => 'required|file|mimes:mp4,mov,avi,webm,mkv|mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo|max:102400',
            ], [
                'file.mimes' => 'ملف الفيديو يجب أن يكون بصيغة صالحة (mp4, mov, avi, webm, mkv).',
                'file.max' => 'الحد الأقصى لحجم الفيديو هو 100 ميغابايت.',
            ]);
        } else {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,jpg,png,webp|mimetypes:image/jpeg,image/png,image/webp|max:15360',
            ], [
                'file.mimes' => 'ملف الصورة يجب أن يكون بصيغة آمنة (jpeg, jpg, png, webp) فقط.',
                'file.max' => 'الحد الأقصى لحجم الصورة هو 15 ميغابايت.',
            ]);
        }

        $file = $request->file('file');

        // قائمة حظر صارمة للامتدادات القابلة للتنفيذ
        $forbiddenExtensions = ['php', 'phtml', 'phar', 'exe', 'sh', 'bat', 'html', 'htm', 'js', 'svg', 'cgi', 'pl', 'py'];
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $forbiddenExtensions, true)) {
            abort(400, 'نوع الملف المرفوع غير مسموح به نهائياً لأسباب أمنية.');
        }

        // تطهير اسم الملف وتخزينه بأمان
        $safeOriginalName = strip_tags($file->getClientOriginalName());
        $path = $file->store("reports/{$report->id}", 'public');

        MediaAttachment::create([
            'activity_report_id' => $report->id,
            'category' => $category,
            'file_path' => $path,
            'file_name' => $safeOriginalName,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'تم التحقق من المرفق الإعلامي ورفعه بأمان بنجاح.');
    }

    public function destroy(MediaAttachment $media)
    {
        // حماية BOLA / IDOR: التحقق من صلاحية المستخدم على الجلسة المرتبطة بالمرفق
        $session = $media->activityReport->trainingSession;
        $this->authorizeSessionAccess($session);

        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return back()->with('success', 'تم حذف المرفق بنجاح.');
    }
}
