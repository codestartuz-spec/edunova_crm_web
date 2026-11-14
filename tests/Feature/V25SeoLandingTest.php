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
        $base = ['name' => 'EduNova CRM', 'short' => 'EduNova'];
        $this->post(route('brand.update'), $base + ['landing_telegram' => 'bad url!!'])->assertSessionHasErrors('landing_telegram');
        $this->post(route('brand.update'), $base + ['landing_telegram' => '@edunova_uz', 'landing_email' => 'info@edunova.uz'])->assertSessionHasNoErrors();
        $this->assertSame('@edunova_uz', AppSetting::get('landing.telegram'));
        $this->get('/ru')->assertSee('https://t.me/edunova_uz', false)->assertSee('mailto:info@edunova.uz', false);
    }
}
