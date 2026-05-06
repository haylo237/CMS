<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $currencyId = DB::table('currencies')->where('code', 'NGN')->value('id');

        if ($currencyId) {
            DB::table('settings')->insertOrIgnore([
                'key' => 'currency_id',
                'value' => (string) $currencyId,
                'type' => 'text',
                'group' => 'general',
                'label' => 'Currency',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'currency_id')->delete();
    }
};