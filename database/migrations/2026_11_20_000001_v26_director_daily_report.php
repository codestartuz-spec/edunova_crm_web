<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** v26: har bir foydalanuvchi (direktor/admin) uchun kunlik Telegram hisobot sozlamasi. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->boolean('report_enabled')->default(true)->after('telegram_linked_at');
            $t->string('report_time', 5)->default('20:00')->after('report_enabled');
            $t->date('report_last_sent_on')->nullable()->after('report_time');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['report_enabled', 'report_time', 'report_last_sent_on']));
    }
};
