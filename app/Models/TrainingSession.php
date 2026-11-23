<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'session_date',
        'institution_id',
        'venue',
        'wilaya',
        'municipality',
        'lead_facilitator_1_id',
        'lead_facilitator_2_id',
        'assistant_facilitators',
        'transit_from_municipality',
        'transit_to_municipality',
        'institution_approved',
        'institution_signature_path',
        'institution_approved_at',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
        'assistant_facilitators' => 'array',
        'institution_approved' => 'boolean',
        'institution_approved_at' => 'datetime',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function leadFacilitator1()
    {
        return $this->belongsTo(User::class, 'lead_facilitator_1_id');
    }

    public function leadFacilitator2()
    {
        return $this->belongsTo(User::class, 'lead_facilitator_2_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class)->orderBy('serial_number');
    }

    public function attendees()
    {
        return $this->belongsToMany(Beneficiary::class, 'attendances')
                    ->withPivot('serial_number', 'attended', 'remarks')
                    ->withTimestamps();
    }

    public function activityReport()
    {
        return $this->hasOne(ActivityReport::class);
    }
}
