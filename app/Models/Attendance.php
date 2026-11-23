<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_session_id',
        'beneficiary_id',
        'serial_number',
        'attended',
        'remarks',
    ];

    protected $casts = [
        'attended' => 'boolean',
        'serial_number' => 'integer',
    ];

    public function trainingSession()
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
