<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Services\BrandService;
use App\Support\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * v20: Brend - tizim nomi va logotiplari (butun tizim uchun bitta). Faqat sAdmin (route `role:sadmin`).
 * Logotip o'lchami qat'iy: 512×512 (kvadrat) va 640×160 (gorizontal), faqat PNG.
 */
class BrandController extends Controller
{
    public function edit()
    {
        return view('brand.edit', [
            'defaults' => ['name' => Brand::DEFAULT_NAME, 'short' => Brand::DEFAULT_SHORT, 'tagline' => Brand::DEFAULT_TAGLINE],
            'gd' => function_exists('imagecreatefromstring'),
        ]);
    }

    public function update(Request $request, BrandService $brand): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:40'],
            'short' => ['required', 'string', 'min:2', 'max:12'],
            'tagline' => ['nullable', 'string', 'max:120'],
            'logo' => ['nullable', 'file'],
            'logo_wide' => ['nullable', 'file'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_logo_wide' => ['nullable', 'boolean'],
        ], [], ['name' => 'Loyiha nomi', 'short' => 'Qisqa nom', 'tagline' => 'Shior', 'logo' => 'Logotip', 'logo_wide' => 'Gorizontal logotip']);

        // Avval fayllarni tekshiramiz - biror xato bo'lsa hech narsa saqlanmaydi
        if ($request->hasFile('logo')) {
            $brand->inspect($request->file('logo'), Brand::LOGO_SIZE, 'logo');
        }
        if ($request->hasFile('logo_wide')) {
            $brand->inspect($request->file('logo_wide'), Brand::WIDE_SIZE, 'logo_wide');
        }

        $old = Brand::name();
        AppSetting::put('brand.name', trim($data['name']));
        AppSetting::put('brand.short', trim($data['short']));
        AppSetting::put('brand.tagline', trim((string) ($data['tagline'] ?? '')));

        $notes = [];
        if ($request->hasFile('logo')) {
            $brand->saveLogo($request->file('logo'));
            $notes[] = 'logotip yangilandi';
        } elseif ($request->boolean('remove_logo') && Brand::hasLogo()) {
            $brand->removeLogo();
            $notes[] = 'logotip olib tashlandi';
        }

        if ($request->hasFile('logo_wide')) {
            $brand->saveWide($request->file('logo_wide'));
            $notes[] = 'gorizontal logotip yangilandi';
        } elseif ($request->boolean('remove_logo_wide') && Brand::hasWide()) {
            $brand->removeWide();
            $notes[] = 'gorizontal logotip olib tashlandi';
        }

        AuditLog::record('brand.updated', null, "Brend yangilandi: «{$old}» → «".trim($data['name']).'»'.($notes ? ' ('.implode(', ', $notes).')' : ''));

        return redirect()->route('brand.edit')->with('success', 'Brend saqlandi. Ilovani telefonga o\'rnatgan foydalanuvchilarda ikonka qayta o\'rnatilgandan keyin yangilanadi.');
    }

    /** Brend fayllari (logotip, ikonkalar): kirish sahifasi ham ko'rsin deb ochiq; faqat ro'yxatdagi nomlar beriladi. */
    public function asset(string $file)
    {
        $allowed = ['logo.png', 'logo-wide.png', 'maskable-512.png', ...array_map(fn ($s) => "icon-{$s}.png", Brand::ICON_SIZES)];
        abort_unless(in_array($file, $allowed, true) && is_file(Brand::path($file)), 404);

        return response()->file(Brand::path($file), ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=31536000, immutable', 'X-Content-Type-Options' => 'nosniff']);
    }
}
