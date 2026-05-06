<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'address', 'city', 'phone', 'email',
        'pastor_id', 'parent_branch_id', 'description',
    ];

    public function pastor(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'pastor_id');
    }

    public function parentBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'parent_branch_id');
    }

    public function subBranches(): HasMany
    {
        return $this->hasMany(Branch::class, 'parent_branch_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
