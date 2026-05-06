<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_send_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->foreign('sent_by')->references('id')->on('members')->nullOnDelete();
            $table->enum('audience_type', ['all', 'branch', 'department', 'ministry']);
            $table->unsignedBigInteger('audience_id')->nullable(); // branch/dept/ministry id
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->enum('status', ['pending', 'sending', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_send_logs');
    }
};
