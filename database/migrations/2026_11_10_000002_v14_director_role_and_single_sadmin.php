<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * v14: yangi "director" roli va YAGONA sAdmin.
 *  1) users.role ENUM ro'yxatiga 'director' qo'shiladi (eski qiymatlar o'zgarmaydi).
 *  2) Bir nechta sAdmin bo'lsa - eng birinchisi (eng kichik id) sAdmin bo'lib qoladi, qolganlari
 *     DIREKTORGA o'tkaziladi (ruxsatlarsiz: sAdmin ularga kerakli ruxsatlarni o'zi belgilaydi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['sadmin', 'director', 'admin', 'manager', 'teacher', 'student', 'operator'])->change();
        });

        $keep = DB::table('users')->where('role', 'sadmin')->whereNull('archived_at')->orderBy('id')->value('id');

        if ($keep) {
            DB::table('users')->where('role', 'sadmin')->where('id', '!=', $keep)->update(['role' => 'director']);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'director')->update(['role' => 'sadmin']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['sadmin', 'admin', 'manager', 'teacher', 'student', 'operator'])->change();
        });
    }
};
