<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** v18: filiallarning CRM obuna to'lovlari tarixi (tizimning boshqa joyiga bog'liq emas). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branch_payments')) {
            return;
        }

        Schema::create('branch_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('amount');
            $t->string('method', 10); // cash | card
            $t->string('note', 500)->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['branch_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_payments');
    }
};
