<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'code',
        'title_ar',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActivities($query)
    {
        return $query->where('type', 'activity')->orderBy('sort_order');
    }

    public function scopeCompetencies($query)
    {
        return $query->where('type', 'competency')->orderBy('sort_order');
    }
}
