<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\LegalAcceptance;
use App\Models\LegalDocument;
use App\Services\LegalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v14: huquqiy hujjatlar, rozilik sahifasi (versiyalar bo'yicha), rozilik jurnali, cookie. */
class V14LegalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['legal.enforce' => true]);
    }

    public function test_documents_are_public_and_have_default_text(): void
    {
        foreach (array_keys(LegalService::TYPES) as $t) {
            $this->get(route('legal.show', $t))->assertOk()->assertSee(LegalService::TYPES[$t]);
        }
        $this->get('/legal/nonexistent')->assertNotFound();
        $this->assertSame(3, LegalDocument::count());
    }

    public function test_render_escapes_html_and_builds_headings_and_lists(): void
    {
        $html = LegalService::render("## Sarlavha <b>\n\n- bir\n- ikki\n\nOddiy <script>alert(1)</script>\nikkinchi qator");

        $this->assertStringContainsString('<h2>Sarlavha &lt;b&gt;</h2>', $html);
        $this->assertStringContainsString('<ul><li>bir</li><li>ikki</li></ul>', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('<br>', $html);
    }

    public function test_user_without_consent_is_redirected_until_accepting_all(): void
    {
        $user = $this->user(Role::Admin);

        $this->actingAs($user)->get('/')->assertRedirect(route('legal.consent'));
        $this->actingAs($user)->get(route('legal.consent'))->assertOk()->assertSee('Maxfiylik siyosati')->assertSee('Cookie siyosati');

        // hammasini belgilamasdan davom etib bo'lmaydi
        $this->actingAs($user)->post(route('legal.accept'), ['agree' => ['privacy']])->assertSessionHasErrors();
        $this->assertSame(0, LegalAcceptance::count());

        $this->actingAs($user)->post(route('legal.accept'), ['agree' => ['privacy', 'terms', 'cookies']])
            ->assertRedirect(route('dashboard'))->assertCookie('cookie_consent', 'all', false);

        $this->assertSame(3, LegalAcceptance::where('user_id', $user->id)->count());
        $row = LegalAcceptance::first();
        $this->assertSame($user->name, $row->user_name);
        $this->assertSame(1, $row->version);
        $this->assertNotNull($row->ip);

        $this->actingAs($user)->get('/')->assertOk();
    }

    public function test_new_version_requires_reacceptance_only_for_that_document(): void
    {
        $user = $this->user(Role::Admin);
        $this->actingAs($user)->post(route('legal.accept'), ['agree' => ['privacy', 'terms', 'cookies']]);
        $this->actingAs($user)->get('/')->assertOk();

        $doc = app(LegalService::class)->document('terms');
        $doc->update(['version' => 2]);
        app(LegalService::class)->forgetCache();

        $this->actingAs($user)->get('/')->assertRedirect(route('legal.consent'));
        $this->actingAs($user)->get(route('legal.consent'))->assertOk()->assertSee('Hujjatlar yangilandi')->assertSee('Foydalanish shartlari')->assertDontSee('value="privacy"', false);

        $this->actingAs($user)->post(route('legal.accept'), ['agree' => ['terms']])->assertRedirect();
        $this->assertTrue(LegalAcceptance::where('user_id', $user->id)->where('document_type', 'terms')->where('version', 2)->exists());
        $this->actingAs($user)->get('/')->assertOk();
    }

    public function test_students_and_json_requests_are_gated_too(): void
    {
        $student = $this->user(Role::Student);

        $this->actingAs($student)->get(route('student.home'))->assertRedirect(route('legal.consent'));
        $this->actingAs($student)->getJson('/statistics/data')->assertStatus(428);
        $this->actingAs($student)->post(route('logout'))->assertRedirect(route('login'));
    }

    public function test_enforcement_can_be_disabled(): void
    {
        config(['legal.enforce' => false]);

        $this->actingAs($this->user(Role::Admin))->get('/')->assertOk();
    }

    public function test_admin_edits_text_and_new_version_resets_consent(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $this->actingAs($sadmin)->post(route('legal.accept'), ['agree' => ['privacy', 'terms', 'cookies']]);

        $this->actingAs($sadmin)->get(route('legal.admin'))->assertOk()->assertSee('Rozilik jurnali');

        // matnni tahrirlash - versiya o'zgarmaydi
        $this->actingAs($sadmin)->put(route('legal.admin.update', 'privacy'), ['title' => 'Maxfiylik', 'body' => "## Yangi\n\nMatn"])->assertRedirect();
        $this->assertSame(1, LegalDocument::where('type', 'privacy')->value('version'));
        $this->actingAs($sadmin)->get('/')->assertOk();

        // yangi versiya - hamma qayta rozilik beradi
        $this->actingAs($sadmin)->put(route('legal.admin.update', 'privacy'), ['title' => 'Maxfiylik', 'body' => "## Yangi\n\nMatn 2", 'new_version' => 1])->assertRedirect();
        $this->assertSame(2, LegalDocument::where('type', 'privacy')->value('version'));
        $this->actingAs($sadmin)->get('/')->assertRedirect(route('legal.consent'));

        $this->get(route('legal.show', 'privacy'))->assertSee('Matn 2')->assertSee('Versiya 2');
    }

    public function test_only_permitted_users_manage_documents_and_view_journal(): void
    {
        $this->actingAs($this->user(Role::SAdmin))->post(route('legal.accept'), ['agree' => ['privacy', 'terms', 'cookies']]);
        $admin = $this->user(Role::Admin, permissions: ['audit.view']);
        $this->actingAs($admin)->post(route('legal.accept'), ['agree' => ['privacy', 'terms', 'cookies']]);

        $this->actingAs($admin)->get(route('legal.admin'))->assertForbidden();
        $this->actingAs($admin)->put(route('legal.admin.update', 'terms'), ['title' => 'x', 'body' => 'y'])->assertForbidden();
        $this->actingAs($admin)->get(route('legal.journal'))->assertForbidden();
    }

    public function test_journal_filters_and_csv_export(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $ali = $this->user(Role::Admin, attrs: ['name' => 'Ali Valiyev']);
        $vali = $this->user(Role::Manager, attrs: ['name' => 'Vali Aliyev']);
        $this->actingAs($sadmin)->post(route('legal.accept'), ['agree' => ['privacy', 'terms', 'cookies']]);
        $this->actingAs($ali)->post(route('legal.accept'), ['agree' => ['privacy', 'terms', 'cookies']]);
        $this->actingAs($vali)->post(route('legal.accept'), ['agree' => ['privacy', 'terms', 'cookies']]);

        $this->actingAs($sadmin)->get(route('legal.journal', ['q' => 'Ali Valiyev', 'type' => 'terms']))->assertOk()->assertSee('Ali Valiyev')->assertDontSee('Vali Aliyev');

        $csv = $this->actingAs($sadmin)->get(route('legal.journal.export'))->assertOk();
        $this->assertStringContainsString('text/csv', $csv->headers->get('Content-Type'));
        $content = $csv->streamedContent();
        $this->assertStringContainsString('Ali Valiyev', $content);
        $this->assertStringContainsString('Maxfiylik siyosati', $content);
        $this->assertSame(10, count(array_filter(explode("\n", trim($content)))));   // sarlavha + 3 foydalanuvchi x 3 hujjat
    }

    public function test_guest_pages_show_cookie_banner_and_document_links(): void
    {
        $this->get('/login')->assertOk()->assertSee('cookie-banner', false)->assertSee(route('legal.show', 'privacy'), false);
        $this->get('/forgot-password')->assertOk()->assertSee('cookie-banner', false);
    }

    public function test_forgot_password_shows_bot_username_with_telegram_link(): void
    {
        \App\Models\AppSetting::put('telegram.username', 'edunova_test_bot');

        $this->get('/forgot-password')->assertOk()
            ->assertSee('edunova_test_bot')
            ->assertSee('https://t.me/edunova_test_bot?start=parol', false)
            ->assertSee('Botni ochish');
    }
}
