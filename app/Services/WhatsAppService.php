<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $accessToken;
    private string $phoneNumberId;
    private bool $enabled;

    public function __construct()
    {
        $this->accessToken   = Setting::get('whatsapp_access_token', '');
        $this->phoneNumberId = Setting::get('whatsapp_phone_number_id', '');
        $this->enabled       = (bool) Setting::get('whatsapp_enabled', false);
    }

    public function isConfigured(): bool
    {
        return $this->enabled && !empty($this->accessToken) && !empty($this->phoneNumberId);
    }

    /**
     * Send a plain-text WhatsApp message to a single phone number.
     * Returns true on success, false on failure.
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('WhatsApp: attempted send but service is not configured or disabled.');
            return false;
        }

        $normalized = $this->normalizePhone($phone);
        if (!$normalized) {
            Log::warning("WhatsApp: skipping invalid phone number [{$phone}]");
            return false;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(15)
                ->post("https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $normalized,
                    'type'              => 'text',
                    'text'              => ['body' => $message],
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp send failed', [
                'phone'  => $normalized,
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp exception: ' . $e->getMessage(), ['phone' => $normalized]);
            return false;
        }
    }

    /**
     * Normalize a phone number to E.164 format (digits only, with leading +).
     * Assumes Nigerian numbers (234 country code) if no country code detected.
     */
    public function normalizePhone(string $phone): ?string
    {
        // Strip everything except digits and leading +
        $digits = preg_replace('/[^\d]/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // Already has country code (11+ digits starting with country code)
        if (strlen($digits) >= 11 && !str_starts_with($digits, '0')) {
            return $digits;
        }

        // Strip leading 0 for local numbers (e.g. 0803... → 803...)
        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // Default country code: Nigeria (234)
        $countryCode = Setting::get('whatsapp_country_code', '234');

        return $countryCode . $digits;
    }
}
