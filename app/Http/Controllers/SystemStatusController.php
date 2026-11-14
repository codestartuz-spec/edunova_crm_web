<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\HealthCheck;
use App\Services\HealthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/** Tizimning umumiy sog'ligi (cron, zaxira, disk, ...) - faqat sAdmin (route darajasida `role:sadmin`), filialga bog'liq emas. */
class SystemStatusController extends Controller
{
    private const RANGES = ['24h' => ['label' => '24 soat', 'hours' => 24], '7d' => ['label' => '7 kun', 'hours' => 168], '30d' => ['label' => '30 kun', 'hours' => 720]];

    private const BARS = 60;

    private const SEVERITY = ['ok' => 1, 'warn' => 2, 'fail' => 3];

    public function index(Request $request, HealthService $health)
    {
        $range = array_key_exists((string) $request->query('range'), self::RANGES) ? (string) $request->query('range') : '24h';
        $checks = $health->checks();

        // O'zini-o'zi tiklash: rejalashtiruvchi (cron) to'xtab qolsa ham tarix uzilmasin - oxirgi yozuv 20 daqiqadan eski bo'lsa, hozirgi natijani yozib qo'yamiz.
        $last = HealthCheck::latest('id')->value('created_at');
        $stale = ! $last || \Illuminate\Support\Carbon::parse($last)->lt(now()->subMinutes(20));
        $selfHealed = false;
        if ($stale && Cache::lock('health.selfheal', 60)->get()) {
            $health->run(true, $checks);
            $selfHealed = true;
        }

        $now = now();
        $from = $now->copy()->subHours(self::RANGES[$range]['hours']);
        $rows = HealthCheck::where('created_at', '>=', $from)->orderBy('id')->get(['id', 'status', 'fail_count', 'warn_count', 'created_at']);

        // Ustunlar: vaqt oralig'i bo'yicha guruhlanadi, har birida eng og'ir holat. Ma'lumot yo'q oraliq - bo'sh (cron ishlamagan).
        $span = max(1, $now->diffInSeconds($from, true));
        $size = $span / self::BARS;
        $buckets = [];
        for ($i = 0; $i < self::BARS; $i++) {
            $buckets[$i] = ['from' => $from->copy()->addSeconds((int) ($i * $size)), 'to' => $from->copy()->addSeconds((int) (($i + 1) * $size)), 'status' => 'none', 'total' => 0, 'ok' => 0, 'worst' => null];
        }
        foreach ($rows as $r) {
            $i = min(self::BARS - 1, max(0, (int) floor($r->created_at->diffInSeconds($from, true) / $size)));
            $b = &$buckets[$i];
            $b['total']++;
            $b['ok'] += $r->status === 'ok' ? 1 : 0;
            if ($b['worst'] === null || self::SEVERITY[$r->status] >= self::SEVERITY[$b['worst']->status]) {
                $b['worst'] = $r;
                $b['status'] = $r->status;
            }
            unset($b);
        }

        // Hodisalar: ketma-ket "ok" bo'lmagan tekshiruvlar
        $incidents = [];
        $cur = null;
        foreach ($rows as $r) {
            if ($r->status !== 'ok') {
                if (! $cur) {
                    $cur = ['start' => $r->created_at, 'end' => null, 'worst' => $r, 'count' => 0];
                }
                $cur['count']++;
                if (self::SEVERITY[$r->status] >= self::SEVERITY[$cur['worst']->status]) {
                    $cur['worst'] = $r;
                }
            } elseif ($cur) {
                $cur['end'] = $r->created_at;
                $incidents[] = $cur;
                $cur = null;
            }
        }
        if ($cur) {
            $incidents[] = $cur;
        }
        $incidents = array_slice(array_reverse($incidents), 0, 15);

        // Tooltip va hodisalar uchun: faqat eng og'ir yozuvlarning natijalaridan "ok" bo'lmaganlari
        $ids = collect($buckets)->filter(fn ($b) => $b['worst'] && $b['status'] !== 'ok')->map(fn ($b) => $b['worst']->id)
            ->merge(collect($incidents)->map(fn ($i) => $i['worst']->id))->unique()->values();
        $issues = HealthCheck::whereIn('id', $ids)->get(['id', 'results'])->mapWithKeys(fn ($h) => [
            $h->id => collect($h->results ?? [])->where('status', '!=', 'ok')->map(fn ($r) => ['label' => $r['label'] ?? '', 'status' => $r['status'] ?? 'warn', 'detail' => $r['detail'] ?? ''])->values()->all(),
        ]);

        $uptime = [];
        foreach (self::RANGES as $k => $cfg) {
            $c = HealthCheck::where('created_at', '>=', $now->copy()->subHours($cfg['hours']))->selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');
            $total = (int) $c->sum();
            $uptime[$k] = [
                'total' => $total,
                'up' => $total ? round(($total - (int) ($c['fail'] ?? 0)) / $total * 100, 2) : null,
                'perfect' => $total ? round((int) ($c['ok'] ?? 0) / $total * 100, 1) : null,
            ];
        }

        return view('system-status.index', [
            'checks' => $checks,
            'groups' => collect($checks)->groupBy('group'),
            'groupLabels' => HealthService::GROUPS,
            'range' => $range,
            'ranges' => self::RANGES,
            'buckets' => $buckets,
            'incidents' => $incidents,
            'issues' => $issues,
            'uptime' => $uptime,
            'lastCheck' => HealthCheck::latest('id')->value('created_at'),
            'historyCount' => $rows->count(),
            'selfHealed' => $selfHealed,
            'cronGapMinutes' => $last ? (int) \Illuminate\Support\Carbon::parse($last)->diffInMinutes(now(), true) : null,
        ]);
    }

    /** Tekshiruvni hozir bajarib, tarixga yozadi. */
    public function runNow(HealthService $health): RedirectResponse
    {
        $rec = $health->run();

        return back()->with($rec->status === 'fail' ? 'error' : 'success', "Tekshiruv bajarildi: {$rec->fail_count} ta muammo, {$rec->warn_count} ta ogohlantirish.");
    }

    /**
     * v12: sAdmin kutilmagan (masalan mysqldump muammosini tuzatgandan keyin) zaxirani darhol
     * qo'lda ishga tushirishi mumkin - cron'ning ertagi 03:00'sini kutish shart emas.
     */
    public function runBackupNow(): RedirectResponse
    {
        $exitCode = Artisan::call('app:backup');
        $output = trim(Artisan::output());

        AuditLog::record('system.backup_manual', null, "sAdmin zaxirani qo'lda ishga tushirdi: ".($exitCode === 0 ? 'muvaffaqiyatli' : 'xato'));

        return $exitCode === 0
            ? back()->with('success', "Zaxira olindi. {$output}")
            : back()->with('error', "Zaxira olishda xatolik: {$output}");
    }
}
