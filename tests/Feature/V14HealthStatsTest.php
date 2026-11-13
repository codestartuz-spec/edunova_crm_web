<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\Backup;
use App\Models\HealthCheck;
use App\Services\HealthService;
use App\Services\Telegram\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v14: "Tizim holati" (HealthService) va Statistika KPI JSON endpointi. */
class V14HealthStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_checks_have_valid_shape_and_cover_all_groups(): void
    {
        $rows = app(HealthService::class)->checks();

        $this->assertNotEmpty($rows);
        foreach ($rows as $r) {
            $this->assertContains($r['status'], ['ok', 'warn', 'fail']);
            $this->assertArrayHasKey($r['group'], HealthService::GROUPS);
            $this->assertNotSame('', $r['detail']);
        }
        $this->assertEqualsCanonicalizing(array_keys(HealthService::GROUPS), array_values(array_unique(array_column($rows, 'group'))));
    }

    public function test_missing_backup_is_a_failure_and_recent_backup_is_ok(): void
    {
        $find = fn () => collect(app(HealthService::class)->checks())->firstWhere('key', 'backup');

        $this->assertSame('fail', $find()['status']);

        Backup::create(['status' => 'ok', 'trigger' => 'schedule', 'file' => 'x.zip', 'size' => 10, 'sha256' => str_repeat('a', 64), 'telegram' => []]);
        $this->assertSame('ok', $find()['status']);

        Backup::query()->update(['created_at' => now()->subHours(30)]);
        $this->assertSame('fail', $find()['status']);
    }

    public function test_run_stores_history_and_alerts_admins_only_about_new_failures(): void
    {
        AppSetting::put('telegram.token', '123456:'.str_repeat('A', 35), true);
        app(TelegramService::class)->addAdmin(999, 'Elshod');
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
        Cache::forget('health.failing');

        $rec = app(HealthService::class)->run();
        $this->assertSame('fail', $rec->status);          // zaxira yo'q va sAdmin yo'q
        $this->assertGreaterThan(0, $rec->fail_count);
        $this->assertDatabaseCount('health_checks', 1);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'sendMessage') && $r['chat_id'] == 999 && str_contains($r['text'], 'Kunlik zaxira'));

        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
        app(HealthService::class)->run();                  // o'zgarish yo'q - qayta xabar yo'q
        Http::assertNothingSent();
        $this->assertDatabaseCount('health_checks', 2);
    }

    public function test_old_history_is_pruned(): void
    {
        HealthCheck::create(['status' => 'ok', 'results' => [], 'created_at' => now()->subDays(40)]);
        app(HealthService::class)->run(false);

        $this->assertSame(1, HealthCheck::count());
    }

    public function test_system_status_page_renders_for_sadmin_and_run_is_sadmin_only(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $viewer = $this->user(Role::Director, permissions: ['system.view']);

        $this->actingAs($sadmin)->get('/system-status')->assertOk()->assertSee('Tizim holati')->assertSee('Infratuzilma')->assertSee('Hozir tekshirish');
        $this->actingAs($viewer)->get('/system-status')->assertOk()->assertDontSee('Hozir tekshirish');
        $this->actingAs($viewer)->post(route('system-status.run'))->assertForbidden();
        $this->actingAs($sadmin)->post(route('system-status.run'))->assertRedirect();
        $this->assertDatabaseCount('health_checks', 1);
    }

    public function test_statistics_kpi_feed_returns_cards_with_deltas_and_respects_money_permission(): void
    {
        $branch = $this->branch();
        $full = $this->user(Role::Admin, $branch, ['statistics.view', 'payments.view', 'finance.view']);
        $plain = $this->user(Role::Manager, $branch, ['statistics.view']);

        $keys = fn ($u) => collect($this->actingAs($u)->getJson('/statistics/data?from=2026-09-01&to=2026-09-30')->assertOk()->json('cards'))->pluck('key')->all();

        $this->assertContains('net_income', $keys($full));
        $this->assertContains('profit', $keys($full));
        $this->assertNotContains('net_income', $keys($plain));
        $this->assertContains('debtors', $keys($plain));

        $this->actingAs($full)->get('/statistics')->assertOk()->assertSee('data-kpi-feed', false)->assertSee('Naqt tushum')->assertSee('Plastik tushum');
    }

    public function test_statistics_data_requires_permission(): void
    {
        $none = $this->user(Role::Manager, $this->branch(), []);
        $this->actingAs($none)->getJson('/statistics/data')->assertForbidden();
    }
}
