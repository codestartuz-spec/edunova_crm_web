<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // O'quvchi tanlagan javoblar (savol ID si => variant indeksi): natijani keyin qayta ko'rish uchun
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->json('answers')->nullable()->after('answer_key');
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropColumn('answers');
        });
    }
};
