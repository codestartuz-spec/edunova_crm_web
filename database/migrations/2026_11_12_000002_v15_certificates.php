<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->string('number', 24)->unique();          // EDU-2026-000123
            $table->string('code', 16)->unique();            // tasodifiy maxfiy kod (QR manzilida)
            $table->string('student_name');
            $table->string('course_name');
            $table->string('level_name')->nullable();
            $table->date('issued_on');
            $table->string('status', 12)->default('issued')->index(); // issued | revoked
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('revoke_reason')->nullable();
            $table->foreignId('replaced_by_id')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['student_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
