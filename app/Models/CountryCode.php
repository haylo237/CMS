<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    protected $fillable = [
        'country_name',
        'iso_code',
        'dial_code',
        'is_active',
    ];

    public function getDisplayNameAttribute(): string
    {
        return "+{$this->dial_code} ({$this->country_name})";
    }
}