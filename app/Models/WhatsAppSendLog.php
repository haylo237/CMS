<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppSendLog extends Model
{
    protected $fillable = [
        'announcement_id',
        'sent_by',
        'audience_type',
        'audience_id',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
        'error_message',
    ];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'sent_by');
    }
}
