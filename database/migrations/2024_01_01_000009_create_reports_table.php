<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('report_type', ['department', 'ministry', 'finance']);
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('ministry_id')->nullable()->constrained('ministries')->nullOnDelete();
            $table->foreignId('submitted_by')->constrained('members')->cascadeOnDelete();
            $table->date('reporting_period_start');
            $table->date('reporting_period_end');
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->jsonb('metadata')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('members')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
