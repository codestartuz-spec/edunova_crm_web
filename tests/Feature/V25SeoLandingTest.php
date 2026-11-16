<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v25: ochiq landing, robots.txt, sitemap.xml, noindex himoyasi. */
class V25SeoLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_indexable_landing_on_root(): void
    {
        $r = $this->get('/')->assertOk()
            ->assertSee('<title>', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee('hreflang="ru"', false)
            ->assertSee('hreflang="x-default"', false)
            ->assertSee('og:image', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('FAQPage', false)
            ->assertSee('<h1', false);
        $this->assertStringContainsString('index, follow', $r->getContent());
        $this->assertNull($r->headers->get('X-Robots-Tag'));
        $this->assertSame('uz', $r->headers->get('Content-Language'));
    }

    public function test_russian_landing_and_uz_redirect(): void
    {
        $this->get('/ru')->assertOk()->assertSee('lang="ru"', false)->assertSee('CRM-система', false);
        $this->assertNull($this->get('/ru')->headers->get('X-Robots-Tag'));
        $this->get('/uz')->assertStatus(301)->assertRedirect('/');
    }

    public function test_json_ld_is_valid_json(): void
    {
        $html = $this->get('/')->getContent();
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $m);
        $data = json_decode($m[1], true);
        $this->assertNotNull($data);
        $types = array_column($data['@graph'], '@type');
        $this->assertEqualsCanonicalizing(['Organization', 'WebSite', 'SoftwareApplication', 'FAQPage'], $types);
    }

    public function test_robots_and_sitemap(): void
    {
        $robots = $this->get('/robots.txt')->assertOk()->getContent();
        $this->assertStringContainsString('Disallow: /', $robots);
        $this->assertStringContainsString('Allow: /$', $robots);
        $this->assertStringContainsString('Sitemap: ', $robots);
        $this->assertNull($this->get('/robots.txt')->headers->get('X-Robots-Tag'));

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('<loc>', $xml);
        $this->assertStringContainsString('hreflang="ru"', $xml);
        $this->assertNotFalse(simplexml_load_string($xml));
        $this->assertStringNotContainsString('/login', $xml);
    }

    public function test_private_pages_are_noindex(): void
    {
        $this->get('/login')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/legal/terms')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/students')->assertRedirect(route('login'))->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_logged_in_user_still_gets_dashboard_on_root(): void
    {
        $this->actingAs($this->user(Role::SAdmin))->get('/')->assertOk()->assertDontSee('application/ld+json', false);
    }

    public function test_contact_buttons_only_when_configured(): void
    {
        $this->get('/')->assertDontSee('t.me/', false);
        AppSetting::put('landing.telegram', '@edunova_uz');
        AppSetting::put('landing.phone', '+998 90 123 45 67');
        $this->get('/')->assertSee('https://t.me/edunova_uz', false)->assertSee('tel:+998901234567', false);
    }

    public function test_manifest_starts_at_login(): void
    {
        $this->get('/manifest.webmanifest')->assertOk()->assertJsonPath('start_url', url('/login'));
    }

    public function test_sadmin_saves_landing_contacts_with_validation(): void
    {
        $this->actingAs($this->user(Role::SAdmin));
        $base = ['name' => 'NovaCrm', 'short' => 'NovaCrm'];
        $this->post(route('brand.update'), $base + ['landing_telegram' => 'bad url!!'])->assertSessionHasErrors('landing_telegram');
        $this->post(route('brand.update'), $base + ['landing_telegram' => '@edunova_uz', 'landing_email' => 'info@edunova.uz'])->assertSessionHasNoErrors();
        $this->assertSame('@edunova_uz', AppSetting::get('landing.telegram'));
        $this->get('/ru')->assertSee('https://t.me/edunova_uz', false)->assertSee('mailto:info@edunova.uz', false);
    }

    // ---- Demo so'rov ----

    private function demoPayload(array $over = []): array
    {
        return $over + [
            'name' => 'Aziz Karimov', 'phone' => '+998 90 123 45 67', 'center' => 'Test markaz', 'size' => '101-300', 'message' => 'Salom',
            'locale' => 'uz', 't' => \Illuminate\Support\Facades\Crypt::encryptString((string) (time() - 10)),
        ];
    }

    public function test_demo_request_is_saved_and_admins_get_telegram(): void
    {
        \App\Models\AppSetting::put('telegram.token', '123:abc', true);
        app(\App\Services\Telegram\TelegramService::class)->addAdmin(111, 'Elshod');
        app(\App\Services\Telegram\TelegramService::class)->addAdmin(222, 'Boshqa');
        \Illuminate\Support\Facades\Http::fake(['api.telegram.org/*' => \Illuminate\Support\Facades\Http::response(['ok' => true, 'result' => []])]);

        $this->postJson(route('demo.store'), $this->demoPayload())->assertOk()->assertJson(['ok' => true]);

        $d = \App\Models\DemoRequest::firstOrFail();
        $this->assertSame('998901234567', $d->phone);
        $this->assertSame('new', $d->status);
        $this->assertNotSame('127.0.0.1', $d->ip_hash);
        \Illuminate\Support\Facades\Http::assertSentCount(2);
        \Illuminate\Support\Facades\Http::assertSent(fn ($r) => str_contains($r->url(), 'sendMessage') && $r['chat_id'] === 111 && str_contains($r['text'], 'Aziz Karimov') && str_contains($r['text'], '+998901234567'));
        $this->assertTrue($d->fresh()->telegram_sent);
    }

    public function test_demo_request_validation_and_spam_protection(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        $this->postJson(route('demo.store'), $this->demoPayload(['phone' => '123']))->assertStatus(422)->assertJsonValidationErrors('phone');
        $this->postJson(route('demo.store'), $this->demoPayload(['name' => '']))->assertStatus(422)->assertJsonValidationErrors('name');
        $this->postJson(route('demo.store'), $this->demoPayload(['size' => 'xxx']))->assertStatus(422)->assertJsonValidationErrors('size');

        // robot: yashirin maydon to'ldirilgan, yoki forma juda tez yuborilgan -> "muvaffaqiyat", lekin saqlanmaydi
        $this->postJson(route('demo.store'), $this->demoPayload(['website' => 'http://spam']))->assertOk();
        $this->postJson(route('demo.store'), $this->demoPayload(['t' => \Illuminate\Support\Facades\Crypt::encryptString((string) time())]))->assertOk();
        $this->postJson(route('demo.store'), $this->demoPayload(['t' => 'buzilgan']))->assertOk();
        $this->assertSame(0, \App\Models\DemoRequest::count());

        // takroriy so'rov (bir xil raqam, 30 daqiqa) ikkinchi yozuv ochmaydi
        $this->postJson(route('demo.store'), $this->demoPayload())->assertOk();
        $this->postJson(route('demo.store'), $this->demoPayload(['name' => 'Boshqa']))->assertOk();
        $this->assertSame(1, \App\Models\DemoRequest::count());
    }

    public function test_demo_request_is_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('demo.store'), $this->demoPayload(['phone' => '+998 90 123 45 '.sprintf('%02d', $i)]))->assertOk();
        }
        $this->postJson(route('demo.store'), $this->demoPayload(['phone' => '+998 91 000 00 00']))->assertStatus(429);
    }

    public function test_demo_requests_page_is_sadmin_only(): void
    {
        \App\Models\DemoRequest::create(['name' => 'Aziz', 'phone' => '998901234567', 'locale' => 'uz']);
        $this->get(route('demo.index'))->assertRedirect(route('login'));
        foreach ([Role::Director, Role::Admin, Role::Manager] as $role) {
            $this->actingAs($this->user($role, $this->branch()))->get(route('demo.index'))->assertForbidden();
        }
        $this->actingAs($this->user(Role::SAdmin));
        $this->get(route('demo.index'))->assertOk()->assertSee('Aziz')->assertSee('+998901234567');
        $d = \App\Models\DemoRequest::first();
        $this->put(route('demo.update', $d), ['status' => 'contacted', 'note' => 'ertaga'])->assertSessionHasNoErrors();
        $this->assertSame('contacted', $d->fresh()->status);
        $this->delete(route('demo.destroy', $d))->assertSessionHasNoErrors();
        $this->assertSame(0, \App\Models\DemoRequest::count());
    }
}
