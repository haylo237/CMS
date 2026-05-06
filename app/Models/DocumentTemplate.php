<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'file_path', 'original_name',
        'mime_type', 'file_size', 'description', 'uploaded_by', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'file_size'  => 'integer',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'uploaded_by');
    }

    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
