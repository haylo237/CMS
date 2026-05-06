<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'type', 'description'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'department_member')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function head(): ?Member
    {
        return $this->members()->wherePivot('role', 'head')->first();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function financeTransactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class);
    }
}
