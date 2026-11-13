<?php

namespace App\Services\Legacy;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\DirectorPermissions;
use App\Support\PermissionRegistry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * v15: eski (masalan, temuriy-crm.uz) Edunova bazasi dampidagi ma'lumotlarni HOZIRGI tizimga ko'chiradi.
 *
 *  - Fayldagi SQL bajarilmaydi, faqat qatorlari o'qiladi (SqlDumpReader). Jadvallar tuzilmasi o'zgarmaydi.
 *  - Hozirgi sAdmin hisobi saqlanadi; fayldagi asosiy sAdmin shu hisobga birlashtiriladi, boshqa sAdminlar
 *    DIREKTORga aylantiriladi (ruxsatlarsiz).
 *  - Sozlamalar (Telegram bot, SMS shlyuzi, huquqiy hujjatlar, sertifikat sozlamalari, zaxira tarixi) tegilmaydi.
 *  - Hammasi BITTA tranzaksiyada: xato bo'lsa, hech narsa o'zgarmaydi.
 */
class LegacyImporter
{
    /** Eski bazadan ko'chiriladigan jadvallar. */
    public const COPY = [
        'branches', 'users', 'user_branches', 'user_permissions',
        'lead_sources', 'leads', 'lead_notes',
        'courses', 'course_videos', 'course_audios', 'course_questions', 'test_attempts', 'books',
        'rooms', 'lesson_times', 'price_plans', 'discount_campaigns', 'holidays',
        'groups', 'group_days', 'group_students', 'attendance_sessions', 'attendances',
        'wallets', 'wallet_transactions', 'balance_transactions', 'payments', 'payouts',
        'cash_requests', 'cash_closings', 'expense_categories',
        'student_notes', 'sms_templates',
        'audit_logs', 'notifications', 'notification_recipients',
        'ai_chats', 'ai_messages', 'import_batches', 'task_dismissals', 'app_versions',
    ];

    /** Ko'chirilmaydigan, lekin foydalanuvchilarga bog'liq bo'lgani uchun tozalanadigan jadvallar (seans/token turi). */
    public const WIPE_EXTRA = [
        // SMS tarixi va navbati ko'chirilmaydi: eski xabarlar yangi tizimda yuborilib ketmasligi kerak
        'sms_messages', 'jobs', 'failed_jobs',
        'submission_tokens', 'device_tokens', 'personal_access_tokens', 'password_reset_codes', 'password_reset_logs',
        'push_subscriptions', 'legal_acceptances', 'certificates',
        // Filial obuna to'lovlari va modem hisobi: filial ID lari almashgani uchun avval saqlab olinadi, keyin kod bo'yicha qaytariladi
        'branch_payments', 'sms_branch_usage',
    ];

    /** Foydalanuvchilarga murojaat qiluvchi ustunlar (asosiy sAdmin ID sini almashtirish uchun). */
    public const USER_REFS = [
        'ai_chats' => ['user_id'], 'ai_messages' => ['user_id'], 'app_versions' => ['updated_by'],
        'attendances' => ['student_id'], 'attendance_sessions' => ['taken_by'], 'audit_logs' => ['user_id'],
        'balance_transactions' => ['created_by', 'student_id'], 'cash_closings' => ['closed_by'],
        'cash_requests' => ['decided_by', 'requested_by'], 'course_audios' => ['created_by'],
        'course_questions' => ['created_by'], 'course_videos' => ['created_by'], 'groups' => ['created_by', 'deleted_by', 'teacher_id'],
        'group_days' => ['teacher_id'], 'group_students' => ['added_by', 'removed_by', 'student_id'],
        'import_batches' => ['user_id'], 'leads' => ['created_by', 'student_id'], 'lead_notes' => ['user_id'],
        'notifications' => ['sent_by'], 'notification_recipients' => ['user_id'],
        'payments' => ['created_by', 'refund_confirmed_by', 'refund_rejected_by', 'reversed_by', 'student_id'],
        'payouts' => ['created_by', 'recipient_id'], 'sms_messages' => ['created_by', 'recipient_id'],
        'student_notes' => ['closed_by', 'student_id', 'user_id'], 'task_dismissals' => ['user_id'],
        'test_attempts' => ['student_id'], 'user_branches' => ['granted_by', 'user_id'], 'user_permissions' => ['user_id'],
        'wallet_transactions' => ['created_by'], 'users' => ['created_by'],
    ];

    /** Dump bo'yicha va baza bo'yicha tekshiriladigan yig'indilar (ma'lumot o'tkazilganini isbotlaydi). */
    private const TOTALS = ['students', 'balance_sum', 'payments_count', 'payments_sum', 'groups', 'group_students', 'wallets_sum', 'payouts_sum'];

    /**
     * Faylni skanerlaydi (bazaga tegmaydi).
     *
     * @return array<string,mixed>
     */
    public function inspect(string $path): array
    {
        $tables = [];
        $columns = [];
        $sadmins = [];
        $migrations = [];
        $totals = array_fill_keys(self::TOTALS, 0);
        $users = [];

        foreach ((new SqlDumpReader($path))->inserts() as $s) {
            $t = $s['table'];
            $tables[$t] = ($tables[$t] ?? 0) + count($s['rows']);
            $columns[$t] = $s['columns'];
            foreach ($s['rows'] as $r) {
                $row = array_combine($s['columns'], $r);
                match ($t) {
                    'users' => $this->scanUser($row, $sadmins, $totals, $users),
                    'migrations' => $migrations[] = $row['migration'] ?? '',
                    'payments' => $this->scanPayment($row, $totals),
                    'groups' => $totals['groups']++,
                    'group_students' => $totals['group_students']++,
                    'wallets' => $totals['wallets_sum'] += (int) ($row['balance'] ?? 0),
                    'payouts' => $totals['payouts_sum'] += (int) ($row['amount'] ?? 0),
                    default => null,
                };
            }
        }

        $errors = [];
        $warnings = [];
        foreach (['users', 'branches'] as $need) {
            if (! isset($tables[$need])) {
                $errors[] = "Faylda «{$need}» jadvali yo'q: bu Edunova bazasining dampi emas.";
            }
        }
        if (! in_array(true, array_map(fn ($m) => str_contains((string) $m, 'create_users_table'), $migrations), true) && $migrations !== []) {
            $warnings[] = "Migratsiyalar ro'yxati kutilgan ko'rinishda emas.";
        }
        if ($sadmins === [] && isset($tables['users'])) {
            $warnings[] = "Faylda sAdmin yo'q: hozirgi sAdmin hisobi saqlanadi.";
        }

        $unknownCols = [];
        $unknownTables = [];
        foreach ($tables as $t => $_) {
            if (! Schema::hasTable($t)) {
                $unknownTables[] = $t;

                continue;
            }
            $extra = array_values(array_diff($columns[$t], Schema::getColumnListing($t)));
            if ($extra) {
                $unknownCols[$t] = $extra;
            }
        }

        return [
            'tables' => $tables, 'columns' => $columns, 'sadmins' => $sadmins, 'totals' => $totals,
            'last_migration' => $migrations ? end($migrations) : null, 'migration_count' => count($migrations),
            'unknown_tables' => $unknownTables, 'unknown_columns' => $unknownCols,
            'skipped_tables' => array_values(array_diff(array_keys($tables), self::COPY, ['migrations'])),
            'errors' => $errors, 'warnings' => $warnings, 'users' => $users,
        ];
    }

    private function scanUser(array $row, array &$sadmins, array &$totals, array &$users): void
    {
        $users[(int) $row['id']] = ['username' => (string) ($row['username'] ?? ''), 'email' => $row['email'] ?? null, 'role' => $row['role'] ?? null];
        if (($row['role'] ?? null) === 'sadmin') {
            $sadmins[] = ['id' => (int) $row['id'], 'name' => (string) ($row['name'] ?? ''), 'username' => (string) ($row['username'] ?? ''), 'phone' => $row['phone'] ?? null];
        }
        if (($row['role'] ?? null) === 'student') {
            $totals['students']++;
            $totals['balance_sum'] += (int) ($row['balance'] ?? 0);
        }
    }

    private function scanPayment(array $row, array &$totals): void
    {
        if (($row['type'] ?? null) === 'payment' && empty($row['reversed_at'])) {
            $totals['payments_count']++;
            $totals['payments_sum'] += (int) ($row['amount'] ?? 0);
        }
    }

    /**
     * Ko'chirishni bajaradi.
     *
     * @param  array{primary?:int|null}  $opts  primary - fayldagi qaysi sAdmin (ID) hozirgi sAdminga birlashtiriladi
     * @param  null|callable(string,int):void  $progress
     * @return array<string,mixed> hisobot
     */
    public function run(string $path, array $opts = [], ?callable $progress = null): array
    {
        $say = fn (string $label, int $pct) => $progress ? $progress($label, $pct) : null;
        $started = microtime(true);

        $say('Fayl tekshirilmoqda', 3);
        $info = $this->inspect($path);
        if ($info['errors']) {
            throw new RuntimeException(implode(' ', $info['errors']));
        }

        $current = User::where('role', 'sadmin')->whereNull('archived_at')->orderBy('id')->first();
        if (! $current) {
            throw new RuntimeException('Hozirgi tizimda sAdmin topilmadi.');
        }

        $sadminIds = array_column($info['sadmins'], 'id');
        $primary = isset($opts['primary']) && $opts['primary'] ? (int) $opts['primary'] : ($sadminIds ? min($sadminIds) : null);
        if ($primary !== null && $sadminIds !== [] && ! in_array($primary, $sadminIds, true)) {
            throw new RuntimeException("Tanlangan sAdmin (ID {$primary}) faylda topilmadi.");
        }
        $userMap = $primary ? [$primary => (int) $current->id] : [];

        // Foydalanuvchi nomi/email to'qnashuvi: hozirgi sAdmin bilan (boshqa hammasi o'chiriladi)
        foreach ($info['users'] as $id => $u) {
            if ($id === $primary) {
                continue;
            }
            if ($id === (int) $current->id) {
                throw new RuntimeException("Faylda hozirgi sAdmin ID si ({$id}) bilan to'qnashuvchi foydalanuvchi bor. Fayldagi sAdmin ID sini asosiy qilib tanlang yoki administrator bilan bog'laning.");
            }
            if (strcasecmp($u['username'], (string) $current->username) === 0) {
                throw new RuntimeException("Fayldagi foydalanuvchi (ID {$id}) logini hozirgi sAdmin logini («{$current->username}») bilan bir xil. Avval birini o'zgartiring.");
            }
            if ($current->email && $u['email'] && strcasecmp((string) $u['email'], (string) $current->email) === 0) {
                throw new RuntimeException("Fayldagi foydalanuvchi (ID {$id}) emaili hozirgi sAdmin emaili bilan bir xil.");
            }
        }

        $imported = [];
        $skipped = [];
        $directors = [];
        $droppedPermissions = 0;
        $copy = array_flip(self::COPY);
        $secrets = new LegacySecrets();
        $secretStats = ['ok' => 0, 'reencrypted' => 0, 'plain' => 0, 'lost' => 0, 'empty' => 0];

        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();

        try {
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('PRAGMA defer_foreign_keys = ON'); // tashqi tranzaksiya ichida foreign_keys=OFF ishlamaydi
            }
            $extras = $this->snapshotBranchExtras();
            $say("Eski ma'lumotlar tozalanmoqda", 8);
            foreach (array_merge(array_reverse(self::COPY), self::WIPE_EXTRA) as $t) {
                if ($t === 'users' || ! Schema::hasTable($t)) {
                    continue;
                }
                DB::table($t)->delete();
            }
            DB::table('users')->where('id', '!=', $current->id)->delete();

            $total = max(1, array_sum(array_intersect_key($info['tables'], $copy)));
            $done = 0;

            foreach ((new SqlDumpReader($path))->inserts() as $s) {
                $t = $s['table'];
                if (! isset($copy[$t]) || ! Schema::hasTable($t)) {
                    continue;
                }
                $dest = array_flip(Schema::getColumnListing($t));
                $cols = $s['columns'];
                $refs = array_flip(self::USER_REFS[$t] ?? []);
                $buffer = [];

                foreach ($s['rows'] as $r) {
                    $row = array_combine($cols, $r);

                    if ($t === 'users') {
                        if ($primary !== null && (int) $row['id'] === $primary) {
                            continue;
                        }
                        if (($row['role'] ?? null) === 'sadmin') {
                            $row['role'] = 'director';
                            $row['branch_id'] = null;
                            $directors[] = ['id' => (int) $row['id'], 'name' => $row['name'], 'username' => $row['username']];
                        }
                    }
                    // Eski bazadagi direktor va hodimlarning ruxsatlari ko'chirilmaydi: hodimlar ruxsatsiz qoladi,
                    // direktorlarga esa pastda umumiy direktor shabloni qo'llanadi.
                    if ($t === 'user_permissions') {
                        $droppedPermissions++;

                        continue;
                    }
                    if (in_array($t, ['user_permissions', 'user_branches'], true) && in_array((int) $row['user_id'], $sadminIds, true)) {
                        $t === 'user_permissions' && $droppedPermissions++;

                        continue;
                    }
                    if ($t === 'user_permissions' && ! PermissionRegistry::exists((string) $row['permission'])) {
                        $droppedPermissions++;

                        continue;
                    }
                    foreach ($refs as $col => $_) {
                        if (isset($row[$col]) && isset($userMap[(int) $row[$col]])) {
                            $row[$col] = $userMap[(int) $row[$col]];
                        }
                    }

                    if ($t === 'branches' && array_key_exists('eskiz_password', $row)) {
                        [$row['eskiz_password'], $state] = $secrets->normalize($row['eskiz_password']);
                        $secretStats[$state]++;
                    }

                    $buffer[] = array_intersect_key($row, $dest);
                    $imported[$t] = ($imported[$t] ?? 0) + 1;
                    $done++;
                }

                if ($buffer) {
                    $perInsert = max(1, intdiv(900, max(1, count($buffer[0]))));
                    foreach (array_chunk($buffer, $perInsert) as $chunk) {
                        DB::table($t)->insert($chunk);
                    }
                }
                $say("«{$t}» ko'chirildi", 10 + (int) (80 * min(1, $done / $total)));
            }

            $say('Yangi qoidalar qo\'llanmoqda', 92);
            $this->extendUslugaTiers();
            $this->restoreBranchExtras($extras, (int) $current->id);
            $directorCount = DirectorPermissions::applyToAll();

            foreach (self::COPY as $t) {
                $imported[$t] ??= 0;
            }
            $skipped = array_values(array_diff(array_keys($info['tables']), self::COPY, ['migrations']));

            $say('Tekshiruv', 95);
            $verify = $this->verify($info, $imported);
            if ($secretStats['lost'] > 0) {
                $verify['warnings'][] = "Filiallar Eskiz paroli: {$secretStats['lost']} tasi eski kalit bilan ochilmadi (LEGACY_APP_KEY yo'q yoki noto'g'ri). Ular bo'sh qoldirildi, parolni Filiallar sozlamasidan qayta kiriting.";
            }
            if ($secretStats['reencrypted'] + $secretStats['plain'] > 0) {
                $verify['checks'][] = ['label' => 'Filiallar Eskiz paroli', 'ok' => $secretStats['lost'] === 0, 'detail' => "qayta shifrlandi: {$secretStats['reencrypted']} · shifrlandi: {$secretStats['plain']} · ochilmadi: {$secretStats['lost']}"];
            }
            if ($verify['fatal']) {
                throw new RuntimeException('Tekshiruvdan o\'tmadi: '.implode('; ', $verify['fatal']));
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Schema::enableForeignKeyConstraints();

            throw $e;
        }
        Schema::enableForeignKeyConstraints();

        $say('Yakunlanmoqda', 98);
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->where(fn ($q) => $q->whereNull('user_id')->orWhere('user_id', '!=', $current->id))->delete();
        }
        Cache::flush();

        $report = [
            'finished_at' => now()->toDateTimeString(),
            'seconds' => round(microtime(true) - $started, 1),
            'primary_dump_id' => $primary, 'sadmin_id' => (int) $current->id,
            'imported' => $imported, 'skipped_tables' => $skipped, 'directors' => $directors,
            'dropped_permissions' => $droppedPermissions, 'directors_with_template' => $directorCount, 'unknown_columns' => $info['unknown_columns'],
            'dump_totals' => $info['totals'], 'db_totals' => $verify['totals'], 'checks' => $verify['checks'], 'warnings' => $verify['warnings'],
        ];

        try {
            AuditLog::record('system.legacy_import', null, "Eski baza ko'chirildi: ".array_sum($imported)." qator, ".count($directors).' direktor', [], ['imported' => array_sum($imported)], null);
        } catch (\Throwable) {
            // jurnal yozilmasa ham ko'chirish tugagan
        }

        return $report;
    }

    /** v14 dagi usluga 8 pog'ona qoidasi: eski 4-pog'ona summalari 5-8 ga nusxalanadi. */
    /** Eski bazada yo'q ma'lumotlar (obuna to'lovlari, modem limiti va hisobi) - filial kodi bo'yicha saqlab olinadi. */
    private function snapshotBranchExtras(): array
    {
        $codes = DB::table('branches')->pluck('code', 'id');
        $out = ['limits' => [], 'payments' => [], 'usage' => []];

        if (Schema::hasColumn('branches', 'sms_modem_daily_limit')) {
            foreach (DB::table('branches')->get(['code', 'sms_modem_daily_limit']) as $b) {
                $out['limits'][$b->code] = (int) $b->sms_modem_daily_limit;
            }
        }
        if (Schema::hasTable('branch_payments')) {
            foreach (DB::table('branch_payments')->get() as $r) {
                if (isset($codes[$r->branch_id])) {
                    $out['payments'][] = ['code' => $codes[$r->branch_id], 'amount' => $r->amount, 'method' => $r->method, 'note' => $r->note, 'created_at' => $r->created_at];
                }
            }
        }
        if (Schema::hasTable('sms_branch_usage')) {
            foreach (DB::table('sms_branch_usage')->get() as $r) {
                if (isset($codes[$r->branch_id])) {
                    $out['usage'][] = ['code' => $codes[$r->branch_id], 'day' => $r->day, 'count' => $r->count];
                }
            }
        }

        return $out;
    }

    private function restoreBranchExtras(array $extras, int $sadminId): void
    {
        $ids = DB::table('branches')->pluck('id', 'code');

        foreach ($extras['limits'] as $code => $limit) {
            if (isset($ids[$code]) && Schema::hasColumn('branches', 'sms_modem_daily_limit')) {
                DB::table('branches')->where('id', $ids[$code])->update(['sms_modem_daily_limit' => $limit]);
            }
        }
        foreach ($extras['payments'] as $r) {
            if (isset($ids[$r['code']])) {
                DB::table('branch_payments')->insert(['branch_id' => $ids[$r['code']], 'amount' => $r['amount'], 'method' => $r['method'], 'note' => $r['note'], 'created_by' => $sadminId, 'created_at' => $r['created_at']]);
            }
        }
        foreach ($extras['usage'] as $r) {
            if (isset($ids[$r['code']])) {
                DB::table('sms_branch_usage')->insert(['branch_id' => $ids[$r['code']], 'day' => $r['day'], 'count' => $r['count']]);
            }
        }
    }

    private function extendUslugaTiers(): void
    {
        if (! Schema::hasColumn('branches', 'usluga_rates')) {
            return;
        }
        DB::table('branches')->whereNotNull('usluga_rates')->orderBy('id')->each(function ($b) {
            $rates = json_decode((string) $b->usluga_rates, true);
            if (! is_array($rates) || ! isset($rates[4])) {
                return;
            }
            foreach ([5, 6, 7, 8] as $t) {
                $rates[$t] ??= $rates[4];
            }
            DB::table('branches')->where('id', $b->id)->update(['usluga_rates' => json_encode($rates)]);
        });
    }

    /**
     * Ko'chirishdan keyingi tekshiruv.
     *
     * @return array{fatal:list<string>,warnings:list<string>,totals:array<string,int>,checks:list<array{label:string,ok:bool,detail:string}>}
     */
    private function verify(array $info, array $imported): array
    {
        $fatal = [];
        $warnings = [];
        $checks = [];

        $totals = [
            'students' => (int) DB::table('users')->where('role', 'student')->count(),
            'balance_sum' => (int) DB::table('users')->where('role', 'student')->sum('balance'),
            'payments_count' => (int) DB::table('payments')->where('type', 'payment')->whereNull('reversed_at')->count(),
            'payments_sum' => (int) DB::table('payments')->where('type', 'payment')->whereNull('reversed_at')->sum('amount'),
            'groups' => (int) DB::table('groups')->count(),
            'group_students' => (int) DB::table('group_students')->count(),
            'wallets_sum' => (int) DB::table('wallets')->sum('balance'),
            'payouts_sum' => (int) DB::table('payouts')->sum('amount'),
        ];
        $labels = [
            'students' => "O'quvchilar soni", 'balance_sum' => "O'quvchilar balansi yig'indisi", 'payments_count' => "To'lovlar soni",
            'payments_sum' => "To'lovlar summasi", 'groups' => 'Guruhlar soni', 'group_students' => "Guruh a'zoliklari",
            'wallets_sum' => "Kassa (hamyonlar) yig'indisi", 'payouts_sum' => "Maosh to'lovlari summasi",
        ];
        foreach ($totals as $k => $v) {
            $ok = $v === (int) $info['totals'][$k];
            $checks[] = ['label' => $labels[$k], 'ok' => $ok, 'detail' => "fayl: {$info['totals'][$k]} · baza: {$v}"];
            if (! $ok) {
                $fatal[] = "{$labels[$k]} mos kelmadi (fayl {$info['totals'][$k]}, baza {$v})";
            }
        }

        // Qator sonlari: users va user_permissions filtrlanadi, qolganlari aynan teng bo'lishi shart
        foreach (self::COPY as $t) {
            if (in_array($t, ['users', 'user_permissions', 'user_branches'], true) || ! isset($info['tables'][$t]) || ! Schema::hasTable($t)) {
                continue;
            }
            $db = (int) DB::table($t)->count();
            if ($db !== (int) $info['tables'][$t]) {
                $fatal[] = "«{$t}»: faylda {$info['tables'][$t]}, bazada {$db} qator";
            }
        }
        $checks[] = ['label' => 'Jadvallar qator sonlari', 'ok' => true, 'detail' => 'barcha jadvallarda fayl bilan bir xil'];

        // Bog'lanmagan yozuvlar (ma'lumot)
        $orphans = [
            ["To'lov → o'quvchi", 'payments', 'student_id', 'users'], ["Guruh a'zoligi → guruh", 'group_students', 'group_id', 'groups'],
            ["Guruh a'zoligi → o'quvchi", 'group_students', 'student_id', 'users'], ['Davomat → o\'quvchi', 'attendances', 'student_id', 'users'],
            ["Balans harakati → o'quvchi", 'balance_transactions', 'student_id', 'users'],
        ];
        foreach ($orphans as [$label, $t, $col, $ref]) {
            $n = (int) DB::table($t)->whereNotNull($col)->whereNotIn($col, DB::table($ref)->select('id'))->count();
            $checks[] = ['label' => "Bog'lanmagan: {$label}", 'ok' => $n === 0, 'detail' => $n === 0 ? 'yo\'q' : "{$n} ta"];
            if ($n) {
                $warnings[] = "{$label}: {$n} ta bog'lanmagan yozuv";
            }
        }

        return ['fatal' => $fatal, 'warnings' => $warnings, 'totals' => $totals, 'checks' => $checks];
    }
}
