<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever("setting:{$key}", function () use ($key) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return '__missing__';
            }
            return $setting->value ?? '__null__';
        });

        if ($value === '__missing__') {
            return $default;
        }

        if ($value === '__null__') {
            return $default;
        }

        // Re-fetch type for boolean cast (type is rarely needed, so skip caching it)
        $type = Cache::rememberForever("setting_type:{$key}", fn() => static::where('key', $key)->value('type') ?? 'text');

        if ($type === 'boolean') {
            return (bool) $value;
        }

        return $value;
    }

    /**
     * Set a setting value by key. Creates the key if it doesn't exist.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting:{$key}");
        Cache::forget("setting_type:{$key}");
    }

    /**
     * Return all settings in a given group, keyed by key.
     */
    public static function group(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->get()->keyBy('key');
    }

    /**
     * Return all settings as a flat key => value array.
     */
    public static function allKeyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
