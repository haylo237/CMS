<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministry_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('ministry_id')->constrained('ministries')->cascadeOnDelete();
            $table->enum('role', ['leader', 'assistant', 'member'])->default('member');
            $table->timestamps();

            $table->unique(['member_id', 'ministry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministry_member');
    }
};
