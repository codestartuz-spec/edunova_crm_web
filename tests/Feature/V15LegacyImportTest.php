<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Services\Legacy\LegacyImporter;
use App\Services\Legacy\SqlDumpReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class V15LegacyImportTest extends TestCase
{
    use RefreshDatabase;

    private function dump(): string
    {
        $now = '2026-01-01 10:00:00';

        return <<<SQL
-- test dump
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `branches` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(7, 'Eski filial; "A"', 'E1', '$now', '$now');
INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `branch_id`, `balance`, `created_at`, `updated_at`) VALUES
(41, 'Eski Elshod', 'sadmin', 'x', 'sadmin', NULL, 0, '$now', '$now'),
(50, 'Murod', 'murod', 'x', 'sadmin', NULL, 0, '$now', '$now'),
(60, 'Ali O\\'g\\'li', 'ali', 'x', 'student', 7, -5000, '$now', '$now'),
(61, 'Vali', 'vali', 'x', 'student', 7, 12000, '$now', '$now');
INSERT INTO `user_permissions` (`id`, `user_id`, `permission`) VALUES
(1, 50, 'students.view'),
(2, 41, 'students.view');
INSERT INTO `payments` (`id`, `branch_id`, `student_id`, `amount`, `type`, `method`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 7, 60, 100000, 'payment', 'cash', 41, '$now', '$now');
INSERT INTO `sms_messages` (`id`, `branch_id`, `phone`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 7, '+998901112233', 'Eski xabar', 'queued', '$now', '$now');
SQL;
    }

    private function file(): string
    {
        $p = tempnam(sys_get_temp_dir(), 'dmp').'.sql';
        file_put_contents($p, $this->dump());

        return $p;
    }

    public function test_reader_parses_escapes_and_nulls(): void
    {
        $rows = [];
        foreach ((new SqlDumpReader($this->file()))->inserts() as $s) {
            $rows[$s['table']] = $s;
        }
        $this->assertSame("Eski filial; \"A\"", $rows['branches']['rows'][0][1]);
        $this->assertSame("Ali O'g'li", $rows['users']['rows'][2][1]);
        $this->assertNull($rows['users']['rows'][0][5] ?? null);
        $this->assertCount(4, $rows['users']['rows']);
    }

    public function test_import_remaps_sadmin_demotes_others_and_keeps_settings(): void
    {
        $oldBranch = $this->branch('Joriy');
        $sadmin = $this->user(Role::SAdmin, null, [], ['username' => 'elshod']);
        $junk = $this->student($oldBranch);
        AppSetting::put('telegram_bot_token', 'KEEP-ME');

        DB::table('sms_messages')->insert(['branch_id' => $oldBranch->id, 'phone' => '+998900000000', 'message' => 'joriy navbat', 'status' => 'queued', 'created_at' => now(), 'updated_at' => now()]);

        $report = app(LegacyImporter::class)->run($this->file(), ['primary' => 41]);

        $this->assertSame(0, DB::table('sms_messages')->count(), 'SMS tarixi/navbati ko\'chirilmasligi va tozalanishi kerak');
        $this->assertSame('KEEP-ME', AppSetting::get('telegram_bot_token'));
        $this->assertDatabaseMissing('users', ['id' => $junk->id, 'role' => 'student']);
        $this->assertSame(1, DB::table('users')->where('role', 'sadmin')->count());
        $this->assertSame('elshod', DB::table('users')->where('role', 'sadmin')->value('username'));
        $this->assertDatabaseHas('users', ['id' => 50, 'role' => 'director']);
        $this->assertSame(0, DB::table('user_permissions')->where('user_id', 50)->count());
        $this->assertDatabaseHas('users', ['id' => 60, 'role' => 'student', 'balance' => -5000]);
        $this->assertSame($sadmin->id, (int) DB::table('payments')->value('created_by'));
        $this->assertSame(1, DB::table('branches')->count());
        $this->assertSame(100000, $report['db_totals']['payments_sum']);
        $this->assertSame(2, $report['dump_totals']['students']);
    }

    public function test_import_drops_old_permissions_and_applies_director_template(): void
    {
        $this->branch('Joriy');
        $this->user(Role::SAdmin, null, [], ['username' => 'elshod']);
        \App\Support\DirectorPermissions::save(['students.view', 'branches.manage']);

        app(LegacyImporter::class)->run($this->file(), ['primary' => 41]);

        // Fayldagi ruxsatlar ko'chirilmaydi; direktor (ID 50) faqat umumiy shablonni oladi
        $this->assertEqualsCanonicalizing(['students.view', 'branches.manage'], \App\Models\User::find(50)->permissionKeys());
        $this->assertSame(2, DB::table('user_permissions')->count());
    }

    public function test_conflicting_username_aborts_without_changes(): void
    {
        $b = $this->branch('Joriy');
        $this->user(Role::SAdmin, null, [], ['username' => 'murod']);
        $this->student($b);
        $before = DB::table('users')->count();

        try {
            app(LegacyImporter::class)->run($this->file(), ['primary' => 41]);
            $this->fail('exception expected');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('murod', $e->getMessage());
        }
        $this->assertSame($before, DB::table('users')->count());
    }

    public function test_only_sadmin_can_open_section(): void
    {
        $b = $this->branch();
        $this->actingAs($this->user(Role::Admin, $b))->get(route('legacy.index'))->assertForbidden();
        $this->actingAs($this->user(Role::SAdmin))->get(route('legacy.index'))->assertOk()->assertSee("Eski bazani ko'chirish");
    }

    public function test_web_flow_upload_review_run(): void
    {
        $admin = $this->user(Role::SAdmin, null, [], ['username' => 'elshod', 'password' => bcrypt('secret123')]);
        $this->actingAs($admin);

        $up = UploadedFile::fake()->createWithContent('dump.sql', $this->dump());
        $this->post(route('legacy.upload'), ['dump' => $up])->assertRedirect(route('legacy.review'));
        $this->get(route('legacy.review'))->assertOk()->assertSee('Murod');

        $this->post(route('legacy.run'), ['password' => 'wrong', 'confirm' => 'TASDIQLAYMAN', 'primary' => 41, 'backup' => 0])->assertSessionHas('error');
        $this->post(route('legacy.run'), ['password' => 'secret123', 'confirm' => 'ha', 'primary' => 41, 'backup' => 0])->assertSessionHasErrors('confirm');
        $this->post(route('legacy.run'), ['password' => 'secret123', 'confirm' => 'TASDIQLAYMAN', 'primary' => 41, 'backup' => 0])->assertRedirectContains('legacy-import/progress/');

        $this->assertDatabaseHas('users', ['id' => 60, 'role' => 'student']);
        $token = collect(glob(storage_path('app/legacy-import/progress-*.json')))->sortByDesc(fn ($f) => filemtime($f))->map(fn ($f) => substr(basename($f), 9, 16))->first();
        $this->get(route('legacy.progress', $token))->assertOk()->assertSee('Ko\'chirish');
        $st = $this->getJson(route('legacy.status', $token))->assertOk()->json();
        $this->assertSame('done', $st['status']);
        $this->assertSame(100, $st['percent']);
        $this->assertNotEmpty($st['log']);
        $this->get(route('legacy.index'))->assertOk()->assertSee('Maosh');
    }
}
