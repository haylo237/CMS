<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PhoneNumber;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'address', 'city', 'region', 'division', 'subdivision', 'country_code_id', 'region_id', 'division_id', 'subdivision_id', 'phone', 'email',
        'pastor_id', 'parent_branch_id', 'description',
    ];

    public function countryCode(): BelongsTo
    {
        return $this->belongsTo(CountryCode::class);
    }

    public function getFullPhoneNumberAttribute(): ?string
    {
        return PhoneNumber::normalize($this->phone, $this->countryCode?->dial_code);
    }

    public function getDisplayPhoneAttribute(): ?string
    {
        return PhoneNumber::display($this->phone, $this->countryCode?->dial_code);
    }

    public function getAliasAttribute(): string
    {
        return $this->parent_branch_id ? $this->name : 'HQ';
    }

    public function pastor(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'pastor_id');
    }

    public function regionRef(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function divisionRef(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function subdivisionRef(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
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
