<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'type',
        'category',
        'description',
        'recorded_by',
        'department_id',
        'transaction_date',
        'reference_number',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'recorded_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeByMonth($query, int $year, int $month)
    {
        return $query->whereYear('transaction_date', $year)
                     ->whereMonth('transaction_date', $month);
    }
}
