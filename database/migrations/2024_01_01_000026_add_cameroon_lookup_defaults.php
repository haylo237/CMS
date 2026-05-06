<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')->insertOrIgnore([
            'code' => 'XAF',
            'name' => 'Central African CFA Franc',
            'symbol' => 'XAF',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('country_codes')->insertOrIgnore([
            'country_name' => 'Cameroon',
            'iso_code' => 'CM',
            'dial_code' => '237',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('currencies')->where('code', 'XAF')->delete();
        DB::table('country_codes')->where('iso_code', 'CM')->delete();
    }
};