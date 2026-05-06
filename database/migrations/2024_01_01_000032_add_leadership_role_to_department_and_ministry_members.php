<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('department_member', function (Blueprint $table) {
            $table->foreignId('leadership_role_id')
                ->nullable()
                ->after('role')
                ->constrained('leadership_roles')
                ->nullOnDelete();
        });

        Schema::table('ministry_member', function (Blueprint $table) {
            $table->foreignId('leadership_role_id')
                ->nullable()
                ->after('role')
                ->constrained('leadership_roles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('department_member', function (Blueprint $table) {
            $table->dropConstrainedForeignId('leadership_role_id');
        });

        Schema::table('ministry_member', function (Blueprint $table) {
            $table->dropConstrainedForeignId('leadership_role_id');
        });
    }
};
