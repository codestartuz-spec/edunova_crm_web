<?php

namespace App\Services\Statistics;

use App\Enums\Role;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\User;
use App\Support\Viz;
use Carbon\CarbonImmutable;

/**
 * Hodim faoliyati statistikasi (StatisticsService dan ajratib olingan; xatti-harakat o'zgarmagan).
 * `StatisticsService::staffActivity()` shu klassga yo'naltiradi.
 */
class StaffActivityStatistics
{
    /**
     * v13: bitta hodimning (admin / menejer / operator) faoliyati: qabul qilgan to'lovlari (soni va summasi),
     * qo'shgan murojaatlari (shundan qabul qilinganlari), murojaatlarga yozgan izohlari va ro'yxatga olgan o'quvchilari.
     *  - day:   so'nggi 30 kun (har kun alohida);  - month: so'nggi 12 oy.
     * Storno qilingan to'lovlar hisobga olinmaydi.
     *
     * @return array{unit:string,rows:array<int,array<string,mixed>>,totals:array<string,int>}
     */
    public function staffActivity(User $person, string $unit = 'day'): array
    {
        $unit = $unit === 'month' ? 'month' : 'day';
        $today = CarbonImmutable::today();
        $from = $unit === 'month' ? $today->startOfMonth()->subMonths(11) : $today->subDays(29);
        $f = $from->toDateString();
        $t = $today->toDateString();

        $fields = ['payments' => 0, 'cash' => 0, 'card' => 0, 'leads' => 0, 'converted' => 0, 'notes' => 0, 'students' => 0, 'paid_students' => 0, 'paid_sum' => 0];
        $byDate = [];
        $add = function (string $date, array $values) use (&$byDate, $fields) {
            $date = substr($date, 0, 10);
            $byDate[$date] ??= $fields;
            foreach ($values as $k => $v) {
                $byDate[$date][$k] += (int) $v;
            }
        };

        $pay = Payment::query()->where('created_by', $person->id)->where('type', Payment::PAYMENT)->whereNull('reversed_at')
            ->whereDate('created_at', '>=', $f)->whereDate('created_at', '<=', $t)
            ->selectRaw("DATE(created_at) as d, count(*) as c,
                coalesce(sum(case when method = 'cash' then amount end), 0) as cash,
                coalesce(sum(case when method = 'card' then amount end), 0) as card")
            ->groupByRaw('DATE(created_at)')->get();
        foreach ($pay as $r) {
            $add($r->d, ['payments' => $r->c, 'cash' => $r->cash, 'card' => $r->card]);
        }

        $leads = Lead::query()->where('created_by', $person->id)
            ->whereDate('created_at', '>=', $f)->whereDate('created_at', '<=', $t)
            ->selectRaw("DATE(created_at) as d, count(*) as c, coalesce(sum(case when status = ? then 1 else 0 end), 0) as conv", [Lead::CONVERTED])
            ->groupByRaw('DATE(created_at)')->get();
        foreach ($leads as $r) {
            $add($r->d, ['leads' => $r->c, 'converted' => $r->conv]);
        }

        $notes = \App\Models\LeadNote::query()->where('user_id', $person->id)->where('type', 'note')->whereIn('lead_id', Lead::query()->select('id'))
            ->whereDate('created_at', '>=', $f)->whereDate('created_at', '<=', $t)
            ->selectRaw('DATE(created_at) as d, count(*) as c')->groupByRaw('DATE(created_at)')->get();
        foreach ($notes as $r) {
            $add($r->d, ['notes' => $r->c]);
        }

        $students = \App\Models\AuditLog::query()->where('user_id', $person->id)->where('action', 'student.created')->when(\App\Support\BranchContext::id(), fn ($q, $id) => $q->where('branch_id', $id))
            ->whereDate('created_at', '>=', $f)->whereDate('created_at', '<=', $t)
            ->selectRaw('DATE(created_at) as d, count(*) as c')->groupByRaw('DATE(created_at)')->get();
        foreach ($students as $r) {
            $add($r->d, ['students' => $r->c]);
        }

        // Shu xodim ro'yxatga olgan yangi o'quvchilardan to'lov qilganlar: soni va to'lov summasi
        $mine = User::query()->where('role', Role::Student)->where('created_by', $person->id)
            ->when(\App\Support\BranchContext::id(), fn ($q, $id) => $q->where('users.branch_id', $id))
            ->whereDate('created_at', '>=', $f)->whereDate('created_at', '<=', $t)
            ->get(['id', 'created_at']);
        if ($mine->isNotEmpty()) {
            $paidBy = Payment::query()->whereIn('student_id', $mine->pluck('id'))->where('type', Payment::PAYMENT)->whereNull('reversed_at')
                ->selectRaw('student_id, coalesce(sum(amount), 0) as s')->groupBy('student_id')->pluck('s', 'student_id');
            foreach ($mine as $stu) {
                if (isset($paidBy[$stu->id])) {
                    $add($stu->created_at->toDateString(), ['paid_students' => 1, 'paid_sum' => $paidBy[$stu->id]]);
                }
            }
        }

        // Davrlarga yig'ish (eng yangisi tepada)
        $periods = [];
        if ($unit === 'day') {
            for ($d = $today; $d->gte($from); $d = $d->subDay()) {
                $periods[$d->toDateString()] = ['label' => $d->format('d.m.Y'), 'dates' => [$d->toDateString()]];
            }
        } else {
            for ($m = $today->startOfMonth(); $m->gte($from); $m = $m->subMonthNoOverflow()) {
                $periods[$m->format('Y-m')] = ['label' => Viz::label($m->format('Y-m')), 'dates' => []];
            }
            foreach (array_keys($byDate) as $date) {
                $periods[substr($date, 0, 7)]['dates'][] = $date;
            }
        }

        $rows = [];
        $totals = $fields;
        foreach ($periods as $key => $p) {
            $row = $fields;
            foreach ($p['dates'] as $date) {
                foreach ($byDate[$date] ?? [] as $k => $v) {
                    $row[$k] += $v;
                }
            }
            foreach ($row as $k => $v) {
                $totals[$k] += $v;
            }
            $rows[] = ['key' => $key, 'label' => $p['label'], 'total' => $row['cash'] + $row['card']] + $row;
        }

        $totals['total'] = $totals['cash'] + $totals['card'];

        return ['unit' => $unit, 'rows' => $rows, 'totals' => $totals];
    }
}
