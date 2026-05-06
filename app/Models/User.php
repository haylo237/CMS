<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    const ROLES = [
        'super_admin',
        'admin',
        'pastor',
        'hod',
        'ministry_leader',
        'finance_officer',
        'member',
    ];

    protected $fillable = [
        'member_id',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isPastor(): bool
    {
        return $this->role === 'pastor';
    }

    public function isHOD(): bool
    {
        return $this->role === 'hod';
    }

    public function isMinistryLeader(): bool
    {
        return $this->role === 'ministry_leader';
    }

    public function isFinanceOfficer(): bool
    {
        return $this->role === 'finance_officer';
    }
}
