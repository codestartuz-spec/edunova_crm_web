<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** v24: «Video darslar» — tizim haqidagi video qo'llanmalar (hamma filial uchun umumiy). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutorial_videos', function (Blueprint $t) {
            $t->id();
            $t->string('title', 150);
            $t->string('url', 500);
            $t->string('category', 60)->nullable();
            $t->string('description', 500)->nullable();
            $t->string('duration', 10)->nullable(); // masalan: 5:30
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutorial_videos');
    }
};
