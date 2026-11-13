<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** v18: USB modem limiti filial bo'yicha + filial/kun bo'yicha modem SMS hisobi (Eskiz hisobga olinmaydi). */
return new class extends Migration
{
    public function up(): void
    {
        // Qayta ishga tushirilsa ham xato bermasligi uchun (MySQL DDL tranzaksiyada qaytmaydi)
        if (! Schema::hasColumn('branches', 'sms_modem_daily_limit')) {
            Schema::table('branches', function (Blueprint $t) {
                $t->unsignedInteger('sms_modem_daily_limit')->default(0)->after('sms_enabled');
            });
        }

        if (! Schema::hasTable('sms_branch_usage')) {
            Schema::create('sms_branch_usage', function (Blueprint $t) {
                $t->id();
                $t->foreignId('branch_id')->constrained()->cascadeOnDelete();
                $t->date('day');
                $t->unsignedInteger('count')->default(0);
                $t->unique(['branch_id', 'day']);
            });
        }

        DB::table('sms_branch_usage')->delete();

        // Oldingi oylar: modem orqali yuborilgan SMSlarni (Toshkent sanasi bo'yicha) qayta hisoblash
        $from = now('Asia/Tashkent')->subMonthsNoOverflow(2)->startOfMonth()->utc();
        $agg = [];
        DB::table('sms_messages')->where('provider', 'modem')->where('status', 'sent')->whereNotNull('sent_at')->where('sent_at', '>=', $from)
            ->select('id', 'branch_id', 'sent_at')->orderBy('id')->chunk(1000, function ($rows) use (&$agg) {
                foreach ($rows as $r) {
                    $day = \Illuminate\Support\Carbon::parse($r->sent_at, 'UTC')->setTimezone('Asia/Tashkent')->toDateString();
                    $agg[$r->branch_id][$day] = ($agg[$r->branch_id][$day] ?? 0) + 1;
                }
            });
        foreach ($agg as $branchId => $days) {
            foreach ($days as $day => $count) {
                DB::table('sms_branch_usage')->insert(['branch_id' => $branchId, 'day' => $day, 'count' => $count]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_branch_usage');
        if (Schema::hasColumn('branches', 'sms_modem_daily_limit')) {
            Schema::table('branches', fn (Blueprint $t) => $t->dropColumn('sms_modem_daily_limit'));
        }
    }
};
