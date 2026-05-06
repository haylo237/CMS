<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'report_type',
        'department_id',
        'ministry_id',
        'submitted_by',
        'reporting_period_start',
        'reporting_period_end',
        'status',
        'metadata',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'metadata'               => 'array',
        'reporting_period_start' => 'date',
        'reporting_period_end'   => 'date',
        'reviewed_at'            => 'datetime',
    ];

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'reviewed_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, string $start, string $end)
    {
        return $query->whereBetween('reporting_period_start', [$start, $end]);
    }
}
