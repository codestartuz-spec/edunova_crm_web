<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** v25: ochiq landing sahifasidan kelgan demo so'rovlar (filialga bog'liq emas, faqat sAdmin ko'radi). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_requests', function (Blueprint $t) {
            $t->id();
            $t->string('name', 80);
            $t->string('phone', 20);
            $t->string('center', 100)->nullable();
            $t->string('size', 20)->nullable();       // o'quvchilar soni oralig'i
            $t->string('message', 500)->nullable();
            $t->string('locale', 2)->default('uz');
            $t->string('status', 20)->default('new'); // new | contacted | done | spam
            $t->boolean('telegram_sent')->default(false);
            $t->string('ip_hash', 64)->nullable();    // IP ning xeshi (shaxsiy IP saqlanmaydi)
            $t->text('note')->nullable();             // sAdmin izohi
            $t->timestamps();
            $t->index(['status', 'created_at']);
            $t->index(['phone', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};
