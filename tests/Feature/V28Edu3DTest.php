<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Support\BrandTheme;
use App\Support\Edu3D;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v28: 3D dizayn tizimi - sahifa turiga qarab daraja/sahna, filial rangidan urg'u va grafik ranglari, animatsion SVG fon. */
class V28Edu3DTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_maps_to_scene_and_mode(): void
    {
        $this->assertSame(['scene' => 'path', 'mode' => 'rich'], Edu3D::forRoute('dashboard'));
        $this->assertSame(['scene' => 'math', 'mode' => 'calm'], Edu3D::forRoute('payments.index'));
        $this->assertSame(['scene' => 'supplies', 'mode' => 'form'], Edu3D::forRoute('students.create'));
        $this->assertSame(['scene' => 'orbs', 'mode' => 'calm'], Edu3D::forRoute('leads.index'));
        $this->assertSame(['scene' => 'supplies', 'mode' => 'rich'], Edu3D::forRoute('student.home'));
        $this->assertSame(['scene' => 'supplies', 'mode' => 'calm'], Edu3D::forRoute(null));
        foreach (['dashboard', 'students.index', 'finance.index', 'sms.index', 'settings.index', 'nomalum.marshrut'] as $n) {
            $r = Edu3D::forRoute($n);
            $this->assertContains($r['scene'], Edu3D::SCENES);
            $this->assertContains($r['mode'], Edu3D::MODES);
        }
    }

    public function test_branch_color_derives_harmonious_accent_and_chart_colors(): void
    {
        $b = $this->branch('Rangli');
        $b->update(['brand_color' => '#0d9488']);
        $admin = $this->user(Role::SAdmin);
        $html = $this->actingAs($admin)->withSession(['current_branch_id' => $b->id])->get(route('dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('--accent-400:', $html);
        $this->assertStringContainsString('--viz-s1:rgb(', $html);
        $this->assertStringContainsString('--brand-hex:#0d9488', $html);

        [$a4, $a5] = BrandTheme::accent('#0d9488');
        $this->assertCount(3, $a4);
        $this->assertNotSame($a4, $a5);
        foreach ([...$a4, ...$a5] as $v) {
            $this->assertTrue($v >= 0 && $v <= 255);
        }
        // Qizil brend uchun ham to'g'ri qiymatlar (rang doirasidan o'tish joyi)
        foreach (BrandTheme::accent('#e11d48')[0] as $v) {
            $this->assertTrue($v >= 0 && $v <= 255);
        }
    }

    public function test_default_color_leaves_default_palette(): void
    {
        $b = $this->branch('Oddiy');
        $html = $this->actingAs($this->user(Role::SAdmin))->withSession(['current_branch_id' => $b->id])->get(route('dashboard'))->assertOk()->getContent();
        $this->assertStringNotContainsString('id="brand-theme"', $html);
    }

    public function test_pages_render_animated_svg_background_with_correct_scene(): void
    {
        $b = $this->branch('Fon');
        $admin = $this->user(Role::SAdmin);
        $s = $this->actingAs($admin)->withSession(['current_branch_id' => $b->id]);

        foreach ([['dashboard', 'path', 'rich'], ['payments.index', 'math', 'calm'], ['students.create', 'supplies', 'form'], ['leads.index', 'orbs', 'calm']] as [$route, $scene, $mode]) {
            $html = $s->get(route($route))->assertOk()->getContent();
            $this->assertStringContainsString('class="edu-bg" data-scene="'.$scene.'"', $html, $route);
            $this->assertStringContainsString('data-fx-mode="'.$mode.'"', $html, $route);
            $this->assertStringContainsString("localStorage.getItem('edu-fx')", $html, $route);
            $this->assertStringContainsString('class="edu-layer', $html, $route);
            $this->assertStringContainsString('eb-book', $html, $route);
        }
    }

    public function test_login_and_student_cabinet_have_background(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('edu-bg', false)->assertSee('data-fx-mode="rich"', false);

        $b = $this->branch('Kabinet');
        $this->actingAs($this->student($b))->get(route('student.home'))->assertOk()->assertSee('edu-bg', false)->assertSee('data-scene="supplies"', false);
    }

    public function test_profile_has_animation_level_setting(): void
    {
        $this->actingAs($this->user(Role::SAdmin))->get(route('profile.edit'))->assertOk()
            ->assertSee('Animatsiya va 3D effektlar')->assertSee('Yengil')->assertSee("O'chiq", false);
    }
}
