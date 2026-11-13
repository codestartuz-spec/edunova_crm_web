<?php

namespace Tests\Feature;

use App\Enums\PayMethod;
use App\Enums\Role;
use App\Enums\Wallet;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use App\Services\CashboxService;
use App\Services\AiLeadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class V6FeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-09-21 10:00:00');
    }

    // ---- Kassa tarixi ----

    public function test_cashbox_history_only_for_permitted_users(): void
    {
        $branch = $this->branch();
        $admin = $this->user(Role::Admin, $branch, ['cashbox.view', 'cashbox.request', 'cashbox.approve', 'cashbox.history']);
        $manager = $this->user(Role::Manager, $branch, ['cashbox.view', 'cashbox.request']);
        $this->fund($branch, Wallet::TillCash, 500000);
        $this->actingAs($admin);
        $req = app(CashboxService::class)->request('expense', PayMethod::Cash, 10000, 'Maxsus xarajat sababi', $admin);
        app(CashboxService::class)->approve($req, $admin);

        $this->actingAs($admin)->get('/cashbox')->assertOk()->assertSee("So'nggi 30 kun", false)->assertSee('Maxsus xarajat sababi');
        $this->actingAs($this->user(Role::SAdmin))->withSession(['current_branch_id' => $branch->id])->get('/cashbox')->assertOk()->assertSee("So'nggi 30 kun", false);
        $this->actingAs($manager)->get('/cashbox')->assertOk()->assertDontSee("So'nggi 30 kun", false)->assertDontSee('Maxsus xarajat sababi');
    }

    // ---- Chegirma qoidalari sozlamasi ----

    public function test_discount_rules_settings_page(): void
    {
        $branch = $this->branch();
        $admin = $this->user(Role::Admin, $branch, ['settings.branch']);

        $this->actingAs($admin)->get('/settings/discount-rules')->assertOk()->assertSee('Chegirma qoidalari');
        $this->put('/settings/discount-rules', ['discount_days_before' => 14, 'discount_days_after' => 5])->assertRedirect();
        $this->assertSame(14, $branch->fresh()->discount_days_before);
        $this->assertSame(5, $branch->fresh()->discount_days_after);

        $this->put('/settings/discount-rules', ['discount_days_before' => -1, 'discount_days_after' => 999])->assertSessionHasErrors(['discount_days_before', 'discount_days_after']);

        $manager = $this->user(Role::Manager, $branch, ['students.view']);
        $this->actingAs($manager)->get('/settings/discount-rules')->assertForbidden();
    }

    // ---- Varonka: havola, iframe, AI ----

    public function test_lead_form_link_follows_selected_branch_and_embed_works(): void
    {
        $a = $this->branch('Samarqand');
        $b = $this->branch('Buxoro');
        $sadmin = $this->user(Role::SAdmin);

        $this->actingAs($sadmin)->get('/leads')->assertOk()->assertSee('filialni tanlang');
        $this->actingAs($sadmin)->withSession(['current_branch_id' => $a->id])->get('/leads')->assertOk()
            ->assertSee("/apply/{$a->code}", false)->assertSee('iframe', false)->assertDontSee("/apply/{$b->code}", false);
        $this->actingAs($sadmin)->withSession(['current_branch_id' => $b->id])->get('/leads')->assertOk()->assertSee("/apply/{$b->code}", false);

        $manager = $this->user(Role::Manager, $a, ['leads.view']);
        $this->actingAs($manager)->get('/leads')->assertOk()->assertSee("/apply/{$a->code}", false);

        // Iframe uchun yengil sahifa (menyusiz) va yuborilgandan keyin ham embed rejimida qoladi
        $this->get("/apply/{$a->code}?embed=1")->assertOk()->assertDontSee('© ', false)->assertSee('name="embed"', false);
        $this->post("/apply/{$a->code}", ['name' => 'Ali', 'phone' => '+998 90 123 4567', 'embed' => 1])->assertRedirect("/apply/{$a->code}?embed=1");
    }

    public function test_ai_lead_analysis_is_saved_without_personal_data(): void
    {
        config(['services.openai.key' => 'sk-test', 'services.openai.daily_limit' => 50]);
        $branch = $this->branch();
        $manager = $this->user(Role::Manager, $branch, ['leads.view', 'leads.manage']);
        $json = json_encode(['priority' => 'yuqori', 'probability' => 72, 'summary' => 'Qiziqish yuqori.', 'next_step' => "Ertaga qo'ng'iroq qiling.", 'talking_points' => ['Narxni ayting', 'Sinov darsini taklif qiling'], 'risks' => []], JSON_UNESCAPED_UNICODE);
        Http::fake(['api.openai.com/*' => Http::response(['choices' => [['message' => ['role' => 'assistant', 'content' => $json]]]])]);

        // Yangi murojaat tushganda avtomatik tahlil (sync navbat)
        $this->post("/apply/{$branch->code}", ['name' => 'Sherzod Qodirov', 'phone' => '+998 90 123 4567', 'address' => 'Chilonzor, Sherzod uyi']);
        $lead = Lead::withoutGlobalScopes()->firstOrFail();
        $this->assertSame('yuqori', $lead->ai_analysis['priority']);
        $this->assertSame(72, $lead->ai_analysis['probability']);
        $this->assertNotNull($lead->ai_analyzed_at);
        $this->assertSame(1, LeadNote::where('lead_id', $lead->id)->where('type', 'ai')->count());

        // Izoh yozilganda qayta tahlil; izohdagi ism va raqam OpenAI'ga ketmaydi
        $this->actingAs($manager)->post("/leads/{$lead->id}/note", ['body' => "Sherzod bilan gaplashdim, +998 90 123 4567 raqami, ertaga keladi"]);
        $sent = json_encode(Http::recorded()->last()[0]->data(), JSON_UNESCAPED_UNICODE);
        $this->assertStringNotContainsString('Sherzod', $sent);
        $this->assertStringNotContainsString('123 4567', $sent);
        $this->assertStringNotContainsString('998901234567', $sent);
        $this->assertStringContainsString('[raqam]', $sent);
        $this->assertSame(2, LeadNote::where('lead_id', $lead->id)->where('type', 'ai')->count());

        $this->actingAs($manager)->get("/leads/{$lead->id}")->assertOk()->assertSee('AI fikri')->assertSee('72% qabul ehtimoli')->assertSee('Sinov darsini taklif qiling');
        $this->actingAs($manager)->get('/leads?status=in_progress')->assertOk()->assertSee('yuqori · 72%');
    }

    public function test_ai_lead_analysis_manual_button_errors_and_limits(): void
    {
        $branch = $this->branch();
        $manager = $this->user(Role::Manager, $branch, ['leads.view', 'leads.manage']);
        $lead = Lead::create(['branch_id' => $branch->id, 'name' => 'ALI', 'phone' => '+998 90 123 4567', 'status' => 'new']);

        // Kalit yo'q: avtomatik jimgina o'tkazib yuboriladi, qo'lda bosilsa xato ko'rsatiladi
        $this->assertNull(app(AiLeadService::class)->analyze($lead));
        $this->actingAs($manager)->post("/leads/{$lead->id}/analyze")->assertSessionHasErrors('ai');

        config(['services.openai.key' => 'sk-test', 'services.openai.daily_limit' => 1]);
        Http::fake(['api.openai.com/*' => Http::response(['choices' => [['message' => ['role' => 'assistant', 'content' => json_encode(['priority' => 'past', 'probability' => 20, 'summary' => 'Xulosa'])]]]])]);
        $this->actingAs($manager)->post("/leads/{$lead->id}/analyze")->assertSessionHas('success');
        $this->actingAs($manager)->post("/leads/{$lead->id}/analyze")->assertSessionHasErrors('ai');       // kunlik limit

        $viewer = $this->user(Role::Manager, $branch, ['leads.view']);
        $this->actingAs($viewer)->post("/leads/{$lead->id}/analyze")->assertForbidden();
    }

    // ---- Yordam (AI) va bilim bazasi ----
}
