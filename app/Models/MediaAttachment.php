<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_report_id',
        'category',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    public function activityReport()
    {
        return $this->belongsTo(ActivityReport::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public static function categories(): array
    {
        return [
            'institution_gate' => 'الباب الخارجي للمؤسسة التي أجري بها النشاط',
            'facilitators_team' => 'طاقم الميسرين',
            'group_photo' => 'صورة جماعية لليافعين والهيئة المستقبلة والميسرين',
            'in_action' => 'صورة لجميع اليافعين أثناء النشاط',
            'activity_video' => 'فيديو للنشاط',
            'extra' => 'مرفقات وصور إضافية',
        ];
    }
}
