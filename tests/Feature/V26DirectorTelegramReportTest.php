<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Services\DailyReportService;
use App\Services\Telegram\DirectorTelegram;
use App\Services\Telegram\TelegramService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v26: direktor formasidan Telegram ID, hisobot vaqti/yoqish, havola orqali ulash, filial bo'yicha kunlik hisobot. */
class V26DirectorTelegramReportTest extends TestCase
{
    use RefreshDatabase;

    private function bot(array $reply = ['ok' => true, 'result' => ['username' => 'edu_bot']]): void
    {
        AppSetting::put('telegram.token', '123456:'.str_repeat('A', 35), true);
        Http::swap(new \Illuminate\Http\Client\Factory);
        Http::fake(['api.telegram.org/*' => Http::response($reply)]);
    }

    private function payload($d, array $extra = []): array
    {
        return $extra + ['name' => $d->name, 'username' => $d->username, 'phone' => '+998 90 111 2233'];
    }

    public function test_sadmin_saves_telegram_id_time_and_toggle(): void
    {
        $this->bot();
        $sadmin = $this->user(Role::SAdmin);
        $d = $this->user(Role::Director);

        $this->actingAs($sadmin)->get(route('directors.edit', $d->id))->assertOk()->assertSee('Telegram ID')->assertSee('Hisobot yuborish vaqti');

        $this->actingAs($sadmin)->put(route('directors.update', $d->id), $this->payload($d, ['telegram_chat_id' => '987654321', 'report_time' => '07:30', 'report_enabled' => '1']))
            ->assertRedirect(route('directors.index'));

        $d->refresh();
        $this->assertSame(987654321, (int) $d->telegram_chat_id);
        $this->assertSame('07:30', $d->report_time);
        $this->assertTrue($d->report_enabled);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'sendMessage') && $r['chat_id'] == 987654321);

        $this->actingAs($sadmin)->put(route('directors.update', $d->id), $this->payload($d, ['telegram_chat_id' => '987654321', 'report_time' => '07:30', 'report_enabled' => '0']));
        $this->assertFalse($d->fresh()->report_enabled);
    }

    public function test_invalid_time_and_id_and_unreachable_chat_are_rejected(): void
    {
        $this->bot();
        $sadmin = $this->user(Role::SAdmin);
        $d = $this->user(Role::Director);

        $this->actingAs($sadmin)->put(route('directors.update', $d->id), $this->payload($d, ['report_time' => '25:99']))->assertSessionHasErrors('report_time');
        $this->actingAs($sadmin)->put(route('directors.update', $d->id), $this->payload($d, ['telegram_chat_id' => 'abc', 'report_time' => '20:00']))->assertSessionHasErrors('telegram_chat_id');

        $this->bot(['ok' => false, 'error_code' => 403, 'description' => 'Forbidden: bot was blocked by the user']);
        $this->actingAs($sadmin)->put(route('directors.update', $d->id), $this->payload($d, ['telegram_chat_id' => '555000111', 'report_time' => '20:00']))->assertSessionHasErrors('telegram_chat_id');
        $this->assertNull($d->fresh()->telegram_chat_id);
    }

    public function test_telegram_id_must_be_unique_among_directors_and_can_be_cleared(): void
    {
        $this->bot();
        $sadmin = $this->user(Role::SAdmin);
        $a = $this->user(Role::Director);
        $b = $this->user(Role::Director);
        $a->forceFill(['telegram_chat_id' => 111222333])->save();

        $this->actingAs($sadmin)->put(route('directors.update', $b->id), $this->payload($b, ['telegram_chat_id' => '111222333', 'report_time' => '20:00']))->assertSessionHasErrors('telegram_chat_id');

        $this->actingAs($sadmin)->put(route('directors.update', $a->id), $this->payload($a, ['telegram_chat_id' => '', 'report_time' => '20:00', 'phone' => '+998 91 222 3344']));
        $this->assertNull($a->fresh()->telegram_chat_id);
    }

    public function test_changing_time_already_passed_today_starts_tomorrow(): void
    {
        $this->bot();
        $this->travelTo(now('Asia/Tashkent')->setTime(15, 0));
        $sadmin = $this->user(Role::SAdmin);
        $d = $this->user(Role::Director);

        $this->actingAs($sadmin)->put(route('directors.update', $d->id), $this->payload($d, ['report_time' => '09:00', 'report_enabled' => '1']));
        $this->assertSame(now('Asia/Tashkent')->toDateString(), $d->fresh()->report_last_sent_on->toDateString());

        $d2 = $this->user(Role::Director);
        $this->actingAs($sadmin)->put(route('directors.update', $d2->id), $this->payload($d2, ['report_time' => '23:00', 'report_enabled' => '1']));
        $this->assertNull($d2->fresh()->report_last_sent_on);
    }

    public function test_link_flow_connects_director_once(): void
    {
        $this->bot();
        $sadmin = $this->user(Role::SAdmin);
        $d = $this->user(Role::Director);

        $url = $this->actingAs($sadmin)->postJson(route('directors.telegram.link', $d->id))->assertOk()->json('url');
        $this->assertStringStartsWith('https://t.me/edu_bot?start=d', $url);
        $token = substr($url, strpos($url, 'start=') + 6);

        $this->actingAs($sadmin)->getJson(route('directors.telegram.status', $d->id))->assertJson(['linked' => false]);

        $secret = app(TelegramService::class)->secret();
        $hook = fn () => $this->postJson('/telegram/webhook', ['message' => ['text' => '/start '.$token, 'chat' => ['id' => 777888, 'type' => 'private'], 'from' => ['id' => 777888, 'username' => 'dir_user']]], ['X-Telegram-Bot-Api-Secret-Token' => $secret]);
        $hook()->assertOk();

        $this->assertSame(777888, (int) $d->fresh()->telegram_chat_id);
        $this->assertSame('dir_user', $d->fresh()->telegram_username);
        $this->actingAs($sadmin)->getJson(route('directors.telegram.status', $d->id))->assertJson(['linked' => true]);

        // bir martalik: qayta ishlatib bo'lmaydi
        $d->forceFill(['telegram_chat_id' => null])->save();
        $hook()->assertOk();
        $this->assertNull($d->fresh()->telegram_chat_id);
    }

    public function test_only_sadmin_can_use_telegram_endpoints(): void
    {
        $this->bot();
        $d = $this->user(Role::Director);
        $this->actingAs($this->user(Role::Admin))->postJson(route('directors.telegram.link', $d->id))->assertForbidden();
    }

    public function test_report_goes_only_to_assigned_branches_and_at_own_time(): void
    {
        $this->bot(['ok' => true, 'result' => []]);
        $a = $this->branch('Markaz');
        $b = $this->branch('Chilonzor');
        $d = $this->user(Role::Director);
        $d->forceFill(['telegram_chat_id' => 4242, 'all_branches' => false, 'report_time' => '18:30', 'report_enabled' => true])->save();
        DB::table('director_branches')->insert(['user_id' => $d->id, 'branch_id' => $a->id]);

        $svc = app(DailyReportService::class);
        $day = CarbonImmutable::parse('2026-11-20 18:00', 'Asia/Tashkent');
        $this->assertSame(0, $svc->send($day, onlyDue: true), 'vaqti kelmagan');
        Http::assertNothingSent();

        $this->assertSame(1, $svc->send($day->setTime(18, 31), onlyDue: true));
        Http::assertSent(fn ($r) => str_contains($r->url(), 'sendMessage') && $r['chat_id'] == 4242 && str_contains($r['text'], 'Markaz') && ! str_contains($r['text'], 'Chilonzor'));

        $this->assertSame(0, $svc->send($day->setTime(19, 0), onlyDue: true), 'bugun allaqachon yuborilgan');
        $this->assertSame(1, $svc->send($day->addDay()->setTime(18, 31), onlyDue: true), 'ertasi kuni yana');
    }

    public function test_disabled_director_gets_nothing_and_blocked_bot_clears_link(): void
    {
        $this->bot(['ok' => false, 'error_code' => 403, 'description' => 'Forbidden: bot was blocked by the user']);
        $this->branch('Markaz');
        $off = $this->user(Role::Director);
        $off->forceFill(['telegram_chat_id' => 1001, 'report_enabled' => false])->save();
        $on = $this->user(Role::Director);
        $on->forceFill(['telegram_chat_id' => 1002, 'report_enabled' => true, 'report_time' => '10:00'])->save();

        $day = CarbonImmutable::parse('2026-11-20 12:00', 'Asia/Tashkent');
        $recips = collect(app(DailyReportService::class)->recipients())->pluck('chat')->all();
        $this->assertNotContains(1001, $recips);
        $this->assertContains(1002, $recips);

        app(DailyReportService::class)->send($day, onlyDue: true);
        $this->assertNull($on->fresh()->telegram_chat_id, '403 bo\'lsa bog\'lanish tozalanadi');
        $this->assertNotNull($off->fresh()->telegram_chat_id);
    }

    public function test_long_reports_are_split_below_telegram_limit(): void
    {
        $parts = array_fill(0, 30, str_repeat('x', 400));
        $chunks = app(DailyReportService::class)->chunks($parts);
        $this->assertGreaterThan(1, count($chunks));
        foreach ($chunks as $c) {
            $this->assertLessThanOrEqual(4096, mb_strlen($c));
        }
        $this->assertSame(30, substr_count(implode('', $chunks), str_repeat('x', 400)) );
    }

    public function test_test_report_button_sends_and_index_shows_status(): void
    {
        $this->bot(['ok' => true, 'result' => []]);
        $this->branch('Markaz');
        $sadmin = $this->user(Role::SAdmin);
        $d = $this->user(Role::Director);
        $d->forceFill(['telegram_chat_id' => 9090, 'report_time' => '19:15', 'report_enabled' => true])->save();

        $this->actingAs($sadmin)->post(route('directors.telegram.test', $d->id))->assertSessionHas('success');
        Http::assertSent(fn ($r) => str_contains($r['text'] ?? '', 'Kunlik hisobot'));
        $this->actingAs($sadmin)->get(route('directors.index'))->assertSee('Soat 19:15');
        $this->actingAs($sadmin)->post(route('directors.telegram.unlink', $d->id));
        $this->assertNull($d->fresh()->telegram_chat_id);
    }
}
