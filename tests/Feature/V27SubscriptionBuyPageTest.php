<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\SubscriptionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v27: obuna uzaytirish sahifasi (3D hero, tarif kartalari, bank kartasi, chek zonasi). */
class V27SubscriptionBuyPageTest extends TestCase
{
    use RefreshDatabase;

    private function configure(string $card = '8888888888888888'): void
    {
        AppSetting::put('subscription.price_monthly', 249000);
        AppSetting::put('subscription.price_yearly', 2490000);
        AppSetting::put('subscription.card_number', $card);
        AppSetting::put('subscription.card_holder', 'ELSHOD T.');
    }

    public function test_page_renders_3d_scene_plans_card_and_dropzone(): void
    {
        $this->configure();
        $b = $this->branch();
        $html = $this->actingAs($this->user(Role::Director))->get(route('subscriptions.buy', $b))->assertOk()->getContent();

        foreach (['bx-hero', 'bx-coin', 'bx-cube', 'bx-plan', 'bx-cc', 'bx-drop', 'bxBuy', 'data-bx-count'] as $needle) {
            $this->assertStringContainsString($needle, $html, $needle);
        }
        $this->assertStringContainsString('8888 8888 8888 8888', $html);          // 16 xonali karta 4 tadan guruhlanadi
        $this->assertStringContainsString('ELSHOD T.', $html);
        $this->assertStringContainsString('name="receipt"', $html);               // forma maydonlari saqlangan
        $this->assertStringContainsString('name="plan" value="yearly"', $html);
        $this->assertStringContainsString('(−17%)', $html);
    }

    public function test_non_standard_card_is_shown_as_is(): void
    {
        $this->configure('8600 1234 5678 9012 / Uzcard');
        $html = $this->actingAs($this->user(Role::Director))->get(route('subscriptions.buy', $this->branch()))->assertOk()->getContent();
        $this->assertStringContainsString('8600 1234 5678 9012 / Uzcard', $html);
    }

    public function test_expired_branch_shows_expired_hero(): void
    {
        $this->configure();
        $b = $this->branch();
        $b->update(['subscription_ends_on' => today()->subDays(2)->toDateString()]);
        $this->actingAs($this->user(Role::Director))->get(route('subscriptions.buy', $b))
            ->assertOk()->assertSee('Obuna sotib olish')->assertSee('Muddati tugagan')->assertSee('Tasdiqlangan kundan hisoblanadi');
    }

    public function test_pending_request_hides_form_but_keeps_hero(): void
    {
        $this->configure();
        $b = $this->branch();
        $director = $this->user(Role::Director);
        SubscriptionRequest::create(['branch_id' => $b->id, 'user_id' => $director->id, 'plan' => 'monthly', 'amount' => 249000, 'receipt_path' => 'x.jpg', 'status' => SubscriptionRequest::PENDING]);
        $this->actingAs($director)->get(route('subscriptions.buy', $b))
            ->assertOk()->assertSee('bx-hero', false)->assertSee('allaqachon yuborilgan')->assertDontSee('name="receipt"', false);
    }
}
