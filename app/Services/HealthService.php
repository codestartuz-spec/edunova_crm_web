<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Backup;
use App\Models\HealthCheck;
use App\Models\User;
use App\Services\Sms\SmsDispatcher;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * v14: "Tizim holati" - to'liq tekshiruv. Natija qatori:
 * ['group','key','label','status' => ok|warn|fail,'detail'].
 * Eski SystemStatusService tekshiruvlari qayta ishlatiladi (zaxira bundan mustasno: endi Backup jadvali bo'yicha).
 */
class HealthService
{
    public const GROUPS = [
        'infra' => 'Infratuzilma',
        'queue' => 'Navbat va cron',
        'backup' => 'Zaxira',
        'services' => 'Xizmatlar',
        'logs' => 'Loglar',
        'security' => 'Xavfsizlik',
        'data' => "Ma'lumotlar yaxlitligi",
    ];

    public function __construct(private SystemStatusService $legacy) {}

    /** @return list<array{group:string,key:string,label:string,status:string,detail:string}> */
    public function checks(): array
    {
        $rows = [];
        $map = ['ok' => 'ok', 'warning' => 'warn', 'critical' => 'fail'];
        $groups = ['cron' => 'queue', 'queue' => 'queue', 'jobs' => 'queue', 'migrations' => 'infra', 'disk' => 'infra', 'wallets' => 'data', 'debug' => 'security'];

        foreach ($this->legacy->checks() as $c) {
            if ($c['key'] === 'backup') {
                continue;
            }
            $rows[] = ['group' => $groups[$c['key']] ?? 'infra', 'key' => $c['key'], 'label' => $c['label'], 'status' => $map[$c['status']] ?? 'warn', 'detail' => $c['detail']];
        }

        foreach ([$this->php(), $this->database(), $this->redis(), $this->storage(), $this->backup(), $this->smsModem(), $this->eskiz(), $this->telegram(), $this->logErrors(), $this->https(), $this->appKey(), $this->watchdog(), $this->publicEnv(), $this->singleSAdmin(), $this->orphanSms()] as $r) {
            if ($r === []) {
                continue;
            }
            $rows[] = $r;
        }

        return $rows;
    }

    /** Tekshiruvni bajarib, tarixga yozadi va yangi xatolar bo'lsa Telegram orqali Admin ID'larga xabar beradi. */
    public function run(bool $alert = true, ?array $rows = null): HealthCheck
    {
        $t = microtime(true);
        $rows ??= $this->checks();
        $fail = count(array_filter($rows, fn ($r) => $r['status'] === 'fail'));
        $warn = count(array_filter($rows, fn ($r) => $r['status'] === 'warn'));

        $rec = HealthCheck::create([
            'status' => $fail ? 'fail' : ($warn ? 'warn' : 'ok'),
            'fail_count' => $fail,
            'warn_count' => $warn,
            'duration_ms' => (int) round((microtime(true) - $t) * 1000),
            'results' => $rows,
            'created_at' => now(),
        ]);

        HealthCheck::where('created_at', '<', now()->subDays(30))->delete();

        if ($alert) {
            $this->alert($rows);
        }

        return $rec;
    }

    /** Faqat YANGI muammolar haqida xabar beradi (bir xil muammo har 15 daqiqada takrorlanmaydi). */
    private function alert(array $rows): void
    {
        $tg = app(TelegramService::class);
        $failing = collect($rows)->where('status', 'fail')->keyBy('key');
        $previous = collect(Cache::get('health.failing', []));
        $new = $failing->reject(fn ($r, $k) => $previous->contains($k));
        $fixed = $previous->reject(fn ($k) => $failing->has($k));

        Cache::forever('health.failing', $failing->keys()->all());

        if (! $tg->configured() || ($new->isEmpty() && $fixed->isEmpty())) {
            return;
        }

        $text = '';
        if ($new->isNotEmpty()) {
            $text .= "🔴 <b>".e(\App\Support\Brand::short()).": tizimda muammo</b>\n".$new->map(fn ($r) => '• '.e($r['label']).': '.e($r['detail']))->implode("\n");
        }
        if ($fixed->isNotEmpty()) {
            $text .= ($text ? "\n\n" : '')."🟢 Tuzaldi: ".$fixed->implode(', ');
        }

        foreach ($tg->adminIds() as $id) {
            $tg->sendMessage($id, $text);
        }
    }

    public function telegramSummary(): string
    {
        $rows = $this->checks();
        $icon = ['ok' => '🟢', 'warn' => '🟡', 'fail' => '🔴'];
        $lines = array_map(fn ($r) => $icon[$r['status']].' '.e($r['label']).($r['status'] === 'ok' ? '' : ': '.e($r['detail'])), $rows);
        $bad = count(array_filter($rows, fn ($r) => $r['status'] !== 'ok'));

        return "<b>Tizim holati</b> — ".($bad ? "{$bad} ta e'tibor talab qiladi" : 'hammasi joyida')."\n".implode("\n", $lines);
    }

    private function row(string $group, string $key, string $label, string $status, string $detail): array
    {
        return compact('group', 'key', 'label', 'status', 'detail');
    }

    private function php(): array
    {
        $missing = array_values(array_filter(['pdo', 'mbstring', 'openssl', 'zip', 'curl', 'json', 'fileinfo'], fn ($e) => ! extension_loaded($e)));

        if ($missing) {
            return $this->row('infra', 'php', 'PHP', 'fail', 'Yetishmayotgan kengaytmalar: '.implode(', ', $missing));
        }

        return $this->row('infra', 'php', 'PHP', version_compare(PHP_VERSION, '8.2.0', '>=') ? 'ok' : 'warn', 'PHP '.PHP_VERSION.', Laravel '.app()->version());
    }

    private function database(): array
    {
        try {
            $t = microtime(true);
            DB::select('select 1');
            $ms = (int) round((microtime(true) - $t) * 1000);
        } catch (\Throwable $e) {
            return $this->row('infra', 'database', 'Ma\'lumotlar bazasi', 'fail', 'Ulanib bo\'lmadi: '.$e->getMessage());
        }

        return $this->row('infra', 'database', 'Ma\'lumotlar bazasi', $ms > 500 ? 'warn' : 'ok', "Ulanish ishlayapti ({$ms} ms).");
    }

    /** v22: Redis (kesh/navbat) faqat ishlatilayotgan bo'lsa tekshiriladi. */
    private function redis(): array
    {
        $uses = in_array(config('cache.default'), ['redis', 'failover'], true) || config('queue.default') === 'redis';
        if (! $uses) {
            return [];
        }

        try {
            $t = microtime(true);
            \Illuminate\Support\Facades\Redis::connection('default')->ping();
            $ms = (int) round((microtime(true) - $t) * 1000);
        } catch (\Throwable $e) {
            $fallback = config('cache.default') === 'failover' ? ' Kesh vaqtincha faylga o\'tgan; navbat (SMS, Telegram) to\'xtashi mumkin.' : '';

            return $this->row('infra', 'redis', 'Redis (kesh/navbat)', 'fail', 'Ulanib bo\'lmadi: '.mb_substr($e->getMessage(), 0, 120).'.'.$fallback);
        }

        return $this->row('infra', 'redis', 'Redis (kesh/navbat)', $ms > 200 ? 'warn' : 'ok', "Ulanish ishlayapti ({$ms} ms).");
    }

    private function storage(): array
    {
        $bad = array_values(array_filter([storage_path('app'), storage_path('logs'), storage_path('framework/cache'), base_path('bootstrap/cache')], fn ($p) => ! is_writable($p)));

        return $bad
            ? $this->row('infra', 'storage', 'Yozish huquqlari', 'fail', 'Yozib bo\'lmaydi: '.implode(', ', array_map(fn ($p) => str_replace(base_path().DIRECTORY_SEPARATOR, '', $p), $bad)))
            : $this->row('infra', 'storage', 'Yozish huquqlari', 'ok', 'storage va bootstrap/cache yozish mumkin.');
    }

    private function backup(): array
    {
        $last = Backup::latest('id')->first();
        $ok = Backup::where('status', 'ok')->latest('id')->first();

        if (! $last) {
            return $this->row('backup', 'backup', 'Kunlik zaxira', 'fail', "Hali zaxira olinmagan. «php artisan app:backup» ni sinab ko'ring.");
        }
        if ($last->status !== 'ok') {
            return $this->row('backup', 'backup', 'Kunlik zaxira', 'fail', 'Oxirgi urinish muvaffaqiyatsiz: '.($last->error ?: 'noma\'lum xato'));
        }

        $hours = (int) $ok->created_at->diffInHours(now());
        if ($hours > 26) {
            return $this->row('backup', 'backup', 'Kunlik zaxira', 'fail', "Oxirgi zaxira {$hours} soat oldin — 01:00 jadvali ishlamayotgan bo'lishi mumkin.");
        }

        $tg = (array) $ok->telegram;
        $sent = count(array_filter($tg, fn ($v) => $v === 'ok'));
        $detail = "Oxirgi zaxira {$hours} soat oldin.";

        if (app(TelegramService::class)->configured() && $tg && $sent === 0) {
            return $this->row('backup', 'backup', 'Kunlik zaxira', 'warn', $detail.' Telegramga yuborilmadi.');
        }

        return $this->row('backup', 'backup', 'Kunlik zaxira', 'ok', $detail.($tg ? " Telegram: {$sent}/".count($tg).'.' : ' Telegram sozlanmagan.'));
    }

    private function smsModem(): array
    {
        if (! AppSetting::bool('sms.modem.enabled', false)) {
            return $this->row('services', 'sms_modem', 'SMS modem', 'ok', 'O\'chirilgan — SMS Eskiz orqali yuboriladi.');
        }

        $d = app(SmsDispatcher::class);
        $used = $d->usedToday();

        if ($d->breakerOpen()) {
            return $this->row('services', 'sms_modem', 'SMS modem', 'warn', 'Ketma-ket xatolar tufayli vaqtincha o\'chirildi, SMS Eskiz orqali ketmoqda.');
        }

        return $this->row('services', 'sms_modem', 'SMS modem', 'ok', "Bugun modem orqali {$used} ta SMS yuborildi (limit filial bo'yicha).");
    }

    private function eskiz(): array
    {
        $ok = filled(config('services.eskiz.email')) && filled(config('services.eskiz.password'));

        return $this->row('services', 'eskiz', 'Eskiz (zaxira SMS)', $ok ? 'ok' : 'warn', $ok ? 'Sozlangan.' : 'Kirish ma\'lumotlari .env da yo\'q — modem ishlamasa SMS ketmaydi.');
    }

    private function telegram(): array
    {
        $tg = app(TelegramService::class);

        if (! $tg->configured()) {
            return $this->row('services', 'telegram', 'Telegram bot', 'warn', 'Token kiritilmagan (zaxira va parol tiklash Telegramsiz ishlamaydi).');
        }
        if (! $tg->adminIds()) {
            return $this->row('services', 'telegram', 'Telegram bot', 'warn', 'Admin ID qo\'shilmagan — zaxira va ogohlantirishlar hech kimga bormaydi.');
        }

        if ($err = Cache::get('telegram.webhook_last_error')) {
            return $this->row('services', 'telegram', 'Telegram bot', 'warn', 'Oxirgi webhook xatosi ('.($err['at'] ?? '?').'): '.($err['message'] ?? ''));
        }

        return $this->row('services', 'telegram', 'Telegram bot', 'ok', 'Token sozlangan, Admin ID: '.count($tg->adminIds()).' ta.');
    }

    private function logErrors(): array
    {
        $path = storage_path('logs/laravel.log');

        if (! File::exists($path)) {
            return $this->row('logs', 'log_errors', 'Xatolar logi', 'ok', 'Log fayli bo\'sh.');
        }

        $size = filesize($path);
        $fh = fopen($path, 'rb');
        fseek($fh, max(0, $size - 262144));
        $tail = (string) stream_get_contents($fh);
        fclose($fh);

        $since = now()->subDay();
        $count = 0;
        if (preg_match_all('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \S+\.(ERROR|CRITICAL|ALERT|EMERGENCY)/m', $tail, $m)) {
            $count = count(array_filter($m[1], fn ($d) => $d >= $since->format('Y-m-d H:i:s')));
        }

        $mb = round($size / 1048576, 1);
        if ($count >= 50) {
            return $this->row('logs', 'log_errors', 'Xatolar logi', 'fail', "So'nggi 24 soatda {$count}+ xato. storage/logs/laravel.log ni ko'ring.");
        }
        if ($count > 0 || $size > 100 * 1048576) {
            return $this->row('logs', 'log_errors', 'Xatolar logi', 'warn', "So'nggi 24 soatda {$count} xato; log hajmi {$mb} MB.");
        }

        return $this->row('logs', 'log_errors', 'Xatolar logi', 'ok', "So'nggi 24 soatda xato yo'q (log {$mb} MB).");
    }

    private function https(): array
    {
        if (config('app.env') !== 'production') {
            return $this->row('security', 'https', 'HTTPS', 'ok', 'Ishlab chiqarish muhiti emas.');
        }

        $ok = str_starts_with((string) config('app.url'), 'https://');

        return $this->row('security', 'https', 'HTTPS', $ok ? 'ok' : 'warn', $ok ? 'APP_URL https. ' : 'APP_URL https:// bilan boshlanmaydi (Telegram webhook va Web Push HTTPS talab qiladi).');
    }

    private function appKey(): array
    {
        return filled(config('app.key'))
            ? $this->row('security', 'app_key', 'APP_KEY', 'ok', 'O\'rnatilgan.')
            : $this->row('security', 'app_key', 'APP_KEY', 'fail', '«php artisan key:generate» kerak.');
    }

    private function watchdog(): array
    {
        if (blank(config('services.monitor.beat_key'))) {
            return $this->row('infra', 'watchdog', 'Tashqi monitoring', 'warn', 'Ulanmagan (MONITOR_BEAT_KEY yo\'q). tools/watchdog ga qarang.');
        }
        $last = (int) Cache::get('monitor.beat', 0);
        if ($last === 0) {
            return $this->row('infra', 'watchdog', 'Tashqi monitoring', 'warn', 'Hali signal kelmagan.');
        }
        $min = (int) floor((now()->timestamp - $last) / 60);

        return $min > 10
            ? $this->row('infra', 'watchdog', 'Tashqi monitoring', 'fail', "Signal {$min} daqiqadan beri yo'q — watchdog o'chib qolgan.")
            : $this->row('infra', 'watchdog', 'Tashqi monitoring', 'ok', "So'nggi signal {$min} daqiqa oldin.");
    }

    private function publicEnv(): array
    {
        $exposed = File::exists(public_path('.env')) || File::exists(public_path('storage/../.env'));

        return $exposed
            ? $this->row('security', 'public_env', 'Maxfiy fayllar', 'fail', 'public/ ichida .env bor!')
            : $this->row('security', 'public_env', 'Maxfiy fayllar', basename(base_path()) === 'public' ? 'warn' : 'ok', 'public/ ichida .env yo\'q.');
    }

    private function singleSAdmin(): array
    {
        $n = User::where('role', 'sadmin')->count();

        return $n === 1
            ? $this->row('data', 'single_sadmin', 'sAdmin soni', 'ok', 'Aynan bitta sAdmin bor.')
            : $this->row('data', 'single_sadmin', 'sAdmin soni', 'fail', "sAdmin soni {$n} — bitta bo'lishi kerak.");
    }

    private function orphanSms(): array
    {
        $stuck = DB::table('sms_messages')->where('status', 'queued')->where('created_at', '<', now()->subHour())->count();

        return $stuck
            ? $this->row('data', 'sms_stuck', 'SMS navbati', 'warn', "{$stuck} ta SMS 1 soatdan beri yuborilmay turibdi (queue:work tekshiring).")
            : $this->row('data', 'sms_stuck', 'SMS navbati', 'ok', 'Tiqilib qolgan SMS yo\'q.');
    }
}
