<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leadership_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // GO, First Lady, Elder, Deacon, Deaconess
            $table->unsignedTinyInteger('rank')->default(99); // lower = higher authority
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leadership_roles');
    }
};
