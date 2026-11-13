<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * v14: olib tashlangan bo'limlarning ruxsatlarini foydalanuvchilardan tozalaydi:
 *  - ai.chat                 (Yordam / AI tahlil bo'limi olib tashlandi)
 *  - staff.view_all_branches (Filiallar xodimlari bo'limi olib tashlandi)
 * Jadvallar (ai_chats, app_versions va h.k.) hozircha o'chirilmaydi - ma'lumot saqlanadi.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('user_permissions')->whereIn('permission', ['ai.chat', 'staff.view_all_branches'])->delete();
    }

    public function down(): void
    {
        // Qaytarilmaydi: olib tashlangan ruxsatlar endi mavjud emas.
    }
};
