<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\SubscriptionRequest;
use App\Services\SubscriptionService;
use App\Support\Format;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** v22: filial obunasi. Direktor: o'z filiallari obunasi, sotib olish/uzaytirish. sAdmin: sozlamalar, cheklarni tasdiqlash. */
class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::query()->orderBy('name')->get();
        $pending = SubscriptionRequest::where('status', SubscriptionRequest::PENDING)->pluck('branch_id')->flip();

        if ($request->user()->role === Role::SAdmin) {
            return view('subscriptions.admin', [
                'branches' => $branches,
                'requests' => SubscriptionRequest::with(['branch', 'user', 'reviewer'])->orderByRaw("status = 'pending' desc")->orderByDesc('id')->paginate(20),
                'pendingCount' => SubscriptionRequest::pendingCount(),
            ]);
        }

        $ids = $branches->pluck('id');

        return view('subscriptions.index', [
            'branches' => $branches,
            'pending' => $pending,
            'history' => SubscriptionRequest::with('branch')->whereIn('branch_id', $ids)->orderByDesc('id')->limit(30)->get(),
            'configured' => SubscriptionService::priceConfigured(),
        ]);
    }

    public function buy(Branch $branch)
    {
        abort_unless(auth()->user()->role === Role::Director, 403);

        return view('subscriptions.buy', [
            'branch' => $branch,
            'configured' => SubscriptionService::priceConfigured(),
            'monthly' => SubscriptionService::price('monthly'),
            'yearly' => SubscriptionService::price('yearly'),
            'card' => SubscriptionService::setting('card_number'),
            'holder' => SubscriptionService::setting('card_holder'),
            'terms' => SubscriptionService::setting('terms') ?: SubscriptionService::DEFAULT_TERMS,
            'hasPending' => SubscriptionRequest::where('branch_id', $branch->id)->where('status', SubscriptionRequest::PENDING)->exists(),
        ]);
    }

    public function store(Request $request, Branch $branch, SubscriptionService $service): RedirectResponse
    {
        abort_unless($request->user()->role === Role::Director, 403);

        if (! SubscriptionService::priceConfigured()) {
            return back()->with('error', "Obuna narxi va karta raqami hali belgilanmagan. Administrator bilan bog'laning.");
        }

        if (SubscriptionRequest::where('branch_id', $branch->id)->where('status', SubscriptionRequest::PENDING)->exists()) {
            return back()->with('error', "Bu filial uchun chek allaqachon tekshiruvda. Natijani kuting.");
        }

        $data = $request->validate([
            'plan' => ['required', 'in:monthly,yearly'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [], ['plan' => 'Tarif', 'receipt' => 'Chek rasmi', 'note' => 'Izoh']);

        try {
            $service->submit($branch, $request->user(), $data['plan'], $request->file('receipt'), $data['note'] ?? null);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('subscriptions.index')->with('success', "Chek yuborildi. Administrator uni ".SubscriptionRequest::REVIEW_TIME." ichida ko'rib chiqadi; tasdiqlangach obuna avtomatik faollashadi.");
    }

    /** Chek rasmini faqat sAdmin va shu filialni ko'ra oladigan direktor ko'radi (private disk). */
    public function receipt(SubscriptionRequest $subscriptionRequest)
    {
        $user = auth()->user();
        abort_unless($user->role === Role::SAdmin || ($user->role === Role::Director && Branch::query()->whereKey($subscriptionRequest->branch_id)->exists()), 403);
        abort_unless(Storage::disk('local')->exists($subscriptionRequest->receipt_path), 404);

        return Storage::disk('local')->response($subscriptionRequest->receipt_path, null, ['Cache-Control' => 'private, max-age=0, no-store']);
    }

    public function approve(Request $request, SubscriptionRequest $subscriptionRequest, SubscriptionService $service): RedirectResponse
    {
        $req = $service->approve($subscriptionRequest, $request->user());

        return back()->with('success', $req->status === SubscriptionRequest::APPROVED
            ? "Tasdiqlandi. «{$req->branch->name}» obunasi {$req->new_ends_on->copy()->subDay()->format('d.m.Y')} kunigacha."
            : "Bu so'rov allaqachon ko'rib chiqilgan.");
    }

    public function reject(Request $request, SubscriptionRequest $subscriptionRequest, SubscriptionService $service): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'Rad etish sababi']);
        $service->reject($subscriptionRequest, $request->user(), $data['reason']);

        return back()->with('success', "So'rov rad etildi.");
    }

    /**
     * sAdmin: chek yozuvini (va rasmini) o'chiradi. Filial obunasi muddatiga va moliya yozuviga TEGMAYDI -
     * muddatni o'zgartirish uchun filial obunasini qo'lda tahrirlang.
     */
    public function destroy(SubscriptionRequest $subscriptionRequest): RedirectResponse
    {
        $req = $subscriptionRequest;
        $branch = Branch::withoutGlobalScopes()->find($req->branch_id);
        $desc = "{$branch?->name}: obuna cheki o'chirildi ({$req->planLabel()}, ".Format::money($req->amount).', '.$req->status.')';

        if ($req->receipt_path) {
            Storage::disk('local')->delete($req->receipt_path);
        }
        $req->delete();

        AuditLog::record('subscription.request_deleted', $branch, $desc, [], [], $req->branch_id);

        return back()->with('success', "Chek o'chirildi.");
    }

    // ---- sAdmin: sozlamalar va filial obunasini qo'lda boshqarish -------------------------------------------

    public function settings()
    {
        return view('subscriptions.settings', [
            'v' => collect(SubscriptionService::KEYS)->mapWithKeys(fn ($k) => [$k => SubscriptionService::setting($k)])->all(),
            'defaultTerms' => SubscriptionService::DEFAULT_TERMS,
        ]);
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'price_monthly' => ['required', 'integer', 'min:1', 'max:100000000000'],
            'price_yearly' => ['required', 'integer', 'min:1', 'max:100000000000'],
            'card_number' => ['required', 'string', 'regex:/^[0-9 ]{12,23}$/'],
            'card_holder' => ['nullable', 'string', 'max:100'],
            'terms' => ['nullable', 'string', 'max:3000'],
            'trial_days' => ['required', 'integer', 'min:0', 'max:365'],
        ], ['card_number.regex' => "Karta raqami faqat raqamlardan iborat bo'lishi kerak (12–19 raqam)."],
            ['price_monthly' => 'Oylik narx', 'price_yearly' => 'Yillik narx', 'card_number' => 'Karta raqami', 'card_holder' => 'Karta egasi', 'terms' => 'Shartlar', 'trial_days' => 'Sinov muddati']);

        if ($data['price_yearly'] <= $data['price_monthly'] || $data['price_yearly'] > 12 * $data['price_monthly']) {
            return back()->withInput()->withErrors(['price_yearly' => "Yillik narx oylikdan qimmat, lekin 12 oylik narxdan oshmasligi kerak (".Format::money(12 * $data['price_monthly'])." gacha)."]);
        }

        foreach ($data as $k => $v) {
            AppSetting::put('subscription.'.$k, $k === 'card_number' ? trim(preg_replace('/\s+/', ' ', $v)) : $v);
        }

        AuditLog::record('subscription.settings', null, "Obuna sozlamalari o'zgartirildi: oylik ".Format::money($data['price_monthly']).', yillik '.Format::money($data['price_yearly']));

        return back()->with('success', 'Obuna sozlamalari saqlandi.');
    }

    /** sAdmin: filialni "obunasiz" qilish yoki tugash sanasini qo'lda belgilash. */
    public function updateBranch(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'subscription_exempt' => ['nullable', 'boolean'],
            'subscription_ends_on' => ['nullable', 'date'],
        ], [], ['subscription_ends_on' => 'Tugash sanasi']);

        $exempt = (bool) ($data['subscription_exempt'] ?? false);
        $branch->subscription_exempt = $exempt;
        if (! $exempt && ! empty($data['subscription_ends_on'])) {
            $branch->subscription_ends_on = $data['subscription_ends_on'];
        }
        $branch->save();

        AuditLog::record('subscription.manual', $branch, "{$branch->name}: obuna qo'lda o'zgartirildi — ".($exempt ? 'obunasiz (doim faol)' : 'oxirgi kun '.$branch->subscriptionLastDay()?->format('d.m.Y')), [], [], $branch->id);

        return back()->with('success', "«{$branch->name}» obunasi yangilandi.");
    }
}
