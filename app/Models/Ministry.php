<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ministry extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'ministry_member')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function leader(): ?Member
    {
        return $this->members()->wherePivot('role', 'leader')->first();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
