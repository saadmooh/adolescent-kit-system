<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'is_builtin',
        'description',
    ];

    protected $casts = [
        'is_builtin' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'slug');
    }

    public function hasPermission(string $key): bool
    {
        return $this->permissions->contains('key', $key);
    }
}
