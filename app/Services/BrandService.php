<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Support\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

/**
 * v20: brend fayllarini tekshiradi va saqlaydi. Kengaytma yoki brauzer yuborgan tur emas, HAQIQIY rasm o'lchami va turi
 * tekshiriladi; rasm GD orqali qayta kodlanadi (ichidagi metama'lumot/yashirin kod tozalanadi), ikonkalar avtomatik yasaladi.
 */
class BrandService
{
    /** @return array{0:int,1:int} haqiqiy [kenglik, balandlik] - qoidaga mos kelmasa ValidationException */
    public function inspect(UploadedFile $file, array $size, string $field): array
    {
        $label = $field === 'logo' ? 'Logotip' : 'Gorizontal logotip';
        [$w, $h] = $size;

        if ($file->getSize() > Brand::MAX_KB * 1024) {
            throw ValidationException::withMessages([$field => "{$label} hajmi ".Brand::MAX_KB.' KB dan oshmasligi kerak.']);
        }

        $info = @getimagesize($file->getRealPath());

        if (! $info || ($info[2] ?? null) !== IMAGETYPE_PNG) {
            throw ValidationException::withMessages([$field => "{$label} faqat PNG formatida bo'lishi kerak (shaffof fon tavsiya etiladi)."]);
        }

        if ((int) $info[0] !== $w || (int) $info[1] !== $h) {
            throw ValidationException::withMessages([$field => "{$label} aniq {$w}×{$h} px bo'lishi kerak. Siz yuklagan rasm: {$info[0]}×{$info[1]} px."]);
        }

        return [(int) $info[0], (int) $info[1]];
    }

    public function saveLogo(UploadedFile $file): void
    {
        $this->inspect($file, Brand::LOGO_SIZE, 'logo');
        $this->ensureDir();

        $src = $this->load($file, 'logo');
        $this->write($src, Brand::path('logo.png'));

        foreach (Brand::ICON_SIZES as $size) {
            $this->write($this->resize($src, $size, $size), Brand::path("icon-{$size}.png"));
        }

        $this->write($this->maskable($src), Brand::path('maskable-512.png'));
        imagedestroy($src);

        AppSetting::put('brand.logo_v', (string) time());
    }

    public function saveWide(UploadedFile $file): void
    {
        $this->inspect($file, Brand::WIDE_SIZE, 'logo_wide');
        $this->ensureDir();

        $src = $this->load($file, 'logo_wide');
        $this->write($src, Brand::path('logo-wide.png'));
        imagedestroy($src);

        AppSetting::put('brand.wide_v', (string) time());
    }

    /** Logotipni olib tashlaydi: tizim bosh harfli standart belgiga qaytadi. */
    public function removeLogo(): void
    {
        foreach (['logo.png', 'maskable-512.png', ...array_map(fn ($s) => "icon-{$s}.png", Brand::ICON_SIZES)] as $f) {
            @unlink(Brand::path($f));
        }

        AppSetting::forget('brand.logo_v');
    }

    public function removeWide(): void
    {
        @unlink(Brand::path('logo-wide.png'));
        AppSetting::forget('brand.wide_v');
    }

    private function ensureDir(): void
    {
        if (! function_exists('imagecreatefromstring')) {
            throw ValidationException::withMessages(['logo' => "Serverda rasm bilan ishlash (GD) kengaytmasi yoqilmagan - logotip yuklab bo'lmaydi."]);
        }

        if (! is_dir(Brand::path())) {
            mkdir(Brand::path(), 0775, true);
        }
    }

    /** @return \GdImage */
    private function load(UploadedFile $file, string $field)
    {
        $img = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $img) {
            throw ValidationException::withMessages([$field => "Rasm faylini o'qib bo'lmadi. Boshqa PNG fayl yuklang."]);
        }

        imagealphablending($img, false);
        imagesavealpha($img, true);

        return $img;
    }

    private function resize($src, int $w, int $h)
    {
        $dst = imagecreatetruecolor($w, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));

        return $dst;
    }

    /** Maskable ikonka: Android doira/yumaloq kesganda logotip kesilib ketmasligi uchun 70% hajmda, oq fonda. */
    private function maskable($src)
    {
        $dst = imagecreatetruecolor(512, 512);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagealphablending($dst, true);
        $inner = 358;
        imagecopyresampled($dst, $src, (512 - $inner) / 2, (512 - $inner) / 2, 0, 0, $inner, $inner, imagesx($src), imagesy($src));

        return $dst;
    }

    private function write($img, string $path): void
    {
        imagesavealpha($img, true);
        imagepng($img, $path, 6);
    }
}
