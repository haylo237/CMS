<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['service', 'meeting', 'special', 'prayer', 'outreach'])->default('service');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->date('date');
            $table->time('time')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('members')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
