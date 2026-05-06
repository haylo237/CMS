<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Support\PhoneNumber;

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

    public static function currentCurrency(): ?Currency
    {
        $currencyId = static::get('currency_id');

        if ($currencyId) {
            return Currency::find($currencyId);
        }

        $legacySymbol = static::get('currency_symbol');

        return match ($legacySymbol) {
            'USD' => Currency::where('code', 'USD')->first(),
            'GBP' => Currency::where('code', 'GBP')->first(),
            'EUR' => Currency::where('code', 'EUR')->first(),
            'GHS' => Currency::where('code', 'GHS')->first(),
            'KES' => Currency::where('code', 'KES')->first(),
            default => Currency::where('code', 'NGN')->first(),
        };
    }

    public static function currencySymbol(): string
    {
        return static::currentCurrency()?->symbol ?? 'NGN';
    }

    public static function currencyCode(): string
    {
        return static::currentCurrency()?->code ?? 'NGN';
    }

    public static function currencyDecimalPlaces(): int
    {
        // Most African currencies in active church deployments are non-decimal in practice.
        $zeroDecimalCurrencies = [
            'DZD', 'AOA', 'BWP', 'BIF', 'CVE', 'KMF', 'CDF', 'DJF', 'EGP', 'ERN', 'ETB',
            'GMD', 'GHS', 'GNF', 'KES', 'LSL', 'LRD', 'LYD', 'MGA', 'MWK', 'MRU', 'MUR',
            'MAD', 'MZN', 'NAD', 'NGN', 'RWF', 'SHP', 'SLE', 'SOS', 'ZAR', 'SSP', 'SDG',
            'SZL', 'TZS', 'TND', 'UGX', 'XAF', 'XOF', 'ZMW', 'ZWL',
        ];

        return in_array(strtoupper(static::currencyCode()), $zeroDecimalCurrencies, true) ? 0 : 2;
    }

    public static function formatMoney(float|int|string|null $amount): string
    {
        return static::currencySymbol() . number_format((float) ($amount ?? 0), static::currencyDecimalPlaces());
    }

    public static function churchCountryCode(): ?CountryCode
    {
        $countryCodeId = static::get('church_country_code_id');

        return $countryCodeId ? CountryCode::find($countryCodeId) : null;
    }

    public static function churchDisplayPhone(): ?string
    {
        return PhoneNumber::display(static::get('church_phone'), static::churchCountryCode()?->dial_code);
    }

    public static function churchFullPhone(): ?string
    {
        return PhoneNumber::normalize(static::get('church_phone'), static::churchCountryCode()?->dial_code);
    }
}
