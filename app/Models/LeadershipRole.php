<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LeadershipRole extends Model
{
    protected $fillable = ['name', 'rank', 'description'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_leadership')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }
}
