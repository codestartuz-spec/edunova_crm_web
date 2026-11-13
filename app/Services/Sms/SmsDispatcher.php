<?php

namespace App\Services\Sms;

use App\Models\Branch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * v14: SMS yuborish tartibi: avval USB modem (filial kunlik limiti doirasida, Toshkent sanasi bo'yicha),
 * modem ishlamasa / limit tugasa / uzilish himoyasi (circuit breaker) ochiq bo'lsa - Eskiz.
 */
class SmsDispatcher
{
    private const BREAKER_FAILS = 3;

    private const BREAKER_COOLDOWN_SECONDS = 300;

    public function __construct(private ?UsbModemGateway $modem = null)
    {
        $this->modem ??= new UsbModemGateway;
    }

    /** @return array{result:SmsResult,fallback:bool} */
    public function send(Branch $branch, string $phone, string $text): array
    {
        $fallback = false;

        if ($this->modem->isConfigured()) {
            $skip = $this->breakerOpen() ? 'uzilish himoyasi yoqilgan' : null;

            if (! $skip && ! $this->reserve($branch)) {
                $skip = 'filial kunlik limiti tugagan yoki 0';
            }

            if (! $skip) {
                try {
                    $result = $this->modem->send($phone, $text);
                } catch (\Throwable $e) {
                    $result = SmsResult::fail('modem', 'Modemga ulanib bo\'lmadi: '.$e->getMessage());
                }

                if ($result->ok) {
                    $this->breakerSuccess();

                    return ['result' => $result, 'fallback' => false];
                }

                $this->release($branch);
                $this->breakerFailure();
                Log::warning('USB modem SMS yubormadi, Eskizga o\'tildi: '.$result->response);
            }

            $fallback = true;
        }

        return ['result' => (new EskizGateway($branch))->send($phone, $text), 'fallback' => $fallback];
    }

    /** Filialning kunlik modem limiti (0 = filial modemdan foydalanmaydi, faqat Eskiz). */
    public function branchLimit(Branch $branch): int
    {
        return max(0, (int) Branch::whereKey($branch->id)->value('sms_modem_daily_limit'));
    }

    public function usedToday(?Branch $branch = null): int
    {
        return (int) DB::table('sms_branch_usage')->where('day', $this->today())->when($branch, fn ($q) => $q->where('branch_id', $branch->id))->sum('count');
    }

    /**
     * Filial bo'yicha oxirgi oylar (joriy + oldingi ikkita) modem SMS soni.
     *
     * @return array<int, array<string,int>> [branch_id => ['YYYY-MM' => count]]
     */
    public function monthly(int $months = 3): array
    {
        $now = now('Asia/Tashkent');
        $keys = [];
        for ($i = 0; $i < $months; $i++) {
            $keys[] = $now->copy()->subMonthsNoOverflow($i)->format('Y-m');
        }
        $from = $now->copy()->subMonthsNoOverflow($months - 1)->startOfMonth()->toDateString();

        $out = [];
        foreach (DB::table('sms_branch_usage')->where('day', '>=', $from)->get(['branch_id', 'day', 'count']) as $r) {
            $m = substr((string) $r->day, 0, 7);
            $out[$r->branch_id][$m] = ($out[$r->branch_id][$m] ?? 0) + (int) $r->count;
        }

        return $out;
    }

    /** Atomar hisob: filial limitidan oshmasa 1 ga oshiradi va true qaytaradi. */
    public function reserve(Branch $branch): bool
    {
        $limit = $this->branchLimit($branch);
        if ($limit < 1) {
            return false;
        }

        $day = $this->today();
        DB::table('sms_branch_usage')->insertOrIgnore(['branch_id' => $branch->id, 'day' => $day, 'count' => 0]);

        return DB::table('sms_branch_usage')->where('branch_id', $branch->id)->where('day', $day)->where('count', '<', $limit)->increment('count') > 0;
    }

    private function release(Branch $branch): void
    {
        DB::table('sms_branch_usage')->where('branch_id', $branch->id)->where('day', $this->today())->where('count', '>', 0)->decrement('count');
    }

    public function breakerOpen(): bool
    {
        return Cache::has('sms.modem.breaker_open');
    }

    public function breakerFailure(): void
    {
        $n = (int) Cache::get('sms.modem.fails', 0) + 1;
        Cache::put('sms.modem.fails', $n, now()->addMinutes(30));

        if ($n >= self::BREAKER_FAILS) {
            Cache::put('sms.modem.breaker_open', now()->toIso8601String(), self::BREAKER_COOLDOWN_SECONDS);
        }
    }

    public function breakerSuccess(): void
    {
        Cache::forget('sms.modem.fails');
        Cache::forget('sms.modem.breaker_open');
    }

    private function today(): string
    {
        return now('Asia/Tashkent')->toDateString();
    }
}
