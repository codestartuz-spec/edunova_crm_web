<?php

namespace App\Http\Controllers;

use App\Enums\BranchStatus;
use App\Enums\Role;
use App\Http\Requests\BranchRequest;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\BranchPayment;
use App\Services\Sms\SmsDispatcher;
use App\Support\Format;
use Illuminate\Support\Facades\DB;
use App\Services\BranchDeletionService;
use App\Support\BranchContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/** Filiallarni faqat sAdmin boshqaradi (route darajasida `role:sadmin`). */
class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount([
            'users as staff_count' => fn ($q) => $q->whereIn('role', [Role::Admin, Role::Manager, Role::Teacher, Role::Operator]),
            'users as students_count' => fn ($q) => $q->where('role', Role::Student),
        ])->orderByRaw("status = 'closed'")->orderBy('name')->get();

        $today = DB::table('sms_branch_usage')->where('day', now('Asia/Tashkent')->toDateString())->pluck('count', 'branch_id');
        $isSadmin = auth()->user()->isSuperAdmin();
        $lastPayments = $isSadmin ? BranchPayment::whereIn('id', BranchPayment::selectRaw('max(id)')->groupBy('branch_id'))->get()->keyBy('branch_id') : collect();

        return view('branches.index', compact('branches', 'today', 'lastPayments', 'isSadmin'));
    }

    /** Filial kartochkasi: sozlamalar, SMS (limit va statistika) va obuna to'lovlari tarixi. */
    public function show(Request $request, Branch $branch, SmsDispatcher $dispatcher)
    {
        $branch->loadCount([
            'users as staff_count' => fn ($q) => $q->whereIn('role', [Role::Admin, Role::Manager, Role::Teacher, Role::Operator]),
            'users as students_count' => fn ($q) => $q->where('role', Role::Student),
        ]);

        $now = now('Asia/Tashkent');
        $days = DB::table('sms_branch_usage')->where('branch_id', $branch->id)->where('day', '>=', $now->copy()->subDays(13)->toDateString())->pluck('count', 'day');
        $chart = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $chart[] = ['date' => $d, 'count' => (int) ($days[$d->toDateString()] ?? 0)];
        }

        $isSadmin = $request->user()->isSuperAdmin();
        $payments = $isSadmin ? $branch->payments()->with('creator:id,name')->latest('id')->paginate(15)->appends(['tab' => 'payments']) : null;

        return view('branches.show', [
            'branch' => $branch,
            'isSadmin' => $isSadmin,
            'tab' => in_array($request->query('tab'), ['settings', 'sms', 'payments'], true) ? $request->query('tab') : 'settings',
            'usedToday' => $dispatcher->usedToday($branch),
            'monthly' => $dispatcher->monthly(3)[$branch->id] ?? [],
            'months' => collect(range(0, 2))->map(fn ($i) => $now->copy()->subMonthsNoOverflow($i)->format('Y-m'))->all(),
            'chart' => $chart,
            'canLimit' => $request->user()->can('sms.gateway'),
            'payments' => $payments,
            'paidTotal' => $isSadmin ? (int) $branch->payments()->sum('amount') : 0,
            'lastPayment' => $isSadmin ? $branch->payments()->latest('id')->first() : null,
        ]);
    }

    public function create()
    {
        return view('branches.form', ['branch' => new Branch]);
    }

    public function store(BranchRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $branch = Branch::create([
            ...$data,
            'code' => Branch::makeCode($data['name']),
            'status' => BranchStatus::Active,
            'opened_at' => now(),
        ]);

        AuditLog::record('branch.created', $branch, "Yangi filial ochildi: {$branch->name}");

        // v20: cheklangan direktor o'zi ochgan filialni yo'qotib qo'ymasligi uchun u avtomatik biriktiriladi
        $actor = $request->user();
        if (BranchContext::restrictedIds($actor) !== null) {
            \Illuminate\Support\Facades\DB::table('director_branches')->insertOrIgnore(['user_id' => $actor->id, 'branch_id' => $branch->id]);
            BranchContext::forgetRestricted($actor);
        }

        return redirect()->route('branches.index')->with('success', "«{$branch->name}» filiali ochildi.");
    }

    public function edit(Branch $branch): RedirectResponse
    {
        return redirect()->route('branches.show', $branch);
    }

    /** Asosiy sozlamalar (nomi, aloqa, ochiq sahifa). Eskiz va SMS limiti alohida - sms(). */
    public function update(BranchRequest $request, Branch $branch): RedirectResponse
    {
        $branch->update($request->safe()->only(['name', 'phone', 'address', 'brand_color', 'public_about']));

        AuditLog::record('branch.updated', $branch, "Filial ma'lumotlari yangilandi: {$branch->name}");

        return redirect()->route('branches.show', ['branch' => $branch, 'tab' => 'settings'])->with('success', "«{$branch->name}» ma'lumotlari saqlandi.");
    }

    /** Filialning SMS sozlamalari: kunlik modem limiti va (ixtiyoriy) o'z Eskiz akkaunti. */
    public function sms(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'sms_modem_daily_limit' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'eskiz_email' => ['nullable', 'email', 'max:255'],
            'eskiz_password' => ['nullable', 'string', 'max:255'],
            'eskiz_from' => ['nullable', 'string', 'max:30'],
        ], [], ['sms_modem_daily_limit' => 'Kunlik limit', 'eskiz_email' => 'Eskiz email', 'eskiz_password' => 'Eskiz paroli', 'eskiz_from' => 'Yuboruvchi nomi']);

        $update = [];
        if ($request->user()->can('sms.gateway') && array_key_exists('sms_modem_daily_limit', $data)) {
            $new = (int) ($data['sms_modem_daily_limit'] ?? 0);
            if ((int) $branch->sms_modem_daily_limit !== $new) {
                AuditLog::record('sms.branch_limit_updated', $branch, "{$branch->name}: kunlik modem limiti {$new} ga o'zgartirildi");
            }
            $update['sms_modem_daily_limit'] = $new;
        }

        if (blank($data['eskiz_email'] ?? null)) {
            // Eskiz email o'chirilsa, filial umumiy akkauntga qaytadi
            $update += ['eskiz_email' => null, 'eskiz_password' => null, 'eskiz_from' => null];
        } else {
            $update['eskiz_email'] = $data['eskiz_email'];
            $update['eskiz_from'] = $data['eskiz_from'] ?? null;
            if (filled($data['eskiz_password'] ?? null)) {
                $update['eskiz_password'] = $data['eskiz_password']; // bo'sh qoldirilsa avvalgi parol saqlanadi
            }
        }

        $branch->update($update);
        AuditLog::record('branch.sms_updated', $branch, "Filial SMS sozlamalari yangilandi: {$branch->name}");

        return redirect()->route('branches.show', ['branch' => $branch, 'tab' => 'sms'])->with('success', 'SMS sozlamalari saqlandi.');
    }

    /** Obuna to'lovi: faqat sAdmin (route darajasida). Faqat tarix — tizimning boshqa qismiga ta'sir qilmaydi. */
    public function storePayment(Request $request, Branch $branch): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:100000000000'],
            'method' => ['required', 'in:'.implode(',', array_keys(BranchPayment::METHODS))],
            'note' => ['nullable', 'string', 'max:500'],
        ], [], ['amount' => "To'lov summasi", 'method' => "To'lov turi", 'note' => "To'lov haqida"]);

        $payment = $branch->payments()->create([
            'amount' => $data['amount'],
            'method' => $data['method'],
            'note' => $data['note'] ?? null,
            'created_by' => $request->user()->id,
            'created_at' => now(),
        ]);

        AuditLog::record('branch.payment_added', $branch, "{$branch->name}: obuna to'lovi qabul qilindi — ".Format::money($payment->amount).' ('.$payment->methodLabel().')');

        return redirect()->route('branches.show', ['branch' => $branch, 'tab' => 'payments'])->with('success', "To'lov qabul qilindi: ".Format::money($payment->amount));
    }

    public function close(Request $request, Branch $branch): RedirectResponse
    {
        $request->validate(['closed_reason' => ['nullable', 'string', 'max:255']]);

        $branch->update([
            'status' => BranchStatus::Closed,
            'closed_at' => now(),
            'closed_reason' => $request->input('closed_reason'),
        ]);

        AuditLog::record('branch.closed', $branch, "Filial yopildi: {$branch->name}");

        return redirect()->route('branches.show', $branch)->with('success', "«{$branch->name}» yopildi va arxivga o'tkazildi.");
    }

    public function reopen(Branch $branch): RedirectResponse
    {
        $branch->update(['status' => BranchStatus::Active, 'closed_at' => null, 'closed_reason' => null]);

        AuditLog::record('branch.reopened', $branch, "Filial qayta ochildi: {$branch->name}");

        return redirect()->route('branches.show', $branch)->with('success', "«{$branch->name}» qayta ochildi.");
    }

    /**
     * v10 (7-band): filialni BUTUNLAY o'chirish - barcha tegishli ma'lumot bilan birga,
     * qaytarib bo'lmaydigan tarzda. Xato bosishdan himoya: filial nomi ANIQ mos kelishi kerak.
     */
    public function destroy(Request $request, Branch $branch, BranchDeletionService $deletion): RedirectResponse
    {
        $request->validate(['confirm_name' => ['required', 'string']]);

        if ($request->input('confirm_name') !== $branch->name) {
            throw ValidationException::withMessages(['confirm_name' => "Filial nomi noto'g'ri kiritildi. O'chirish uchun nomni ANIQ mos holda yozing."]);
        }

        $name = $branch->name;
        $deletion->delete($branch);

        return redirect()->route('branches.index')->with('success', "«{$name}» filiali va unga tegishli barcha ma'lumot butunlay o'chirildi.");
    }

    /** sAdmin ishlayotgan filialni almashtiradi (bo'sh qiymat - barcha filiallar). */
    public function switch(Request $request): RedirectResponse
    {
        $data = $request->validate(['branch_id' => ['nullable', 'integer', BranchContext::branchExists(false)]]);
        $user = $request->user();

        // v20: cheklangan direktor faqat o'ziga ruxsat berilgan filialga o'ta oladi ("Barcha filiallar" - ruxsatlilar yig'indisi)
        $allowed = BranchContext::restrictedIds($user);
        if ($allowed !== null && ! empty($data['branch_id'])) {
            abort_unless(in_array((int) $data['branch_id'], $allowed, true), 403);
        }

        if (! $user->isGlobal()) {
            // v13: faqat qo'shimcha filial berilgan operator, faqat o'ziga ruxsat etilgan filiallarga o'ta oladi
            abort_unless($user->role === \App\Enums\Role::Operator && BranchContext::extraBranchIds($user) !== [], 403);
            $id = (int) ($data['branch_id'] ?? $user->branch_id);
            abort_unless(in_array($id, BranchContext::accessibleBranchIds($user), true), 403);
            $data['branch_id'] = $id;
        }

        BranchContext::select($data['branch_id'] ?? null);

        // v13: filial almashganda har doim bosh sahifaga o'tiladi - aks holda oldingi filialning sahifasi (masalan, /groups/6)
        // yangi filialda 404 berardi.
        return redirect()->route('dashboard');
    }
}
