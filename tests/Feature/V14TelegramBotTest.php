<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Services\Telegram\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v14: Telegram webhook — maxfiy sarlavha, kontakt bog'lash (user_id tekshiruvi), faqat Admin ID buyruqlari. */
class V14TelegramBotTest extends TestCase
{
    use RefreshDatabase;

    private string $secret;

    protected function setUp(): void
    {
        parent::setUp();
        AppSetting::put('telegram.token', '123456:'.str_repeat('A', 35), true);
        $this->secret = app(TelegramService::class)->secret();
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => []])]);
    }

    private function hook(array $message, ?string $secret = null)
    {
        return $this->postJson('/telegram/webhook', ['message' => $message + ['chat' => ['id' => 42, 'type' => 'private']]], ['X-Telegram-Bot-Api-Secret-Token' => $secret ?? $this->secret]);
    }

    public function test_requires_secret_header(): void
    {
        $this->postJson('/telegram/webhook', ['message' => []])->assertForbidden();
        $this->hook(['text' => '/start'], 'wrong')->assertForbidden();
        $this->hook(['text' => '/start', 'from' => ['id' => 42]])->assertOk();
    }

    public function test_contact_of_the_sender_links_all_matching_accounts(): void
    {
        $branch = $this->branch();
        $a = $this->student($branch, ['phone' => '+998 90 123 4567']);
        $b = $this->student($branch, ['phone2' => '+998 90 123 4567']);
        $other = $this->student($branch, ['phone' => '+998 91 000 0000']);

        $this->hook(['from' => ['id' => 42, 'username' => 'ali'], 'contact' => ['phone_number' => '998901234567', 'user_id' => 42]])->assertOk();

        $this->assertSame(42, (int) $a->fresh()->telegram_chat_id);
        $this->assertSame(42, (int) $b->fresh()->telegram_chat_id);
        $this->assertNull($other->fresh()->telegram_chat_id);
    }

    public function test_foreign_contact_is_not_linked(): void
    {
        $a = $this->student($this->branch(), ['phone' => '+998 90 123 4567']);

        $this->hook(['from' => ['id' => 42], 'contact' => ['phone_number' => '998901234567', 'user_id' => 99]])->assertOk();

        $this->assertNull($a->fresh()->telegram_chat_id);
    }

    public function test_admin_commands_only_for_admin_ids(): void
    {
        $this->hook(['from' => ['id' => 42], 'text' => '/backup'])->assertOk();

        $sent = collect(Http::recorded())->map(fn ($p) => $p[0]['text'] ?? '')->implode('|');
        $this->assertStringContainsString('faqat administratorlar', $sent);
        $this->assertStringNotContainsString('Zaxira olinmoqda', $sent);
    }

    public function test_unlink_command(): void
    {
        $a = $this->student($this->branch(), ['phone' => '+998 90 123 4567', 'telegram_chat_id' => 42]);

        $this->hook(['from' => ['id' => 42], 'text' => '/unlink'])->assertOk();

        $this->assertNull($a->fresh()->telegram_chat_id);
    }
}
