<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('country_code_id')->nullable()->after('last_name')->constrained('country_codes')->nullOnDelete();
        });

        $defaultCountryCodeId = DB::table('country_codes')->where('iso_code', 'NG')->value('id');

        if ($defaultCountryCodeId) {
            DB::table('members')
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->whereNull('country_code_id')
                ->update(['country_code_id' => $defaultCountryCodeId]);
        }
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('country_code_id');
        });
    }
};