<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DemoRequest;
use App\Services\Telegram\TelegramService;
use App\Support\Brand;
use App\Support\Format;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

/**
 * v25: landing sahifasidan demo so'rov.
 *  - store: OCHIQ (mehmon), spamdan himoyalangan; so'rov bazaga yoziladi va sAdminlarga Telegram orqali xabar ketadi;
 *  - index/update/destroy: faqat sAdmin (marshrutda `role:sadmin`).
 */
class DemoRequestController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $locale = $request->input('locale') === 'ru' ? 'ru' : 'uz';
        $ru = $locale === 'ru';

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'phone' => ['required', 'string', 'max:30', function ($attr, $value, $fail) use ($ru) {
                if (Format::normalizePhone($value) === null) {
                    $fail($ru ? 'Введите номер в формате +998 90 123 45 67.' : "Telefon raqamini +998 90 123 45 67 ko'rinishida kiriting.");
                }
            }],
            'center' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', Rule::in(array_keys(DemoRequest::SIZES))],
            'message' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => $ru ? 'Укажите ваше имя.' : 'Ismingizni kiriting.',
            'name.min' => $ru ? 'Имя слишком короткое.' : 'Ism juda qisqa.',
            'phone.required' => $ru ? 'Укажите номер телефона.' : 'Telefon raqamini kiriting.',
        ]);

        // Spamdan himoya: yashirin maydon (robotlar to'ldiradi) va vaqt tuzog'i (forma 3 soniyadan tez yuborilsa).
        // Robotga xato ko'rsatmaymiz - muvaffaqiyatli deb javob beramiz, lekin hech narsa saqlanmaydi.
        if ($request->filled('website') || $this->tooFast((string) $request->input('t'))) {
            return $this->done($request, $locale);
        }

        $phone = Format::normalizePhone($data['phone']); // 998901234567 (tekshiruvdan o'tgan)

        // Bir xil raqamdan 30 daqiqa ichida takroriy so'rov yangi yozuv ochmaydi (adminga ham qayta xabar bormaydi)
        $dup = DemoRequest::where('phone', $phone)->where('created_at', '>=', now()->subMinutes(30))->exists();
        if (! $dup) {
            $demo = DemoRequest::create([
                'name' => trim(strip_tags($data['name'])),
                'phone' => $phone,
                'center' => isset($data['center']) ? trim(strip_tags($data['center'])) ?: null : null,
                'size' => $data['size'] ?? null,
                'message' => isset($data['message']) ? trim(strip_tags($data['message'])) ?: null : null,
                'locale' => $locale,
                'ip_hash' => hash('sha256', (string) $request->ip().config('app.key')),
            ]);

            // Javobni kutdirmaslik uchun Telegram'ga javobdan KEYIN yuboriladi; xato so'rovni yo'qotmaydi
            dispatch(fn () => $this->notifyAdmins($demo))->afterResponse();
        }

        return $this->done($request, $locale);
    }

    private function done(Request $request, string $locale): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect(($locale === 'ru' ? '/ru' : '/').'#demo')->with('demo_ok', true);
    }

    private function tooFast(string $token): bool
    {
        if ($token === '') {
            return true;
        }
        try {
            return (time() - (int) Crypt::decryptString($token)) < 3;
        } catch (\Throwable) {
            return true;
        }
    }

    /** Barcha sAdmin Telegram ID lariga xabar; hech bo'lmaganda bittasiga yetsa «yuborildi» deb belgilanadi. */
    private function notifyAdmins(DemoRequest $demo): void
    {
        try {
            $tg = app(TelegramService::class);
            if (! $tg->configured() || ! $tg->adminIds()) {
                return;
            }

            $lines = [
                '🆕 <b>'.e(Brand::short()).": yangi demo so'rov</b>",
                '👤 '.e($demo->name),
                '📞 +'.e($demo->phone),
            ];
            if ($demo->center) {
                $lines[] = '🏫 '.e($demo->center);
            }
            if ($demo->size) {
                $lines[] = "👥 O'quvchilar: ".e(DemoRequest::SIZES[$demo->size] ?? $demo->size);
            }
            if ($demo->message) {
                $lines[] = '💬 '.e($demo->message);
            }
            $lines[] = '🌐 Til: '.strtoupper($demo->locale);
            $lines[] = "\n".route('demo.index');

            $sent = false;
            foreach ($tg->adminIds() as $id) {
                $sent = $tg->sendMessage($id, implode("\n", $lines)) || $sent;
            }
            if ($sent) {
                $demo->forceFill(['telegram_sent' => true])->save();
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ---- sAdmin ----

    public function index(Request $request)
    {
        $status = $request->query('status');
        $q = DemoRequest::query()->orderByRaw("status = 'new' desc")->latest('id');
        if (is_string($status) && array_key_exists($status, DemoRequest::STATUSES)) {
            $q->where('status', $status);
        }

        return view('demo.index', [
            'requests' => $q->paginate(30)->withQueryString(),
            'status' => $status,
            'counts' => DemoRequest::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function update(Request $request, DemoRequest $demo): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(DemoRequest::STATUSES))],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $demo->update(['status' => $data['status'], 'note' => $data['note'] ?? null]);
        AuditLog::record('demo.updated', null, "Demo so'rov #{$demo->id}: ".DemoRequest::STATUSES[$demo->status]);

        return back()->with('success', 'Saqlandi.');
    }

    public function destroy(DemoRequest $demo): RedirectResponse
    {
        $demo->delete();
        AuditLog::record('demo.deleted', null, "Demo so'rov #{$demo->id} o'chirildi");

        return back()->with('success', "So'rov o'chirildi.");
    }
}
