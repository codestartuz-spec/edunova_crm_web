<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use App\Support\Viz;
use App\Support\BranchContext;
use App\Support\SafeInput;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatisticsController extends Controller
{
    public function index(Request $request, StatisticsService $stats)
    {
        $this->authorize('statistics.view');

        [$from, $to] = self::period($request);
        $user = $request->user();

        $showMoney = $user->can('payments.view');
        $showFinance = $user->can('finance.view');
        [$o, $trend, $updatedAt] = $this->cachedCore($stats, $from, $to, $user);
        $kpis = $this->kpiCards($stats, $from, $to, $o, $trend, $showMoney, $showFinance, $user);
        $charts = $this->charts($stats, $from, $to, $o, $trend, $showMoney, $showFinance);

        // v13: kunlik / haftalik / oylik tushum dinamikasi (tanlangan davrga bog'liq emas) va "bugun/hafta/oy" solishtiruvi
        $dyn = in_array(SafeInput::string($request->input('dyn')), ['day', 'week', 'month'], true) ? SafeInput::string($request->input('dyn')) : 'day';
        $snapshot = [];
        if ($showMoney) {
            $d = $stats->incomeDynamics($dyn);
            $charts['dynamics'] = Viz::column(array_column($d['rows'], 'label'), [
                ['label' => 'Naqt', 'data' => array_column($d['rows'], 'cash'), 'slot' => 1],
                ['label' => 'Plastik', 'data' => array_column($d['rows'], 'card'), 'slot' => 2],
            ], 'money', stacked: true, nameColumn: ['day' => 'Kun', 'week' => 'Hafta', 'month' => 'Oy'][$dyn]);
            $mm = fn ($v) => Viz::format('money', $v);
            $charts['dynamics_table'] = [
                'columns' => [['day' => 'Kun', 'week' => 'Hafta', 'month' => 'Oy'][$dyn], 'Naqt', 'Plastik', 'Qaytarilgan', 'Sof tushum', "To'lovlar", "O'zgarish"],
                'rows' => array_map(fn ($r) => [$r['label'], $mm($r['cash']), $mm($r['card']), $mm($r['refunds']), $mm($r['net']), (string) $r['count'], $r['change'] === null ? '—' : ($r['change'] > 0 ? '+' : '').$r['change'].'%'], array_reverse($d['rows'])),
                'foot' => ['Jami', $mm($d['totals']['cash']), $mm($d['totals']['card']), $mm($d['totals']['refunds']), $mm($d['totals']['net']), (string) $d['totals']['count'], ''],
            ];
            $charts['dynamics_average'] = $d['average'];
            $snapshot = $stats->incomeSnapshot();
        }

        $charts['sources'] = $this->sourcesChart($stats, $from, $to);

        $comparison = $user->isGlobal() && BranchContext::id() === null ? $stats->branchComparison($from, $to) : [];
        if ($comparison && $showMoney) {
            $charts['branches'] = Viz::column(array_column($comparison, 'branch'), [
                ['label' => 'Sof tushum', 'data' => array_column($comparison, 'net_income'), 'slot' => 1],
            ], 'money', nameColumn: 'Filial');
            $charts['branch_students'] = Viz::column(array_column($comparison, 'branch'), [
                ['label' => "Faol o'quvchi", 'data' => array_column($comparison, 'students'), 'slot' => 3],
                ['label' => "Yangi o'quvchi", 'data' => array_column($comparison, 'new_students'), 'slot' => 2],
            ], nameColumn: 'Filial');
        }

        return view('statistics.index', [
            'from' => $from, 'to' => $to, 'o' => $o, 'trend' => $trend, 'charts' => $charts,
            'updatedAt' => \Carbon\Carbon::createFromTimestamp($updatedAt), 'kpis' => $kpis, 'dyn' => $dyn, 'snapshot' => $snapshot, 'comparison' => $comparison, 'showMoney' => $showMoney, 'showFinance' => $showFinance,
        ]);
    }

    /** Kesh versiyasi: yangi to'lov/murojaat/o'quvchi bo'lganda oshiriladi, eski kesh avtomatik yaroqsiz bo'ladi. */
    public static function version(): int
    {
        return (int) Cache::get('stats:ver', 1);
    }

    public static function bump(): void
    {
        Cache::put('stats:ver', self::version() + 1, now()->addDays(30));
    }

    public function refresh(): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('statistics.view');
        self::bump();

        return back()->with('success', "Statistika yangilandi.");
    }

    /** KPI kartalari JSON ko'rinishida (sahifa raqamlarni 5 daqiqada bir shu manzildan yangilaydi). */
    public function data(Request $request, StatisticsService $stats): JsonResponse
    {
        $this->authorize('statistics.view');

        [$from, $to] = self::period($request);
        $user = $request->user();
        [$o, $trend, $updatedAt] = $this->cachedCore($stats, $from, $to, $user);

        return response()->json(['updated' => \Carbon\Carbon::createFromTimestamp($updatedAt)->format('H:i'), 'cards' => $this->kpiCards($stats, $from, $to, $o, $trend, $user->can('payments.view'), $user->can('finance.view'), $user)]);
    }

    /** Eng og'ir hisob-kitoblar (davr umumiy ko'rsatkichlari va 12 oylik dinamika) 5 daqiqa keshlanadi. */
    private function cachedCore(StatisticsService $stats, CarbonImmutable $from, CarbonImmutable $to, $user): array
    {
        $key = sprintf('stats:core:v%d:%d:%s:%s:%s', self::version(), $user->id, BranchContext::id() ?? 'all', $from->toDateString(), $to->toDateString());

        return Cache::remember($key, 300, fn () => [$stats->overview($from, $to), $stats->trend(12), now()->timestamp]);
    }

    /**
     * KPI kartalari: qiymat, oldingi shunday davrga nisbatan o'zgarish (%) va 12 oylik sparkline.
     *
     * @return list<array<string,mixed>>
     */
    private function kpiCards(StatisticsService $stats, CarbonImmutable $from, CarbonImmutable $to, array $o, array $trend, bool $money, bool $finance, $user): array
    {
        $days = $from->diffInDays($to) + 1;
        $pTo = $from->subDay();
        $pFrom = $pTo->subDays($days - 1);
        $key = sprintf('stats:prev:v%d:%d:%s:%s:%s', self::version(), $user->id, BranchContext::id() ?? 'all', $pFrom->toDateString(), $pTo->toDateString());
        $p = Cache::remember($key, 300, fn () => $stats->overview($pFrom, $pTo));

        $delta = fn ($cur, $prev) => $prev ? round(($cur - $prev) * 100 / abs($prev), 1) : null;
        $spark = fn (string $k) => array_column($trend, $k);
        $profit = array_map(fn ($t) => $t['net_income'] - $t['expenses'] - $t['salaries'], $trend);
        $card = fn (string $key, string $label, $value, string $unit, string $note, string $color = '', ?float $delta = null, array $spark = [], bool $invert = false) => compact('key', 'label', 'value', 'unit', 'note', 'color', 'delta', 'spark', 'invert');
        $m = fn ($v) => \App\Support\Format::money($v);

        $c = [];
        if ($money) {
            $c[] = $card('net_income', 'Sof tushum', $o['net_income'], 'money', "Tushum {$m($o['income'])} − qaytarilgan {$m($o['refunds'])}", 'text-emerald-600', $delta($o['net_income'], $p['net_income']), $spark('net_income'));
            $c[] = $card('cash', 'Naqt tushum', $o['income_cash'], 'money', $o['payments_count'].' ta to\'lov (naqt va plastik)', '', $delta($o['income_cash'], $p['income_cash']), $spark('income_cash'));
            $c[] = $card('card', 'Plastik tushum', $o['income_card'], 'money', 'Karta orqali tushgan', '', $delta($o['income_card'], $p['income_card']), $spark('income_card'));
            $c[] = $card('discounts', 'Chegirma va bonus', $o['discounts'], 'money', 'Balansga qo\'shilgan chegirmalar', 'text-amber-600', $delta($o['discounts'], $p['discounts']), [], true);
        }
        if ($finance) {
            $c[] = $card('expenses', 'Xarajat', $o['expenses'], 'money', 'Kassa va moliya xarajatlari', 'text-brand-600', $delta($o['expenses'], $p['expenses']), $spark('expenses'), true);
            $c[] = $card('salaries', 'Ish haqi', $o['salaries'], 'money', 'O\'qituvchi va hodimlarga to\'langan', 'text-brand-600', $delta($o['salaries'], $p['salaries']), $spark('salaries'), true);
            $c[] = $card('profit', 'Foyda (taxminiy)', $o['profit'], 'money', 'Sof tushum − xarajat − ish haqi', $o['profit'] >= 0 ? 'text-emerald-600' : 'text-brand-600', $delta($o['profit'], $p['profit']), $profit);
        }
        $c[] = $card('new_students', "Yangi o'quvchilar", $o['new_students'], 'int', 'Davrda ro\'yxatga olingan', '', $delta($o['new_students'], $p['new_students']), $spark('new_students'));
        $c[] = $card('active_students', "Faol o'quvchilar", $o['active_students'], 'int', 'Hozir faol guruhda');
        $c[] = $card('active_groups', 'Faol guruhlar', $o['active_groups'], 'int', 'Hozir davom etayotgan');
        $c[] = $card('debtors', 'Qarzdorlar', $o['debtors'], 'int', 'Umumiy qarz: '.$m($o['debt_total']), 'text-brand-600');
        $c[] = $card('new_leads', 'Murojaatlar', $o['new_leads'], 'int', 'Qabul qilingan: '.$o['converted_leads'].($o['lead_conversion'] !== null ? " ({$o['lead_conversion']}%)" : ''), '', $delta($o['new_leads'], $p['new_leads']), $spark('new_leads'));
        $c[] = $card('attendance', 'Davomad', $o['attendance_rate'], 'percent', $o['attendance_records'].' ta yozuv asosida', '', $o['attendance_rate'] !== null && $p['attendance_rate'] !== null ? round($o['attendance_rate'] - $p['attendance_rate'], 1) : null);

        return $c;
    }

    private function sourcesChart(StatisticsService $stats, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $src = $stats->leadSources($from, $to);

        return Viz::donut(array_keys($src), array_values($src), 'int', ['value' => (string) array_sum($src), 'label' => 'murojaat'], null, 'Murojaat', 'Manba');
    }

    /**
     * Har bir dashboard uchun grafik spec'i (jadval ko'rinishi shu spec'dan quriladi).
     *
     * @return array<string, mixed>
     */
    private function charts(StatisticsService $stats, CarbonImmutable $from, CarbonImmutable $to, array $o, array $trend, bool $money, bool $finance): array
    {
        $c = [];
        $months = array_map(fn ($t) => Viz::label($t['month']), $trend);

        if ($money) {
            $inc = $stats->incomeSeries($from, $to);
            $c['income'] = Viz::column(
                array_map(fn ($r) => Viz::label($r['key']), $inc['rows']),
                [['label' => 'Naqt', 'data' => array_column($inc['rows'], 'cash'), 'slot' => 1], ['label' => 'Plastik', 'data' => array_column($inc['rows'], 'card'), 'slot' => 2]],
                'money', stacked: true, nameColumn: $inc['unit'] === 'day' ? 'Kun' : 'Oy',
            );
            $c['income_unit'] = $inc['unit'];
            $c['method'] = Viz::donut(['Naqt', 'Plastik'], [$o['income_cash'], $o['income_card']], 'money', ['value' => Viz::short($o['income']), 'label' => 'tushum'], [null, null], 'Tushum', "To'lov usuli");
            $top = $stats->topGroups($from, $to);
            $c['top_groups'] = Viz::bar(array_column($top, 'name'), [['label' => 'Sof tushum', 'data' => array_column($top, 'net'), 'slot' => 1]], 'money', nameColumn: 'Guruh');
            $c['top_groups_ids'] = array_column($top, 'group_id');
        }

        if ($finance) {
            $c['flow'] = Viz::column($months, [
                ['label' => 'Sof tushum', 'data' => array_column($trend, 'net_income'), 'slot' => 1],
                ['label' => 'Xarajat', 'data' => array_column($trend, 'expenses'), 'slot' => 2],
                ['label' => 'Ish haqi', 'data' => array_column($trend, 'salaries'), 'slot' => 4],
            ], 'money', nameColumn: 'Oy');
            $c['profit'] = Viz::line($months, [[
                'label' => 'Foyda', 'slot' => 3,
                'data' => array_map(fn ($t) => $t['net_income'] - $t['expenses'] - $t['salaries'], $trend),
            ]], 'money', true, 'Oy');
        } elseif ($money) {
            $c['flow'] = Viz::column($months, [['label' => 'Sof tushum', 'data' => array_column($trend, 'net_income'), 'slot' => 1]], 'money', nameColumn: 'Oy');
        }

        $c['people'] = Viz::line($months, [
            ['label' => "Yangi o'quvchi", 'data' => array_column($trend, 'new_students'), 'slot' => 3],
            ['label' => 'Murojaat', 'data' => array_column($trend, 'new_leads'), 'slot' => 1],
            ['label' => 'Qabul qilingan', 'data' => array_column($trend, 'converted_leads'), 'slot' => 2],
        ], 'int', false, 'Oy');

        $courses = $stats->studentsByCourse();
        $c['courses'] = Viz::bar(array_column($courses, 'name'), [['label' => "Faol o'quvchi", 'data' => array_column($courses, 'students'), 'slot' => 3]], 'int', nameColumn: 'Kurs');

        $debt = $stats->debtBuckets();
        $c['debt'] = Viz::column(array_column($debt, 'label'), [['label' => 'Qarzdor soni', 'data' => array_column($debt, 'count'), 'slot' => 8]], 'int', nameColumn: 'Qarz miqdori');
        $c['debt_table'] = [
            'columns' => ['Qarz miqdori', 'Qarzdorlar', 'Qarz summasi'],
            'rows' => array_map(fn ($d) => [$d['label'], Viz::format('int', $d['count']), Viz::format('money', $d['sum'])], $debt),
            'foot' => ['Jami', Viz::format('int', array_sum(array_column($debt, 'count'))), Viz::format('money', array_sum(array_column($debt, 'sum')))],
        ];

        $att = $stats->attendanceByMonth(6);
        $c['attendance'] = Viz::line(array_map(fn ($k) => Viz::label($k), array_keys($att)), [['label' => 'Davomad', 'data' => array_values($att), 'slot' => 6]], 'percent', true, 'Oy');

        $c['funnel'] = $stats->leadStatuses($from, $to);

        return $c;
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable} */
    public static function period(Request $request): array
    {
        $from = self::date(SafeInput::string($request->input('from')), CarbonImmutable::today()->startOfMonth());
        $to = self::date(SafeInput::string($request->input('to')), CarbonImmutable::today());

        return $from->gt($to) ? [$to, $from] : [$from, $to];
    }

    private static function date(?string $value, CarbonImmutable $default): CarbonImmutable
    {
        try {
            return $value ? CarbonImmutable::parse($value)->startOfDay() : $default;
        } catch (\Throwable) {
            return $default;
        }
    }
}
