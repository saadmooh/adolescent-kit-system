<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_session_id',
        'selected_activities',
        'selected_competencies',
        'facilities',
        'difficulties',
        'strengths',
        'weaknesses',
        'engagement_notes',
        'attendance_notes',
        'general_remarks',
        'recommendations',
        'rapporteur_id',
        'rapporteur_wilaya',
        'rapporteur_date',
        'rapporteur_signature_path',
        'host_contact_name',
        'host_contact_title',
        'host_contact_email',
        'host_contact_phone',
        'host_signature_path',
        'host_approval_date',
        'host_wilaya',
        'cached_total_attendees',
        'cached_male_count',
        'cached_female_count',
        'cached_schooled_count',
        'cached_unschooled_count',
        'cached_age_distribution',
        'cached_education_breakdown',
        'cached_disabled_count',
        'cached_disabilities_breakdown',
        'cached_chronic_diseases_count',
    ];

    protected $casts = [
        'selected_activities' => 'array',
        'selected_competencies' => 'array',
        'cached_age_distribution' => 'array',
        'cached_education_breakdown' => 'array',
        'cached_disabilities_breakdown' => 'array',
        'rapporteur_date' => 'date',
        'host_approval_date' => 'date',
    ];

    public function trainingSession()
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function rapporteur()
    {
        return $this->belongsTo(User::class, 'rapporteur_id');
    }

    public function mediaAttachments()
    {
        return $this->hasMany(MediaAttachment::class);
    }

    public function getMediaByCategory(string $category)
    {
        return $this->mediaAttachments()->where('category', $category)->first();
    }
}
