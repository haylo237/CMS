<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sender_id', 'recipient_id', 'subject', 'body', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'recipient_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function scopeInbox($query, int $memberId)
    {
        return $query->where('recipient_id', $memberId);
    }

    public function scopeSent($query, int $memberId)
    {
        return $query->where('sender_id', $memberId);
    }
}
