<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = ['country_code_id', 'item_number', 'name'];

    public function countryCode(): BelongsTo
    {
        return $this->belongsTo(CountryCode::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }
}
