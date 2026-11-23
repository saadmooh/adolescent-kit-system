<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'wilaya',
        'municipality',
        'address',
        'category',
        'contact_person_name',
        'contact_person_title',
        'contact_person_email',
        'contact_person_phone',
    ];

    public function trainingSessions()
    {
        return $this->hasMany(TrainingSession::class);
    }
}
