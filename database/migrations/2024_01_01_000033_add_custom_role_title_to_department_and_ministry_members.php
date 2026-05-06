<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('department_member', function (Blueprint $table) {
            $table->string('custom_role_title')->nullable()->after('role');
        });

        Schema::table('ministry_member', function (Blueprint $table) {
            $table->string('custom_role_title')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('department_member', function (Blueprint $table) {
            $table->dropColumn('custom_role_title');
        });

        Schema::table('ministry_member', function (Blueprint $table) {
            $table->dropColumn('custom_role_title');
        });
    }
};
