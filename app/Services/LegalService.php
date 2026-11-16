<?php

namespace App\Services;

use App\Models\LegalAcceptance;
use App\Models\LegalDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * v14: huquqiy hujjatlar (maxfiylik siyosati, foydalanish shartlari, cookie siyosati) va ularni qabul qilish jurnali.
 * Har bir hujjatning VERSIYASI bor: sAdmin yangi versiya e'lon qilsa, hamma foydalanuvchidan qayta rozilik so'raladi.
 */
class LegalService
{
    public const TYPES = [
        'privacy' => 'Maxfiylik siyosati',
        'terms' => 'Foydalanish shartlari',
        'cookies' => 'Cookie siyosati',
    ];

    private const CACHE = 'legal.versions';

    public static function enforced(): bool
    {
        return (bool) config('legal.enforce', true);
    }

    /** Hujjat qatorlarini kafolatlaydi (yo'q bo'lsa standart matn bilan yaratadi). */
    public function ensure(): void
    {
        $have = LegalDocument::pluck('type')->all();
        foreach (array_diff(array_keys(self::TYPES), $have) as $type) {
            LegalDocument::create(['type' => $type, 'title' => self::TYPES[$type], 'body' => self::defaultBody($type), 'version' => 1, 'published_at' => now()]);
        }
        if (array_diff(array_keys(self::TYPES), $have)) {
            Cache::forget(self::CACHE);
        }
    }

    /** @return array<string,int> type => joriy versiya */
    public function versions(): array
    {
        return Cache::remember(self::CACHE, 600, function () {
            $this->ensure();

            return LegalDocument::pluck('version', 'type')->map(fn ($v) => (int) $v)->all();
        });
    }

    /** Barcha hujjatlar versiyalaridan imzo: sessiyada saqlanib, har so'rovda bazaga bormaslik uchun. */
    public function signature(): string
    {
        $v = $this->versions();
        ksort($v);

        return md5(json_encode($v));
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE);
    }

    public function document(string $type): LegalDocument
    {
        $this->ensure();

        return LegalDocument::where('type', $type)->firstOrFail();
    }

    /** @return \Illuminate\Support\Collection<int,LegalDocument> foydalanuvchi hali qabul qilmagan (joriy versiyadagi) hujjatlar */
    public function pending(User $user)
    {
        $current = $this->versions();
        $accepted = LegalAcceptance::where('user_id', $user->id)->get(['document_type', 'version'])
            ->groupBy('document_type')->map(fn ($g) => (int) $g->max('version'));

        $need = array_keys(array_filter($current, fn ($v, $t) => ($accepted[$t] ?? 0) < $v, ARRAY_FILTER_USE_BOTH));

        return $need ? LegalDocument::whereIn('type', $need)->get()->sortBy(fn ($d) => array_search($d->type, array_keys(self::TYPES)))->values() : collect();
    }

    public function hasAcceptedAll(User $user, Request $request): bool
    {
        $sig = $this->signature();
        if ($request->hasSession() && $request->session()->get('legal_ok') === $user->id.':'.$sig) {
            return true;
        }

        if ($this->pending($user)->isNotEmpty()) {
            return false;
        }

        if ($request->hasSession()) {
            $request->session()->put('legal_ok', $user->id.':'.$sig);
        }

        return true;
    }

    /** @param array<int,string> $types */
    public function accept(User $user, Request $request, array $types): void
    {
        $docs = LegalDocument::whereIn('type', $types)->get()->keyBy('type');

        foreach ($types as $type) {
            $d = $docs[$type] ?? null;
            if (! $d) {
                continue;
            }
            LegalAcceptance::create([
                'user_id' => $user->id, 'user_name' => $user->name, 'user_role' => $user->role->value,
                'document_type' => $type, 'version' => $d->version, 'document_title' => $d->title,
                'ip' => $request->ip(), 'user_agent' => Str::limit((string) $request->userAgent(), 250, ''), 'accepted_at' => now(),
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->forget('legal_ok');
        }
    }

    /** Hujjat matnini xavfsiz HTML ga aylantiradi: «## Sarlavha», «- ro'yxat», bo'sh qator bilan ajratilgan xatboshilar. */
    public static function render(string $body): string
    {
        $html = '';
        foreach (preg_split("/\R{2,}/", trim($body)) as $block) {
            $lines = preg_split("/\R/", trim($block));
            if (str_starts_with($lines[0], '## ')) {
                $html .= '<h2>'.e(trim(substr(array_shift($lines), 3))).'</h2>';
                if (! $lines) {
                    continue;
                }
            }
            if (collect($lines)->every(fn ($l) => str_starts_with(ltrim($l), '- '))) {
                $html .= '<ul>'.collect($lines)->map(fn ($l) => '<li>'.e(trim(substr(ltrim($l), 2))).'</li>')->implode('').'</ul>';
            } else {
                $html .= '<p>'.collect($lines)->map(fn ($l) => e($l))->implode('<br>').'</p>';
            }
        }

        return $html;
    }

    public static function defaultBody(string $type): string
    {
        return match ($type) {
            'privacy' => <<<'TXT'
## 1. Umumiy qoidalar

Ushbu Maxfiylik siyosati NovaCrm tizimi (bundan keyin — «Tizim») foydalanuvchilarining shaxsiy ma'lumotlari qanday yig'ilishi, saqlanishi, ishlatilishi va himoya qilinishini tushuntiradi. Tizimdan foydalanish orqali siz ushbu siyosat shartlariga rozilik bildirasiz.

## 2. Qanday ma'lumotlar yig'iladi

- Ism-familiya, telefon raqami, tug'ilgan sana, manzil va login (foydalanuvchi nomi).
- O'quv jarayoni: guruhlar, davomad, test natijalari, to'lovlar va balans tarixi.
- Xodimlar uchun: lavozim, filial, ish haqi va harakatlar jurnali.
- Texnik ma'lumotlar: IP manzil, brauzer va qurilma turi, tizimga kirish vaqti.
- Telegram bot orqali bog'langan bo'lsa — Telegram ID va foydalanuvchi nomi (faqat parolni tiklash kodlari uchun).

## 3. Ma'lumotlardan foydalanish maqsadlari

- O'quv markazi faoliyatini yuritish: ro'yxatga olish, davomad, to'lov va hisobotlar.
- Tizimga xavfsiz kirishni ta'minlash va parolni tiklash.
- SMS va Telegram orqali eslatma, bildirishnoma va tasdiqlash kodlarini yuborish.
- Tizim xavfsizligini nazorat qilish, firibgarlik va noqonuniy kirishning oldini olish.

## 4. Ma'lumotlarni uzatish

Shaxsiy ma'lumotlaringiz uchinchi shaxslarga sotilmaydi. Ular faqat xizmat ko'rsatish uchun zarur bo'lgan hollarda (SMS yetkazib beruvchi, Telegram) va qonun talab qilganda uzatilishi mumkin.

## 5. Saqlash muddati va himoya

Ma'lumotlar o'quv markazi bilan munosabat davom etar ekan va qonunda belgilangan muddat davomida saqlanadi. Tizim parollarni shifrlangan (xesh) ko'rinishda saqlaydi, ma'lumotlar bazasining zaxira nusxalari parol bilan himoyalangan.

## 6. Sizning huquqlaringiz

Siz o'z ma'lumotlaringiz bilan tanishish, ularni to'g'rilash yoki o'chirishni so'rash, ma'lumotlarni qayta ishlashga bergan rozilikni qaytarib olish huquqiga egasiz. Buning uchun o'quv markazi ma'muriyatiga murojaat qiling.

## 7. O'zgartirishlar

Ushbu siyosat yangilanishi mumkin. Muhim o'zgarishlarda Tizim sizdan qayta rozilik so'raydi.

## 8. Aloqa

Savollar bo'yicha o'quv markazi ma'muriyatiga murojaat qiling (aloqa ma'lumotlarini bu yerga kiriting).
TXT,
            'terms' => <<<'TXT'
## 1. Shartlarni qabul qilish

NovaCrm tizimidan (bundan keyin — «Tizim») foydalanish orqali siz ushbu Foydalanish shartlariga rozilik bildirasiz. Rozi bo'lmasangiz, Tizimdan foydalanmang.

## 2. Akkaunt va xavfsizlik

- Akkaunt ma'lumotlari (login va parol) maxfiy saqlanishi kerak, ularni boshqalarga bermang.
- Akkaunt orqali bajarilgan barcha harakatlar uchun akkaunt egasi javobgar.
- Akkauntga ruxsatsiz kirish aniqlansa, darhol ma'muriyatga xabar bering.

## 3. Foydalanish qoidalari

- Tizimdan faqat o'quv jarayoni va markaz faoliyati maqsadlarida foydalaniladi.
- Boshqa foydalanuvchilarning ma'lumotlariga ruxsatsiz kirish, ularni nusxalash yoki tarqatish taqiqlanadi.
- Tizimning ishlashiga xalaqit beruvchi, uni buzishga urinuvchi harakatlar taqiqlanadi.
- Yolg'on yoki noto'g'ri ma'lumot kiritish mumkin emas.

## 4. To'lovlar va hisob-kitob

To'lovlar, chegirmalar va qaytarishlar o'quv markazi shartnomasi va amaldagi qoidalariga muvofiq yuritiladi. Tizimdagi balans va to'lov tarixi hisob-kitobning rasmiy yozuvi hisoblanadi.

## 5. Harakatlar jurnali

Xavfsizlik va nazorat maqsadida tizimdagi muhim harakatlar (kirish, o'zgartirishlar, to'lovlar) jurnalga yoziladi.

## 6. Mas'uliyatni cheklash

Tizim «mavjud holida» taqdim etiladi. Texnik ishlar yoki kutilmagan nosozliklar sababli vaqtincha uzilishlar bo'lishi mumkin. Biz ma'lumotlar xavfsizligi uchun barcha oqilona choralarni ko'ramiz.

## 7. Akkauntni cheklash

Shartlar buzilganda ma'muriyat akkauntni bloklash yoki cheklash huquqiga ega.

## 8. O'zgartirishlar

Shartlar yangilanishi mumkin. Yangi versiya e'lon qilinganda Tizim qayta rozilik so'raydi; rozilik bermagan foydalanuvchi Tizimdan foydalanishda davom eta olmaydi.
TXT,
            'cookies' => <<<'TXT'
## 1. Cookie nima

Cookie — brauzeringizda saqlanadigan kichik matn fayli. U saytning to'g'ri ishlashi va qulayligi uchun kerak.

## 2. Biz qanday cookie lardan foydalanamiz

- Zarur cookie lar: sessiya (tizimga kirganingizni eslab turadi), xavfsizlik (XSRF himoyasi) va «Meni eslab qol» tanlovi. Ularsiz Tizim ishlamaydi, ularni o'chirib bo'lmaydi.
- Sozlama cookie lari va brauzer xotirasi: yorug'/qorong'i rejim tanlovi, ushbu cookie roziligi tanlovi.
- Bildirishnoma: brauzer bildirishnomalari (push) yoqilgan bo'lsa, brauzeringiz uchun obuna ma'lumoti saqlanadi.

## 3. Reklama va kuzatuv

Tizim reklama yoki uchinchi tomon kuzatuv cookie laridan foydalanmaydi.

## 4. Cookie larni boshqarish

Cookie larni brauzer sozlamalaridan o'chirishingiz yoki bloklashingiz mumkin. Zarur cookie lar o'chirilsa, Tizimga kirish ishlamay qoladi.

## 5. Rozilik

Tizimga kirishda ushbu siyosatni qabul qilasiz. Roziligingiz sanasi va versiyasi jurnalga yoziladi.
TXT,
        };
    }
}
