<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->enum('role', ['head', 'assistant', 'member'])->default('member');
            $table->timestamps();

            $table->unique(['member_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_member');
    }
};
