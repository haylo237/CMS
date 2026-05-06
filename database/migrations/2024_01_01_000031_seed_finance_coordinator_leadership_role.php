<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('leadership_roles')->updateOrInsert(
            ['name' => 'Finance Coordinator'],
            [
                'rank' => 15,
                'description' => 'Coordinates and manages finance data entry and updates.',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('leadership_roles')->where('name', 'Finance Coordinator')->delete();
    }
};
