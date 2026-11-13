<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\SmsMessage;
use App\Services\Sms\SmsDispatcher;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v14: USB modem birinchi, kunlik limit va xatoda Eskizga o'tish. */
class V14SmsGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['services.eskiz.email' => 'a@b.c', 'services.eskiz.password' => 'x', 'services.eskiz.from' => '4546', 'services.eskiz.base_url' => 'https://notify.eskiz.uz/api']);
    }

    private ?\App\Models\Branch $b = null;

    private function br(): \App\Models\Branch
    {
        return $this->b ??= $this->branch();
    }

    private function modem(array $over = []): void
    {
        foreach (['enabled' => true, 'base_url' => 'https://sms.example.test', 'path' => '/api/send', 'success_field' => 'success', 'success_value' => 'true'] as $k => $v) {
            AppSetting::put("sms.modem.$k", $over[$k] ?? $v);
        }
        $this->br()->forceFill(['sms_modem_daily_limit' => $over['daily_limit'] ?? 100])->save();
        AppSetting::put('sms.modem.auth_type', 'header');
        AppSetting::put('sms.modem.auth_header', 'X-API-Key');
        AppSetting::put('sms.modem.auth_value', 'sirli', true);
    }

    private function send(): SmsMessage
    {
        return app(SmsService::class)->queue($this->br(), '998901234567', 'Salom');
    }

    private function fakeEskiz(): array
    {
        return ['*/auth/login' => Http::response(['data' => ['token' => 't']]), '*/message/sms/send' => Http::response(['status' => 'waiting'])];
    }

    public function test_without_modem_eskiz_is_used(): void
    {
        Http::fake($this->fakeEskiz());

        $sms = $this->send()->fresh();

        $this->assertSame('eskiz', $sms->provider);
        $this->assertFalse($sms->fallback_used);
        $this->assertSame(SmsMessage::SENT, $sms->status);
    }

    public function test_modem_is_used_first_with_auth_header_and_counts_usage(): void
    {
        $this->modem();
        Http::fake(['sms.example.test/*' => Http::response(['success' => true])] + $this->fakeEskiz());

        $sms = $this->send()->fresh();

        $this->assertSame('modem', $sms->provider);
        $this->assertSame(SmsMessage::SENT, $sms->status);
        $this->assertSame(1, app(SmsDispatcher::class)->usedToday());
        Http::assertSent(fn ($r) => str_contains($r->url(), 'sms.example.test') && $r->hasHeader('X-API-Key', 'sirli') && $r['phone'] === '+998901234567' && $r['message'] === 'Salom');
        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'eskiz'));
    }

    public function test_over_daily_limit_overflows_to_eskiz(): void
    {
        $this->modem(['daily_limit' => 1]);
        Http::fake(['sms.example.test/*' => Http::response(['success' => true])] + $this->fakeEskiz());

        $first = $this->send()->fresh();
        $second = $this->send()->fresh();

        $this->assertSame('modem', $first->provider);
        $this->assertSame('eskiz', $second->provider);
        $this->assertTrue($second->fallback_used);
        $this->assertSame(1, app(SmsDispatcher::class)->usedToday());
    }

    public function test_modem_failure_falls_back_and_does_not_burn_quota(): void
    {
        $this->modem();
        Http::fake(['sms.example.test/*' => Http::response(['success' => false], 200)] + $this->fakeEskiz());

        $sms = $this->send()->fresh();

        $this->assertSame('eskiz', $sms->provider);
        $this->assertTrue($sms->fallback_used);
        $this->assertSame(0, app(SmsDispatcher::class)->usedToday());
    }

    public function test_circuit_breaker_skips_modem_after_repeated_failures(): void
    {
        $this->modem();
        Http::fake(['sms.example.test/*' => Http::response('xato', 500)] + $this->fakeEskiz());

        foreach (range(1, 3) as $_) {
            $this->send();
        }
        $this->assertTrue(app(SmsDispatcher::class)->breakerOpen());

        Http::fake($this->fakeEskiz());
        $sms = $this->send()->fresh();
        $this->assertSame('eskiz', $sms->provider);
        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'sms.example.test'));
    }

    public function test_gateway_settings_page_is_permission_gated_and_masks_secret(): void
    {
        $sadmin = $this->user(\App\Enums\Role::SAdmin);
        $admin = $this->user(\App\Enums\Role::Admin, $this->br(), ['sms.manage']);

        $this->actingAs($admin)->get(route('sms.gateway'))->assertForbidden();

        $this->actingAs($sadmin)->put(route('sms.gateway.update'), [
            'enabled' => 1, 'base_url' => 'https://sms.example.test', 'path' => '/api/send', 'method' => 'post', 'body_format' => 'json', 'auth_type' => 'bearer',
            'auth_value' => 'maxfiy-token', 'phone_field' => 'phone', 'message_field' => 'message', 'phone_format' => 'plus', 'timeout' => 15,
        ])->assertRedirect();

        $this->assertSame('maxfiy-token', AppSetting::get('sms.modem.auth_value'));
        $this->assertNotSame('maxfiy-token', \App\Models\AppSetting::where('key', 'sms.modem.auth_value')->value('value'));
        $this->actingAs($sadmin)->get(route('sms.gateway'))->assertOk()->assertDontSee('maxfiy-token');
    }

    public function test_branch_with_zero_limit_uses_eskiz_and_limits_are_per_branch(): void
    {
        $this->modem(['daily_limit' => 0]);
        $other = \App\Models\Branch::create(['name' => 'Boshqa', 'code' => 'BSH']);
        $other->forceFill(['sms_modem_daily_limit' => 5])->save();
        Http::fake(['sms.example.test/*' => Http::response(['success' => true])] + $this->fakeEskiz());

        $a = $this->send()->fresh();
        $b = app(SmsService::class)->queue($other, '998901234567', 'Salom')->fresh();

        $this->assertSame('eskiz', $a->provider);
        $this->assertSame('modem', $b->provider);
        $d = app(SmsDispatcher::class);
        $this->assertSame(0, $d->usedToday($this->br()));
        $this->assertSame(1, $d->usedToday($other));
    }

    public function test_monthly_counts_only_modem_and_limits_page_saves(): void
    {
        $this->modem(['daily_limit' => 10]);
        Http::fake(['sms.example.test/*' => Http::response(['success' => true])] + $this->fakeEskiz());
        $this->send();
        $this->send();
        $month = now('Asia/Tashkent')->format('Y-m');
        $this->assertSame(2, app(SmsDispatcher::class)->monthly(3)[$this->br()->id][$month]);

        $sadmin = $this->user(\App\Enums\Role::SAdmin);
        $this->actingAs($sadmin)->put(route('branches.sms', $this->br()), ['sms_modem_daily_limit' => 7])->assertRedirect();
        $this->assertSame(7, (int) $this->br()->fresh()->sms_modem_daily_limit);
        $this->actingAs($sadmin)->get(route('sms.gateway'))->assertOk();
    }
}
