<?php

namespace App\Support;

/** v14: Sozlamalar bo'limlarining yagona ro'yxati (yon menyu va bosh sahifa kartalari uchun). */
class SettingsMenu
{
    /** @return array<int, array{title:string,icon:string,items:array<int,array{label:string,desc:string,url:string,active:bool}>}> */
    public static function groups(): array
    {
        $groups = self::allGroups();
        $me = auth()->user();

        // Faqat kurslarni ko'ra oladigan (sozlamalar ruxsati yo'q) xodimga faqat «Kurslar» ko'rsatiladi
        if ($me && ! $me->can('settings.branch')) {
            $courses = collect($groups)->flatMap(fn ($g) => $g['items'])->firstWhere('label', 'Kurslar');

            return $courses ? [['title' => "Ta'lim", 'icon' => 'book', 'items' => [$courses]]] : [];
        }

        return $groups;
    }

    private static function allGroups(): array
    {
        $is = fn (string ...$p) => request()->is(...$p);
        $me = auth()->user();

        return array_values(array_filter([
            ['title' => "Ta'lim", 'icon' => 'book', 'items' => array_values(array_filter([
                $me?->canany(['courses.view', 'courses.manage']) ? ['label' => 'Kurslar', 'desc' => "Kurslar, video, audio va test savollari", 'url' => route('catalog.index', 'courses'), 'active' => $is('settings/courses*')] : null,
                ['label' => 'Xonalar', 'desc' => "Dars o'tiladigan xonalar", 'url' => route('catalog.index', 'rooms'), 'active' => $is('settings/rooms*')],
                ['label' => 'Dars vaqtlari', 'desc' => 'Dars boshlanish va tugash vaqtlari', 'url' => route('catalog.index', 'lesson-times'), 'active' => $is('settings/lesson-times*')],
                ['label' => 'Narx rejalari', 'desc' => "Oylik to'lov va erta to'lov chegirmasi", 'url' => route('catalog.index', 'price-plans'), 'active' => $is('settings/price-plans*')],
                ['label' => 'Kitoblar', 'desc' => "O'quv kitoblari ro'yxati", 'url' => route('catalog.index', 'books'), 'active' => $is('settings/books*')],
            ]))],
            ['title' => 'Moliya', 'icon' => 'banknote', 'items' => [
                ['label' => 'Chegirma qoidalari', 'desc' => "Avtomatik chegirmalar", 'url' => route('discount-rules.edit'), 'active' => $is('settings/discount-rules*')],
                ['label' => 'Aksiyalar', 'desc' => 'Vaqtinchalik chegirma aksiyalari', 'url' => route('catalog.index', 'campaigns'), 'active' => $is('settings/campaigns*')],
                ['label' => 'Xarajat turlari', 'desc' => 'Kassa xarajatlari kategoriyalari', 'url' => route('catalog.index', 'expense-categories'), 'active' => $is('settings/expense-categories*')],
                ['label' => 'Usluga', 'desc' => "O'qituvchi staj ustamasi (8 pog'ona)", 'url' => route('usluga-settings.edit'), 'active' => $is('settings/usluga*')],
            ]],
            ['title' => 'Hujjatlar', 'icon' => 'file', 'items' => [
                ['label' => 'Shartnoma', 'desc' => 'Shartnoma shabloni va rekvizitlar', 'url' => route('contract-settings.edit'), 'active' => $is('settings/contract*')],
            ]],
            ['title' => 'Marketing', 'icon' => 'megaphone', 'items' => [
                ['label' => "O'quvchi manbalari", 'desc' => "O'quvchi qayerdan kelganini aniqlash", 'url' => route('catalog.index', 'lead-sources'), 'active' => $is('settings/lead-sources*')],
            ]],
            $me?->isSuperAdmin() ? ['title' => 'Yordam', 'icon' => 'play', 'items' => [
                ['label' => 'Video darslar', 'desc' => "Tizim haqidagi video qo'llanmalar (faqat sAdmin boshqaradi)", 'url' => route('tutorials.admin'), 'active' => $is('settings/tutorials*')],
                ['label' => "Demo so'rovlar", 'desc' => "Sotuv sahifasidan kelgan demo so'rovlar (faqat sAdmin)", 'url' => route('demo.index'), 'active' => $is('settings/demo-requests*')],
            ]] : null,
            ['title' => 'Kalendar', 'icon' => 'calendar', 'items' => [
                ['label' => 'Dam olish kunlari', 'desc' => "Bayram va dam olish kunlari", 'url' => route('holidays.index'), 'active' => $is('settings/holidays*')],
            ]],
        ]));
    }
}
