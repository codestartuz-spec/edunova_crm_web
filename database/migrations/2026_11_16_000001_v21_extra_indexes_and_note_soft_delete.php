<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * v21: additive - mavjud ma'lumotga tegmaydi.
 *  - hisobot/qidiruv uchun qo'shimcha indekslar (har biri avval mavjudligi tekshirilib qo'shiladi);
 *  - student_notes: o'chirilgan eslatma qaytarib bo'lmas yo'qolmasin deb soft delete.
 *    (O'quvchi, to'lov va guruhlar tizimda o'chirilmaydi: o'quvchi bloklanadi, to'lov o'zgarmas, guruh arxivlanadi.)
 */
return new class extends Migration
{
    private array $indexes = [
        // [jadval, ustunlar, nom]
        ['payments', ['branch_id', 'type', 'created_at'], 'payments_branch_type_created_idx'],
        ['wallet_transactions', ['subject_type', 'subject_id'], 'wallet_tx_subject_idx'],
        ['sms_messages', ['status', 'created_at'], 'sms_messages_status_created_idx'],
        ['sms_messages', ['phone', 'created_at'], 'sms_messages_phone_created_idx'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as [$table, $columns, $name]) {
            if (Schema::hasTable($table) && ! Schema::hasIndex($table, $name)) {
                Schema::table($table, fn (Blueprint $t) => $t->index($columns, $name));
            }
        }

        if (Schema::hasTable('student_notes') && ! Schema::hasColumn('student_notes', 'deleted_at')) {
            Schema::table('student_notes', fn (Blueprint $t) => $t->softDeletes());
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as [$table, , $name]) {
            if (Schema::hasTable($table) && Schema::hasIndex($table, $name)) {
                Schema::table($table, fn (Blueprint $t) => $t->dropIndex($name));
            }
        }

        if (Schema::hasColumn('student_notes', 'deleted_at')) {
            Schema::table('student_notes', fn (Blueprint $t) => $t->dropSoftDeletes());
        }
    }
};
