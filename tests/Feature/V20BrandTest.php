<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Support\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** v20: Brend - nom, logotip (qat'iy o'lcham), ikonkalar. */
class V20BrandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD kengaytmasi yoqilmagan.');
        }
    }

    protected function tearDown(): void
    {
        foreach (glob(Brand::path('*.png')) ?: [] as $f) {
            @unlink($f);
        }
        parent::tearDown();
    }

    private function png(int $w, int $h, string $name = 'logo.png'): UploadedFile
    {
        $img = imagecreatetruecolor($w, $h);
        imagefill($img, 0, 0, imagecolorallocate($img, 200, 30, 60));
        $path = sys_get_temp_dir().'/'.uniqid('brand_').'.png';
        imagepng($img, $path);

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    private function data(array $over = []): array
    {
        return ['name' => 'Yangi Nom CRM', 'short' => 'YangiNom', 'tagline' => 'Yangi shior', ...$over];
    }

    public function test_only_sadmin_manages_brand(): void
    {
        $admin = $this->user(Role::Admin, $this->branch(), ['branches.manage']);
        $director = $this->user(Role::Director, null, ['branches.manage']);

        $this->actingAs($admin)->get('/brand')->assertForbidden();
        $this->actingAs($director)->post('/brand', $this->data())->assertForbidden();
    }

    public function test_defaults_are_edunova_and_name_can_be_changed(): void
    {
        $this->assertSame('NovaCrm', Brand::name());

        $sa = $this->user(Role::SAdmin);
        $this->actingAs($sa)->post('/brand', $this->data())->assertRedirect('/brand');

        $this->assertSame('Yangi Nom CRM', Brand::name());
        $this->assertSame('YangiNom', Brand::short());
        auth()->guard('web')->logout();
        $this->get('/login')->assertOk()->assertSee('Yangi shior')->assertSee('Yangi Nom CRM', false);
        $this->get('/manifest.webmanifest')->assertJsonPath('name', 'Yangi Nom CRM')->assertJsonPath('short_name', 'YangiNom');
    }

    public function test_logo_must_be_exactly_512_square(): void
    {
        $sa = $this->user(Role::SAdmin);

        $this->actingAs($sa)->post('/brand', $this->data() + ['logo' => $this->png(600, 400)])->assertSessionHasErrors('logo');
        $this->actingAs($sa)->post('/brand', $this->data() + ['logo' => $this->png(512, 511)])->assertSessionHasErrors('logo');
        $this->assertFalse(Brand::hasLogo());
        $this->assertSame('NovaCrm', Brand::name()); // xatoda hech narsa saqlanmaydi
    }

    public function test_logo_must_be_png(): void
    {
        $sa = $this->user(Role::SAdmin);
        $img = imagecreatetruecolor(512, 512);
        $path = sys_get_temp_dir().'/'.uniqid('brand_').'.jpg';
        imagejpeg($img, $path);
        $jpg = new UploadedFile($path, 'logo.png', 'image/png', null, true); // nomi PNG, ichi JPEG

        $this->actingAs($sa)->post('/brand', $this->data() + ['logo' => $jpg])->assertSessionHasErrors('logo');
        $this->assertFalse(Brand::hasLogo());
    }

    public function test_valid_logo_is_saved_and_icons_are_generated(): void
    {
        $sa = $this->user(Role::SAdmin);

        $this->actingAs($sa)->post('/brand', $this->data() + ['logo' => $this->png(512, 512), 'logo_wide' => $this->png(640, 160, 'wide.png')])->assertRedirect('/brand');

        $this->assertTrue(Brand::hasLogo());
        $this->assertTrue(Brand::hasWide());
        foreach (['logo.png', 'logo-wide.png', 'icon-32.png', 'icon-180.png', 'icon-192.png', 'icon-512.png', 'maskable-512.png'] as $f) {
            $this->assertFileExists(Brand::path($f));
        }
        $this->assertSame([192, 192], array_slice(getimagesize(Brand::path('icon-192.png')), 0, 2));

        $this->get(Brand::logoUrl())->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->get('/manifest.webmanifest')->assertJsonPath('icons.0.src', Brand::iconUrl(192));
        $this->assertStringContainsString('icon-192.png', Brand::iconUrl(192));
    }

    public function test_wide_logo_must_be_exactly_640_by_160(): void
    {
        $sa = $this->user(Role::SAdmin);

        $this->actingAs($sa)->post('/brand', $this->data() + ['logo_wide' => $this->png(512, 512)])->assertSessionHasErrors('logo_wide');
        $this->assertFalse(Brand::hasWide());
    }

    public function test_logo_can_be_removed(): void
    {
        $sa = $this->user(Role::SAdmin);
        $this->actingAs($sa)->post('/brand', $this->data() + ['logo' => $this->png(512, 512)]);
        $this->assertTrue(Brand::hasLogo());

        $this->actingAs($sa)->post('/brand', $this->data() + ['remove_logo' => 1])->assertRedirect('/brand');

        $this->assertFalse(Brand::hasLogo());
        $this->assertFileDoesNotExist(Brand::path('logo.png'));
        $this->assertStringContainsString('icons/icon-192.png', Brand::iconUrl(192));
    }

    public function test_asset_route_serves_only_known_files(): void
    {
        $this->get('/brand/passwd.png')->assertNotFound();
        $this->get('/brand/logo.png')->assertNotFound(); // yuklanmagan
    }

    public function test_name_length_is_validated(): void
    {
        $sa = $this->user(Role::SAdmin);

        $this->actingAs($sa)->post('/brand', $this->data(['name' => 'A']))->assertSessionHasErrors('name');
        $this->actingAs($sa)->post('/brand', $this->data(['short' => str_repeat('x', 13)]))->assertSessionHasErrors('short');
        $this->assertNull(AppSetting::get('brand.name'));
    }
}
