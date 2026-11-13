<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * v20: direktorga filial ruxsatlari. `users.all_branches` - true bo'lsa barcha filiallar (hozirgi holat saqlanadi),
 * false bo'lsa faqat `director_branches` dagi filiallar. Migratsiya takroran ishlaganda ham xato bermaydi (idempotent).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'all_branches')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('all_branches')->default(true);
            });
        }

        if (! Schema::hasTable('director_branches')) {
            Schema::create('director_branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
                $table->unique(['user_id', 'branch_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('director_branches');
        if (Schema::hasColumn('users', 'all_branches')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('all_branches'));
        }
    }
};
