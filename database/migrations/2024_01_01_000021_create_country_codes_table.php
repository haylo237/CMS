<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_codes', function (Blueprint $table) {
            $table->id();
            $table->string('country_name');
            $table->string('iso_code', 2)->unique();
            $table->string('dial_code', 10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('country_codes')->insert([
            ['country_name' => 'Nigeria', 'iso_code' => 'NG', 'dial_code' => '234', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_name' => 'Ghana', 'iso_code' => 'GH', 'dial_code' => '233', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_name' => 'Kenya', 'iso_code' => 'KE', 'dial_code' => '254', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_name' => 'South Africa', 'iso_code' => 'ZA', 'dial_code' => '27', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_name' => 'United Kingdom', 'iso_code' => 'GB', 'dial_code' => '44', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_name' => 'United States', 'iso_code' => 'US', 'dial_code' => '1', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('country_codes');
    }
};