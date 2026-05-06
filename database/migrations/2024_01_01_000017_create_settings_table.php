<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, file, boolean
            $table->string('group')->default('general'); // general, branding, finance, notifications
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        $defaults = [
            // General
            ['key' => 'church_name',       'label' => 'Church Name',        'type' => 'text',     'group' => 'general'],
            ['key' => 'church_address',    'label' => 'Address',            'type' => 'textarea', 'group' => 'general'],
            ['key' => 'church_city',       'label' => 'City',               'type' => 'text',     'group' => 'general'],
            ['key' => 'church_phone',      'label' => 'Phone',              'type' => 'text',     'group' => 'general'],
            ['key' => 'church_email',      'label' => 'Email',              'type' => 'text',     'group' => 'general'],
            ['key' => 'church_website',    'label' => 'Website',            'type' => 'text',     'group' => 'general'],
            ['key' => 'church_motto',      'label' => 'Motto / Tagline',    'type' => 'text',     'group' => 'general'],
            ['key' => 'currency_symbol',   'label' => 'Currency Symbol',    'type' => 'text',     'group' => 'general', 'value' => '₦'],
            ['key' => 'timezone',          'label' => 'Timezone',           'type' => 'text',     'group' => 'general', 'value' => 'Africa/Lagos'],
            // Branding
            ['key' => 'church_logo',       'label' => 'Church Logo',        'type' => 'file',     'group' => 'branding'],
            ['key' => 'report_header_bg',  'label' => 'Report Header Color','type' => 'text',     'group' => 'branding', 'value' => '#4f46e5'],
            ['key' => 'primary_color',     'label' => 'Primary Color',      'type' => 'text',     'group' => 'branding', 'value' => '#4f46e5'],
            // Finance
            ['key' => 'fiscal_year_start', 'label' => 'Fiscal Year Start (MM-DD)', 'type' => 'text', 'group' => 'finance', 'value' => '01-01'],
            ['key' => 'finance_approval',  'label' => 'Require Finance Approval', 'type' => 'boolean', 'group' => 'finance', 'value' => '0'],
            // Notifications
            ['key' => 'notify_new_member', 'label' => 'Notify on New Member', 'type' => 'boolean', 'group' => 'notifications', 'value' => '0'],
            ['key' => 'notify_new_report', 'label' => 'Notify on New Report', 'type' => 'boolean', 'group' => 'notifications', 'value' => '0'],
        ];

        foreach ($defaults as $setting) {
            \DB::table('settings')->insert(array_merge([
                'value'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $setting));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
