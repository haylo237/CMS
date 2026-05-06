<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('region')->nullable()->after('city');
            $table->string('division')->nullable()->after('region');
            $table->string('subdivision')->nullable()->after('division');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['region', 'division', 'subdivision']);
        });
    }
};