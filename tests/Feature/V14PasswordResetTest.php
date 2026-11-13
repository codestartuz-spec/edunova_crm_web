<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\PasswordResetLog;
use App\Services\Telegram\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v14: telefon + Telegram kodi orqali parolni tiklash. */
class V14PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        AppSetting::put('telegram.token', '123456:'.str_repeat('A', 35), true);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => []])]);
    }

    private function sentCodeTo(int $chat): ?string
    {
        foreach (Http::recorded() as [$req]) {
            /** @var HttpRequest $req */
            if (str_contains($req->url(), 'sendMessage') && (int) $req['chat_id'] === $chat && preg_match('/<b>(\d{6})<\/b>/', $req['text'], $m)) {
                return $m[1];
            }
        }

        return null;
    }

    public function test_full_flow_with_telegram(): void
    {
        $student = $this->student($this->branch(), ['phone' => '+998 90 123 4567', 'telegram_chat_id' => 555]);

        $this->post('/forgot-password', ['phone' => '90 123 45 67'])->assertRedirect(route('password.code'));
        $code = $this->sentCodeTo(555);
        $this->assertNotNull($code);

        $this->post('/forgot-password/code', ['code' => $code])->assertRedirect(route('password.new'));
        $this->post('/forgot-password/new', ['password' => 'yangi-parol-123', 'password_confirmation' => 'yangi-parol-123'])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('yangi-parol-123', $student->fresh()->password));
        $this->assertTrue(PasswordResetLog::where('event', 'reset_done')->where('user_id', $student->id)->exists());
        $this->assertTrue(PasswordResetLog::where('event', 'code_sent')->where('channel', 'telegram')->exists());

        // Kod bir martalik
        $this->post('/forgot-password/code', ['code' => $code])->assertNotFound();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(\App\Services\PasswordResetService::class)->verify('+998901234567', $code);
    }

    public function test_wrong_code_is_rejected_and_logged(): void
    {
        $this->student($this->branch(), ['phone' => '+998 90 123 4567', 'telegram_chat_id' => 555]);

        $this->post('/forgot-password', ['phone' => '998901234567']);
        $this->post('/forgot-password/code', ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertTrue(PasswordResetLog::where('event', 'code_wrong')->exists());
    }

    public function test_unknown_phone_gets_same_response_and_is_logged(): void
    {
        $this->post('/forgot-password', ['phone' => '998909999999'])->assertRedirect(route('password.code'))->assertSessionHas('success');
        $this->assertTrue(PasswordResetLog::where('event', 'requested')->where('note', 'Akkaunt topilmadi')->exists());
    }

    public function test_many_accounts_require_choice(): void
    {
        $branch = $this->branch();
        $a = $this->student($branch, ['phone' => '+998 90 123 4567', 'telegram_chat_id' => 555]);
        $b = $this->student($branch, ['phone2' => '+998 90 123 4567', 'telegram_chat_id' => 555]);

        $this->post('/forgot-password', ['phone' => '998901234567']);
        $this->post('/forgot-password/code', ['code' => $this->sentCodeTo(555)])->assertRedirect(route('password.choose'));
        $this->get('/forgot-password/choose')->assertOk()->assertSee($a->name)->assertSee($b->name);
        $this->post('/forgot-password/choose', ['user_id' => $b->id])->assertRedirect(route('password.new'));
        $this->post('/forgot-password/new', ['password' => 'yangi-parol-123', 'password_confirmation' => 'yangi-parol-123']);

        $this->assertTrue(Hash::check('yangi-parol-123', $b->fresh()->password));
        $this->assertFalse(Hash::check('yangi-parol-123', $a->fresh()->password));
    }

    public function test_sadmin_code_goes_only_to_admin_ids(): void
    {
        $sadmin = $this->user(Role::SAdmin, attrs: ['phone' => '+998 90 111 2233', 'telegram_chat_id' => 777]);
        app(TelegramService::class)->addAdmin(999, 'Elshod');

        $this->post('/forgot-password', ['phone' => '998901112233']);

        $this->assertNotNull($this->sentCodeTo(999));
        $this->assertNull($this->sentCodeTo(777));
    }

    public function test_requests_are_throttled_per_phone(): void
    {
        $this->student($this->branch(), ['phone' => '+998 90 123 4567', 'telegram_chat_id' => 555]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/forgot-password', ['phone' => '998901234567'])->assertRedirect(route('password.code'));
        }
        $this->post('/forgot-password', ['phone' => '998901234567'])->assertSessionHasErrors('phone');
        $this->assertTrue(PasswordResetLog::where('event', 'blocked')->exists());
    }
}
