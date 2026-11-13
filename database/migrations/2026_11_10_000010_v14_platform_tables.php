<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** v14: global sozlamalar, SMS kunlik hisobi, Telegram, parol tiklash jurnali, zaxira, web push, tizim holati tarixi. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $t) {
            $t->id();
            $t->string('key', 100)->unique();
            $t->text('value')->nullable();
            $t->boolean('is_secret')->default(false);
            $t->timestamps();
        });

        Schema::create('sms_daily_usage', function (Blueprint $t) {
            $t->id();
            $t->date('day');
            $t->string('provider', 20);
            $t->unsignedInteger('count')->default(0);
            $t->unique(['day', 'provider']);
        });

        Schema::table('sms_messages', function (Blueprint $t) {
            $t->string('provider', 20)->nullable();
            $t->boolean('fallback_used')->default(false);
        });

        Schema::table('users', function (Blueprint $t) {
            $t->unsignedBigInteger('telegram_chat_id')->nullable()->index();
            $t->string('telegram_username', 64)->nullable();
            $t->timestamp('telegram_linked_at')->nullable();
        });

        Schema::table('password_reset_codes', function (Blueprint $t) {
            $t->string('channel', 12)->default('sms');
        });

        Schema::create('password_reset_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('phone_entered', 40)->nullable();
            $t->string('channel', 12)->nullable();   // telegram | sms
            $t->string('event', 30);                 // requested | code_sent | no_telegram | failed_send | code_wrong | reset_done | blocked
            $t->string('ip', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->string('note', 255)->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['created_at']);
        });

        Schema::create('backups', function (Blueprint $t) {
            $t->id();
            $t->string('status', 12);                // running | ok | failed
            $t->string('trigger', 12)->default('schedule'); // schedule | manual
            $t->string('file', 255)->nullable();
            $t->unsignedBigInteger('size')->nullable();
            $t->string('sha256', 64)->nullable();
            $t->unsignedInteger('duration_ms')->nullable();
            $t->json('telegram')->nullable();        // {chat_id: ok|error}
            $t->text('error')->nullable();
            $t->foreignId('started_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index('created_at');
        });

        Schema::create('push_subscriptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->string('endpoint_hash', 64)->unique();
            $t->text('endpoint');
            $t->string('public_key', 255);
            $t->string('auth_token', 255);
            $t->string('user_agent', 255)->nullable();
            $t->timestamps();
        });

        Schema::table('notifications', function (Blueprint $t) {
            $t->boolean('is_important')->default(false);
            $t->string('url', 500)->nullable();
            $t->json('roles')->nullable();
            $t->json('branch_ids')->nullable();
        });

        Schema::create('health_checks', function (Blueprint $t) {
            $t->id();
            $t->string('status', 8);                 // ok | warn | fail
            $t->unsignedSmallInteger('fail_count')->default(0);
            $t->unsignedSmallInteger('warn_count')->default(0);
            $t->unsignedInteger('duration_ms')->default(0);
            $t->json('results')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_checks');
        Schema::table('notifications', fn (Blueprint $t) => $t->dropColumn(['is_important', 'url', 'roles', 'branch_ids']));
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('backups');
        Schema::dropIfExists('password_reset_logs');
        Schema::table('password_reset_codes', fn (Blueprint $t) => $t->dropColumn('channel'));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['telegram_chat_id', 'telegram_username', 'telegram_linked_at']));
        Schema::table('sms_messages', fn (Blueprint $t) => $t->dropColumn(['provider', 'fallback_used']));
        Schema::dropIfExists('sms_daily_usage');
        Schema::dropIfExists('app_settings');
    }
};
