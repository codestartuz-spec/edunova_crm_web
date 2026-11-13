<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Support\BrandTheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v19: filial rangiga moslashadigan panel (brand palitrasi CSS o'zgaruvchilari orqali). */
class V19BrandThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_palette_has_all_steps_and_light_colors_are_darkened_for_contrast(): void
    {
        $p = BrandTheme::palette('#ffff00'); // sariq: oq matn bilan o'qilmaydi

        $this->assertSame([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950], array_keys($p));
        // 600 qoraytirilgan: sof sariqdan (255,255,0) ancha to'q
        $this->assertLessThan(200, $p[600][0]);
        $this->assertGreaterThan($p[600][0], $p[50][0]);
    }

    public function test_invalid_color_is_ignored(): void
    {
        $this->assertNull(BrandTheme::valid('qizil'));
        $this->assertNull(BrandTheme::valid('#12345'));
        $this->assertSame('#1a2b3c', BrandTheme::valid('#1A2B3C'));
    }

    public function test_panel_uses_branch_color_and_default_without_it(): void
    {
        $branch = $this->branch();
        $branch->update(['brand_color' => '#0d9488']);
        $admin = $this->user(Role::Admin, $branch);

        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertSee('--brand-600:', false)->assertSee('--brand-hex:#0d9488', false);

        $plain = $this->branch('Rangsiz');
        $other = $this->user(Role::Admin, $plain);
        $this->actingAs($other)->get(route('dashboard'))->assertOk()->assertDontSee('--brand-hex:', false);
    }
}
