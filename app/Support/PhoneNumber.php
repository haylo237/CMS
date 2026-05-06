<?php

namespace App\Support;

class PhoneNumber
{
    public static function digitsOnly(?string $phone): ?string
    {
        $digits = preg_replace('/[^\d]/', '', (string) $phone);

        return $digits !== '' ? $digits : null;
    }

    public static function normalize(?string $phone, ?string $dialCode = null): ?string
    {
        $digits = static::digitsOnly($phone);

        if (!$digits) {
            return null;
        }

        if ($dialCode) {
            $normalizedDialCode = static::digitsOnly($dialCode);
            if (!$normalizedDialCode) {
                return null;
            }

            if (str_starts_with($digits, '0')) {
                $digits = substr($digits, 1);
            }

            if (str_starts_with($digits, $normalizedDialCode)) {
                return $digits;
            }

            return $normalizedDialCode . $digits;
        }

        if (strlen($digits) >= 11 && !str_starts_with($digits, '0')) {
            return $digits;
        }

        return $digits;
    }

    public static function display(?string $phone, ?string $dialCode = null): ?string
    {
        if (!$phone) {
            return null;
        }

        return $dialCode ? '+' . $dialCode . ' ' . $phone : $phone;
    }
}