<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'approval_status',
        'is_active',
        'approved_at',
        'admin_notes',
        'wilaya',
        'municipality',
        'address',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved' && $this->is_active;
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function leadSessions()
    {
        return $this->hasMany(TrainingSession::class, 'lead_facilitator_1_id');
    }

    /**
     * الدور المُسنَد للمستخدم (مفتاح عبر عمود role = slug الدور)
     */
    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'slug');
    }

    /**
     * التحقق من امتلاك صلاحية معيّنة حسب دور المستخدم
     * (مدير النظام يملك كل الصلاحيات دائماً كحماية احتياطية)
     */
    public function hasPermission(string $key): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return $this->roleModel?->hasPermission($key) ?? false;
    }

    /**
     * الاسم العربي للدور للعرض في الواجهات
     */
    public function roleName(): string
    {
        return $this->roleModel?->name ?? $this->role;
    }
}
