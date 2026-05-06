<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'body', 'audience',
        'branch_id', 'department_id', 'ministry_id',
        'published_by', 'published_at', 'expires_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'published_by');
    }

    public function scopeActive($query)
        public function sendLogs(): HasMany
        {
            return $this->hasMany(WhatsAppSendLog::class);
        }

        public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->whereNotNull('published_at');
    }
}
