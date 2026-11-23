<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'birth_date',
        'gender',
        'municipality',
        'education_level',
        'is_schooled',
        'has_chronic_disease',
        'chronic_disease_details',
        'has_disability',
        'disability_type',
        'disability_details',
        'second_disability',
        'other_health_issues',
        'psychosocial_notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_schooled' => 'boolean',
        'has_chronic_disease' => 'boolean',
        'has_disability' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function sessions()
    {
        return $this->belongsToMany(TrainingSession::class, 'attendances');
    }

    /**
     * حساب العمر في تاريخ محدد (أو تاريخ اليوم)
     */
    public function getAgeAtDate(?Carbon $date = null): int
    {
        $targetDate = $date ?? Carbon::now();
        return (int) $this->birth_date->diffInYears($targetDate);
    }

    /**
     * تحديد الفئة العمرية المطابقة لتقرير النشاط (من 10 إلى 19)
     */
    public function getAgeBracketAtDate(?Carbon $date = null): string
    {
        $age = $this->getAgeAtDate($date);

        if ($age < 10) return 'أقل من 10';
        if ($age >= 10 && $age < 11) return 'من 10 الى 11';
        if ($age >= 11 && $age < 12) return 'من 11 الى 12';
        if ($age >= 12 && $age < 13) return 'من 12 الى 13';
        if ($age >= 13 && $age < 14) return 'من 13 الى 14';
        if ($age >= 14 && $age < 15) return 'من 14 الى 15';
        if ($age >= 15 && $age < 16) return 'من 15 الى 16';
        if ($age >= 16 && $age < 17) return 'من 16 الى 17';
        if ($age >= 17 && $age < 18) return 'من 17 الى 18';
        if ($age >= 18 && $age <= 19) return 'من 18 الى 19';

        return 'أكثر من 19';
    }
}
