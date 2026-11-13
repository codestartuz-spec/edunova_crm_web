<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\Backup;
use App\Services\BackupService;
use App\Services\Telegram\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use ZipArchive;

/** v14: parollangan ZIP (AES-256), SHA-256, Telegram (faqat Admin ID), backups jadvali. */
class V14BackupTest extends TestCase
{
    use RefreshDatabase;

    private string $dbFile;

    protected function setUp(): void
    {
        parent::setUp();
        if (config('database.default') !== 'sqlite') {
            $this->markTestSkipped('Faqat sqlite.');
        }
        $this->dbFile = tempnam(sys_get_temp_dir(), 'edunova_bk_');
        file_put_contents($this->dbFile, 'FAKE-DB-CONTENT');
        config(['database.connections.sqlite.database' => $this->dbFile]);
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);
        File::cleanDirectory($dir);
    }

    protected function tearDown(): void
    {
        File::cleanDirectory(storage_path('app/backups'));
        @unlink($this->dbFile);
        parent::tearDown();
    }

    public function test_backup_creates_encrypted_zip_with_matching_sha_and_record(): void
    {
        $this->artisan('app:backup')->assertExitCode(0);

        $b = Backup::firstOrFail();
        $this->assertSame('ok', $b->status);
        $this->assertSame('schedule', $b->trigger);
        $path = app(BackupService::class)->path($b);
        $this->assertNotNull($path);
        $this->assertSame($b->size, filesize($path));

        $zip = new ZipArchive;
        $zip->open($path);
        $this->assertSame(false, $zip->getFromName('SHA256.txt'), 'Parolsiz o\'qib bo\'lmasligi kerak');
        $zip->setPassword(app(BackupService::class)->zipPassword());
        $sha = trim(explode(' ', (string) $zip->getFromName('SHA256.txt'))[0]);
        $zip->close(); // Windows'da ochiq fayl keyingi testlarni buzmasligi uchun
        $this->assertSame($b->sha256, $sha);
        $this->assertSame(64, strlen($sha));
    }

    public function test_telegram_receives_document_only_for_admin_ids(): void
    {
        AppSetting::put('telegram.token', '123456:'.str_repeat('A', 35), true);
        app(TelegramService::class)->addAdmin(999, 'Elshod');
        $this->user(Role::SAdmin, attrs: ['telegram_chat_id' => 777]);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        $this->artisan('app:backup')->assertExitCode(0);

        $this->assertSame(['999' => 'ok'], Backup::firstOrFail()->telegram);
        Http::assertSentCount(1);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'sendDocument'));
    }

    public function test_telegram_failure_is_recorded_but_backup_stays_ok(): void
    {
        AppSetting::put('telegram.token', '123456:'.str_repeat('A', 35), true);
        app(TelegramService::class)->addAdmin(999);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => false, 'description' => 'chat not found'], 400)]);

        $this->artisan('app:backup')->assertExitCode(0);

        $b = Backup::firstOrFail();
        $this->assertSame('ok', $b->status);
        $this->assertSame('chat not found', $b->telegram['999']);
    }

    public function test_backup_page_and_actions_are_sadmin_only(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $director = $this->user(Role::Director, permissions: ['system.view']);

        $this->actingAs($director)->get(route('backups.index'))->assertForbidden();
        $this->actingAs($sadmin)->get(route('backups.index'))->assertOk();

        $this->artisan('app:backup')->assertExitCode(0);
        $b = Backup::firstOrFail();
        $this->assertSame('ok', $b->status, (string) $b->error);
        $this->assertNotNull(app(BackupService::class)->path($b), 'ZIP fayl topilmadi: '.$b->file);

        $this->actingAs($sadmin)->post(route('backups.download', $b), ['password' => 'xato'])->assertForbidden();
        $this->actingAs($sadmin)->post(route('backups.download', $b), ['password' => 'parol12345'])->assertOk();
        $this->actingAs($sadmin)->post(route('backups.password'), ['password' => 'parol12345'])->assertSessionHas('zip_password');
    }

    public function test_schedule_runs_at_0100_tashkent(): void
    {
        $event = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())->first(fn ($e) => str_contains($e->command, 'app:backup'));
        $this->assertSame('0 1 * * *', $event->expression);
        $this->assertSame('Asia/Tashkent', (string) $event->timezone);
    }
}
