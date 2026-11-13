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
use Illuminate\Support\Facades\DB;

/** v15: har kuni kechqurun Telegram orqali filiallar kesimida kunlik hisobot. */
class DailyReportService
{
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

        return implode("\n\n", $out);
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

    /** @return array<int, array{chat:int,branches:array<int,string>}> qabul qiluvchilar (chat_id => filiallar) */
    public function recipients(): array
    {
        $all = Branch::where('status', 'active')->orderBy('name')->pluck('name', 'id')->all();
        $map = [];

        foreach ($this->telegram->adminIds() as $id) {
            $map[$id] = $all;
        }
        User::whereIn('role', [Role::Director->value, Role::Admin->value])->where('status', UserStatus::Active->value)
            ->whereNotNull('telegram_chat_id')->get()->each(function (User $u) use (&$map, $all) {
                $chat = (int) $u->telegram_chat_id;
                if (isset($map[$chat])) {
                    return;
                }
                if ($u->role === Role::Director) {
                    $map[$chat] = $all;
                } elseif ($u->branch_id && isset($all[$u->branch_id])) {
                    $map[$chat] = [$u->branch_id => $all[$u->branch_id]];
                }
            });

        return collect($map)->map(fn ($b, $chat) => ['chat' => (int) $chat, 'branches' => $b])->values()->all();
    }

    /** @return int yuborilgan xabarlar soni */
    public function send(?CarbonImmutable $day = null): int
    {
        if (! $this->telegram->configured()) {
            return 0;
        }
        $day ??= CarbonImmutable::now('Asia/Tashkent');
        $sent = 0;
        foreach ($this->recipients() as $r) {
            if ($r['branches'] !== [] && $this->telegram->sendMessage($r['chat'], $this->build($r['branches'], $day))) {
                $sent++;
            }
        }

        return $sent;
    }
}
