<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Branch;
use App\Models\CashRequest;
use App\Models\User;
use App\Services\Telegram\TelegramService;
use App\Support\Format;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** v15: har kuni kechqurun Telegram orqali filiallar kesimida kunlik hisobot. */
class DailyReportService
{
    /** Admin ID lar va yangi direktorlar uchun standart yuborish vaqti. */
    public const DEFAULT_TIME = '20:00';

    public function __construct(private TelegramService $telegram) {}

    /** @return array<string,int|float|null> bitta filial uchun ko'rsatkichlar */
    public function branchStats(int $branchId, CarbonImmutable $day): array
    {
        $d = $day->toDateString();
        $prev = $day->subDay()->toDateString();

        $pay = fn (string $date) => DB::table('payments')->where('branch_id', $branchId)->where('type', 'payment')->whereNull('reversed_at')
            ->whereDate('created_at', $date)
            ->selectRaw("coalesce(sum(case when method = 'cash' then amount end),0) as cash, coalesce(sum(case when method = 'card' then amount end),0) as card")->first();
        $t = $pay($d);
        $y = $pay($prev);

        $expenses = -(int) DB::table('wallet_transactions')->where('branch_id', $branchId)->where('type', 'expense')->whereDate('created_at', $d)->sum('amount')
            + (int) DB::table('cash_requests')->where('branch_id', $branchId)->where('kind', CashRequest::EXPENSE)->where('status', CashRequest::APPROVED)->whereDate('decided_at', $d)->sum('amount');

        $students = fn () => DB::table('users')->where('branch_id', $branchId)->where('role', Role::Student->value);
        $debt = $students()->whereNull('archived_at')->where('balance', '<', 0);

        $att = DB::table('attendances')->where('branch_id', $branchId)->whereDate('date', $d)
            ->selectRaw('coalesce(sum(is_present),0) as present, count(*) as total')->first();

        $leads = fn () => DB::table('leads')->where('branch_id', $branchId)->whereDate('created_at', $d);

        $income = (int) $t->cash + (int) $t->card;
        $yIncome = (int) $y->cash + (int) $y->card;

        return [
            'cash' => (int) $t->cash, 'card' => (int) $t->card, 'income' => $income,
            'income_delta' => $yIncome > 0 ? round(($income - $yIncome) * 100 / $yIncome) : null,
            'expenses' => $expenses,
            'debtors' => (clone $debt)->count(), 'debt_total' => -(int) (clone $debt)->sum('balance'),
            'new_leads' => $leads()->count(), 'converted' => $leads()->where('status', 'converted')->count(),
            'new_students' => $students()->whereDate('created_at', $d)->count(),
            'active_students' => DB::table('group_students')->where('branch_id', $branchId)->where('is_active', true)->distinct()->count('student_id'),
            'att_rate' => $att->total ? round($att->present * 100 / $att->total) : null,
            'absent' => (int) $att->total - (int) $att->present,
            'tomorrow_lessons' => DB::table('group_days')->join('groups', 'groups.id', '=', 'group_days.group_id')
                ->where('groups.branch_id', $branchId)->whereDate('group_days.date', $day->addDay()->toDateString())->count(),
        ];
    }

    /** @param  array<int,string>  $branches id => nom */
    public function build(array $branches, CarbonImmutable $day): string
    {
        return implode("\n\n", $this->parts($branches, $day));
    }

    /**
     * Hisobot bloklari (sarlavha, har filial, JAMI). Telegram 4096 belgi chegarasi uchun `chunks()` bilan bo'laklanadi.
     *
     * @param  array<int,string>  $branches id => nom
     * @return array<int,string>
     */
    public function parts(array $branches, CarbonImmutable $day): array
    {
        $out = ['📊 <b>Kunlik hisobot — '.$day->format('d.m.Y').'</b>'];
        $tot = [];
        $money = fn ($v) => Format::money($v);

        foreach ($branches as $id => $name) {
            $s = $this->branchStats((int) $id, $day);
            foreach (['cash', 'card', 'income', 'expenses', 'debtors', 'debt_total', 'new_leads', 'converted', 'new_students', 'active_students', 'absent', 'tomorrow_lessons'] as $k) {
                $tot[$k] = ($tot[$k] ?? 0) + $s[$k];
            }
            $out[] = $this->block('🏢 <b>'.e($name).'</b>', $s, $money);
        }

        if (count($branches) > 1) {
            $out[] = "━━━━━━━━━━\n".$this->block('🧮 <b>JAMI</b>', $tot + ['income_delta' => null, 'att_rate' => null], $money);
        }

        return $out;
    }

    /**
     * Bloklarni ≤ $limit belgili xabarlarga joylaydi (blok o'rtasidan uzmaydi).
     *
     * @param  array<int,string>  $parts
     * @return array<int,string>
     */
    public function chunks(array $parts, int $limit = 3800): array
    {
        $chunks = [];
        $cur = '';
        foreach ($parts as $p) {
            if ($cur !== '' && mb_strlen($cur) + 2 + mb_strlen($p) > $limit) {
                $chunks[] = $cur;
                $cur = '';
            }
            $cur = $cur === '' ? $p : $cur."\n\n".$p;
        }
        if ($cur !== '') {
            $chunks[] = $cur;
        }

        return $chunks;
    }

    private function block(string $title, array $s, callable $money): string
    {
        $delta = $s['income_delta'] === null ? '' : ' ('.($s['income_delta'] >= 0 ? '+' : '').$s['income_delta'].'% kecha bilan)';
        $lines = [
            $title,
            '💰 Tushum: <b>'.$money($s['income']).'</b>'.$delta,
            '   naqd '.$money($s['cash']).' · karta '.$money($s['card']),
            '📉 Chiqim: '.$money($s['expenses']),
            '⚠️ Qarzdorlar: <b>'.$s['debtors'].'</b> ta · '.$money($s['debt_total']),
            '📞 Yangi murojaatlar: '.$s['new_leads'].' (o\'quvchiga aylangan: '.$s['converted'].')',
            '🎓 Yangi o\'quvchilar: '.$s['new_students'].' · Faol o\'quvchilar: '.$s['active_students'],
        ];
        if ($s['att_rate'] !== null) {
            $lines[] = '✅ Davomat: '.$s['att_rate'].'% · yo\'q: '.$s['absent'];
        } elseif (isset($s['absent']) && $s['absent'] > 0) {
            $lines[] = '✅ Darsga kelmaganlar: '.$s['absent'];
        }
        $lines[] = '📅 Ertaga darslar: '.$s['tomorrow_lessons'];

        return implode("\n", $lines);
    }

    /**
     * Direktor ko'radigan faol filiallar (id => nom): "Barcha filiallar" yoki tanlanganlari.
     *
     * @param  array<int,string>  $all faol filiallar
     * @return array<int,string>
     */
    public function branchesFor(User $u, array $all): array
    {
        if ($u->role !== Role::Director || ($u->all_branches ?? true)) {
            return $u->role === Role::Director ? $all : ($u->branch_id && isset($all[$u->branch_id]) ? [$u->branch_id => $all[$u->branch_id]] : []);
        }
        $ids = DB::table('director_branches')->where('user_id', $u->id)->pluck('branch_id')->map(fn ($v) => (int) $v)->all();

        return array_intersect_key($all, array_flip($ids));
    }

    /**
     * Qabul qiluvchilar. Admin ID (sozlamadagi) lar barcha filial bo'yicha, doim DEFAULT_TIME da;
     * direktor/admin foydalanuvchilar o'z filiallari va o'z vaqti bo'yicha.
     *
     * @return array<int, array{chat:int,branches:array<int,string>,time:string,user:?User}>
     */
    public function recipients(): array
    {
        $all = Branch::where('status', 'active')->orderBy('name')->pluck('name', 'id')->all();
        $map = [];

        foreach ($this->telegram->adminIds() as $id) {
            $map[$id] = ['chat' => (int) $id, 'branches' => $all, 'time' => self::DEFAULT_TIME, 'user' => null];
        }
        User::whereIn('role', [Role::Director->value, Role::Admin->value])->where('status', UserStatus::Active->value)
            ->whereNull('archived_at')->where('report_enabled', true)->whereNotNull('telegram_chat_id')->get()->each(function (User $u) use (&$map, $all) {
                $chat = (int) $u->telegram_chat_id;
                if (isset($map[$chat])) {
                    return;
                }
                $branches = $this->branchesFor($u, $all);
                if ($branches !== []) {
                    $map[$chat] = ['chat' => $chat, 'branches' => $branches, 'time' => $u->report_time ?: self::DEFAULT_TIME, 'user' => $u];
                }
            });

        return array_values($map);
    }

    /** Hisobot vaqti kelganmi va bugun hali yuborilmaganmi? */
    public function isDue(array $r, CarbonImmutable $now): bool
    {
        if ($now->format('H:i') < $r['time']) {
            return false;
        }

        return $r['user']
            ? ($r['user']->report_last_sent_on?->toDateString() !== $now->toDateString())
            : ! Cache::has($this->sentKey($r['chat'], $now));
    }

    private function sentKey(int $chat, CarbonImmutable $day): string
    {
        return 'daily_report.sent.'.$day->toDateString().'.'.$chat;
    }

    /**
     * Bitta qabul qiluvchiga yuboradi (bo'laklab). Bot bloklangan bo'lsa Telegram bog'lanishi tozalanadi.
     *
     * @return bool hammasi yetib bordimi
     */
    public function deliverTo(array $r, CarbonImmutable $day): bool
    {
        $ok = true;
        foreach ($this->chunks($this->parts($r['branches'], $day)) as $text) {
            $res = $this->telegram->deliver($r['chat'], $text);
            if (! $res['ok']) {
                $ok = false;
                if ($res['blocked'] && $r['user']) {
                    $r['user']->forceFill(['telegram_chat_id' => null, 'telegram_username' => null, 'telegram_linked_at' => null])->save();
                }
                break;
            }
        }

        return $ok;
    }

    /**
     * @param  bool  $onlyDue true: faqat vaqti kelgan va bugun olmaganlarga (har 5 daqiqada); false: hammaga hozir
     * @return int yuborilgan qabul qiluvchilar soni
     */
    public function send(?CarbonImmutable $day = null, bool $onlyDue = false): int
    {
        if (! $this->telegram->configured()) {
            return 0;
        }
        $day ??= CarbonImmutable::now('Asia/Tashkent');
        $sent = 0;
        foreach ($this->recipients() as $r) {
            if ($onlyDue && ! $this->isDue($r, $day)) {
                continue;
            }
            try {
                $ok = $this->deliverTo($r, $day);
            } catch (\Throwable $e) {
                report($e);
                $ok = false;   // bitta qabul qiluvchi xatosi qolganlarni to'xtatmaydi
            }
            if ($ok) {
                $sent++;
            }
            if ($onlyDue && ($ok || $r['user'] === null)) {
                // muvaffaqiyatli yoki Admin ID: bugun qayta urinmaymiz (xato bo'lsa user uchun keyingi 5 daqiqada qayta uriniladi)
                $r['user'] ? $r['user']->forceFill(['report_last_sent_on' => $day->toDateString()])->save() : Cache::put($this->sentKey($r['chat'], $day), 1, now()->addDays(2));
            }
        }

        return $sent;
    }
}
