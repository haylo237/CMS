<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'type', 'branch_id', 'date', 'time', 'description', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function presentCount(): int
    {
        return $this->attendances()->where('status', 'present')->count();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())->orderBy('date');
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString())->orderByDesc('date');
    }
}
