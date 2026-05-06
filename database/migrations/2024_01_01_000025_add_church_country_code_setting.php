<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $countryCodeId = DB::table('country_codes')->where('iso_code', 'NG')->value('id');

        DB::table('settings')->insertOrIgnore([
            'key' => 'church_country_code_id',
            'value' => $countryCodeId ? (string) $countryCodeId : null,
            'type' => 'text',
            'group' => 'general',
            'label' => 'Church Phone Country Code',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'church_country_code_id')->delete();
    }
};