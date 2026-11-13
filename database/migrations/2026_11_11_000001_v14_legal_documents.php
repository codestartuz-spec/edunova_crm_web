<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** v14: maxfiylik siyosati, foydalanish shartlari, cookie siyosati va foydalanuvchi rozilik jurnali. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_documents', function (Blueprint $t) {
            $t->id();
            $t->string('type', 16)->unique();            // privacy | terms | cookies
            $t->string('title', 150);
            $t->longText('body');
            $t->unsignedInteger('version')->default(1);
            $t->timestamp('published_at')->nullable();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('legal_acceptances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('user_name', 150);                // foydalanuvchi o'chirilsa ham jurnalda qoladi
            $t->string('user_role', 16)->nullable();
            $t->string('document_type', 16);
            $t->unsignedInteger('version');
            $t->string('document_title', 150)->nullable();
            $t->string('ip', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->timestamp('accepted_at')->useCurrent();
            $t->index(['user_id', 'document_type', 'version']);
            $t->index('accepted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_acceptances');
        Schema::dropIfExists('legal_documents');
    }
};
