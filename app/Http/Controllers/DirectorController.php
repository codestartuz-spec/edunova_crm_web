<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\AuditLog;
use App\Models\User;
use App\Rules\UzPhone;
use App\Services\DailyReportService;
use App\Services\Telegram\DirectorTelegram;
use App\Support\DirectorPermissions;
use App\Support\Format;
use App\Support\PermissionRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * v14: Direktorlar. Tizimda YAGONA sAdmin bor; u direktorlarni qo'shadi, bloklaydi, olib tashlaydi va
 * ularga o'zidagi ruxsatlardan kerakligini belgilab beradi (ruxsat bermasa - direktor hech narsa qila olmaydi).
 * Direktor filialga bog'lanmagan: barcha filiallarni ko'radi, lekin faqat berilgan ruxsatlar doirasida ishlaydi.
 * Faqat sAdmin (route `role:sadmin`). Direktorlar filial kontekstidan mustaqil, shuning uchun
 * bu yerda User'ni to'g'ridan-to'g'ri rol bo'yicha olamiz (Route::bind('user') emas).
 */
class DirectorController extends Controller
{
    public function index()
    {
        $directors = User::where('role', Role::Director)->whereNull('archived_at')->orderBy('name')->get();

        $counts = \Illuminate\Support\Facades\DB::table('user_permissions')
            ->whereIn('user_id', $directors->pluck('id'))
            ->selectRaw('user_id, count(*) as c')->groupBy('user_id')->pluck('c', 'user_id');

        // v20: har bir direktorning filial ruxsati
        $branchNames = \App\Models\Branch::pluck('name', 'id');
        $assigned = \Illuminate\Support\Facades\DB::table('director_branches')->whereIn('user_id', $directors->pluck('id'))
            ->get()->groupBy('user_id')->map(fn ($rows) => $rows->pluck('branch_id')->map(fn ($id) => $branchNames[$id] ?? null)->filter()->values()->all());

        return view('directors.index', [
            'assigned' => $assigned,
            'totalBranches' => $branchNames->count(),
            'directors' => $directors,
            'counts' => $counts,
            'total' => count(PermissionRegistry::forRole(Role::Director)),
        ]);
    }

    public function create()
    {
        return view('directors.form', $this->branchData(null));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[A-Za-z0-9._@+-]+$/', Rule::unique('users', 'username')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', new UzPhone],
            'password' => ['required', 'confirmed', Password::defaults()],
            'telegram_chat_id' => ['nullable', 'string', 'max:20'],
            'report_enabled' => ['nullable', 'boolean'],
            'report_time' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'branch_mode' => ['nullable', Rule::in(['all', 'selected'])],
            'branch_ids' => ['required_if:branch_mode,selected', 'nullable', 'array'],
            'branch_ids.*' => ['integer', Rule::exists('branches', 'id')],
        ], ['branch_ids.required_if' => "Kamida bitta filialni belgilang yoki «Barcha filiallar» ni tanlang."], ['branch_mode' => 'Filial ruxsati', 'branch_ids' => 'Filiallar', 'name' => 'F.I.O', 'username' => 'Login', 'email' => 'Email', 'phone' => 'Telefon', 'password' => 'Parol', 'telegram_chat_id' => 'Telegram ID', 'report_time' => 'Hisobot vaqti']);

        $phone = Format::canonicalPhone($data['phone']) ?: $data['phone'];
        if (User::whereIn('role', [Role::SAdmin, Role::Director])->where('phone', $phone)->exists()) {
            throw ValidationException::withMessages(['phone' => 'Bu telefon raqami bilan sAdmin yoki direktor allaqachon mavjud.']);
        }

        $director = new User(['name' => $data['name'], 'username' => $data['username'], 'email' => $data['email'] ?? null, 'phone' => $phone, 'password' => $data['password']]);
        $director->role = Role::Director;
        $director->branch_id = null;
        $director->status = UserStatus::Active;
        $director->all_branches = true;
        $director->save();
        $branchNote = $this->saveBranches($director, $data);

        AuditLog::record('director.created', $director, "Yangi direktor qo'shildi: {$director->name}. Filiallar: {$branchNote}");

        // Barcha direktorlar uchun umumiy ruxsatlar shabloni darhol qo'llanadi.
        DirectorPermissions::applyTo($director);

        try {
            $this->saveReport($director, $request, $data);
        } catch (ValidationException $e) {
            // direktor yaratildi, faqat Telegram ulanmadi: qayta yaratmaslik uchun tahrirlash sahifasiga qaytamiz
            return redirect()->route('directors.edit', $director->id)->withErrors($e->errors())
                ->with('success', "{$director->name} qo'shildi, lekin Telegram ulanmadi: pastdagi xabarga qarang.");
        }

        return redirect()->route('directors.index')
            ->with('success', "{$director->name} direktor sifatida qo'shildi. Unga direktorlarning umumiy ruxsatlari berildi.");
    }

    public function edit(int $id)
    {
        $director = $this->find($id);

        return view('directors.form', ['director' => $director] + $this->branchData($director));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $director = $this->find($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[A-Za-z0-9._@+-]+$/', Rule::unique('users', 'username')->ignore($director->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($director->id)],
            'phone' => ['required', 'string', new UzPhone],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'telegram_chat_id' => ['nullable', 'string', 'max:20'],
            'report_enabled' => ['nullable', 'boolean'],
            'report_time' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'branch_mode' => ['nullable', Rule::in(['all', 'selected'])],
            'branch_ids' => ['required_if:branch_mode,selected', 'nullable', 'array'],
            'branch_ids.*' => ['integer', Rule::exists('branches', 'id')],
        ], ['branch_ids.required_if' => "Kamida bitta filialni belgilang yoki «Barcha filiallar» ni tanlang."], ['branch_mode' => 'Filial ruxsati', 'branch_ids' => 'Filiallar', 'name' => 'F.I.O', 'username' => 'Login', 'email' => 'Email', 'phone' => 'Telefon', 'password' => 'Parol', 'telegram_chat_id' => 'Telegram ID', 'report_time' => 'Hisobot vaqti']);

        $phone = Format::canonicalPhone($data['phone']) ?: $data['phone'];
        if (User::whereIn('role', [Role::SAdmin, Role::Director])->where('phone', $phone)->where('id', '!=', $director->id)->exists()) {
            throw ValidationException::withMessages(['phone' => 'Bu telefon raqami bilan sAdmin yoki direktor allaqachon mavjud.']);
        }

        $director->fill(['name' => $data['name'], 'username' => $data['username'], 'email' => $data['email'] ?? null, 'phone' => $phone]);
        $passwordChanged = filled($data['password'] ?? null);
        if ($passwordChanged) {
            $director->password = $data['password'];
        }
        $director->save();
        $before = $this->branchSummary($director);
        $branchNote = $this->saveBranches($director, $data);
        $this->saveReport($director, $request, $data);

        if ($passwordChanged) {
            $sessions = app(\App\Services\SessionService::class);
            $sessions->available() && $sessions->revokeAll($director); // eski qurilmalardan chiqariladi
        }

        AuditLog::record('director.updated', $director, "Direktor ma'lumoti yangilandi: {$director->name}".($passwordChanged ? ' (parol almashtirildi)' : '').". Filiallar: {$branchNote}".($before !== $branchNote ? " (oldin: {$before})" : ''));

        return redirect()->route('directors.index')->with('success', "{$director->name} ma'lumotlari saqlandi.");
    }

    /**
     * v26: kunlik Telegram hisobot sozlamasi (yoqish/vaqt) va Telegram ID.
     * Vaqt bugun allaqachon o'tib ketgan bo'lsa, hisobot bugun darhol yuborilmaydi - ertadan boshlanadi.
     */
    private function saveReport(User $director, Request $request, array $data): void
    {
        if (! $request->has('report_time') && ! $request->has('telegram_chat_id')) {
            return;   // forma yuborilmagan - o'zgarmaydi
        }
        $time = $data['report_time'] ?? $director->report_time ?: DailyReportService::DEFAULT_TIME;
        $director->forceFill([
            'report_enabled' => $request->boolean('report_enabled'),
            'report_time' => $time,
        ]);
        if ($director->isDirty('report_time') && now('Asia/Tashkent')->format('H:i') >= $time) {
            $director->report_last_sent_on = now('Asia/Tashkent')->toDateString();
        }
        $director->save();

        if ($request->has('telegram_chat_id')) {
            app(DirectorTelegram::class)->setChatId($director, $data['telegram_chat_id'] ?? null);
        }
    }

    /** Bir martalik Telegram havolasi (AJAX). */
    public function telegramLink(int $id)
    {
        $link = app(DirectorTelegram::class)->makeLink($this->find($id));

        return $link ? response()->json($link) : response()->json(['message' => 'Telegram bot sozlanmagan (Sozlamalar → Telegram bot).'], 422);
    }

    /** Ulanish holati (havola bosilganini kutish uchun, AJAX). */
    public function telegramStatus(int $id)
    {
        $d = $this->find($id);

        return response()->json(['linked' => $d->telegram_chat_id !== null, 'chat_id' => $d->telegram_chat_id, 'username' => $d->telegram_username]);
    }

    public function telegramUnlink(int $id): RedirectResponse
    {
        $d = $this->find($id);
        app(DirectorTelegram::class)->unlink($d);
        AuditLog::record('director.telegram_unlinked', $d, "Direktorning Telegrami uzildi: {$d->name}");

        return back()->with('success', 'Telegram uzildi.');
    }

    /** Sinov: hisobotni shu direktorga hozir yuboradi (o'z filiallari bo'yicha). */
    public function telegramTest(int $id): RedirectResponse
    {
        $d = $this->find($id);
        $svc = app(DailyReportService::class);
        if (! $d->telegram_chat_id) {
            return back()->withErrors(['telegram_chat_id' => 'Avval Telegramni ulang.']);
        }
        $all = \App\Models\Branch::where('status', 'active')->orderBy('name')->pluck('name', 'id')->all();
        $branches = $svc->branchesFor($d, $all);
        if ($branches === []) {
            return back()->withErrors(['telegram_chat_id' => 'Direktorga faol filial berilmagan.']);
        }
        $ok = $svc->deliverTo(['chat' => (int) $d->telegram_chat_id, 'branches' => $branches, 'time' => $d->report_time, 'user' => $d], \Carbon\CarbonImmutable::now('Asia/Tashkent'));

        return $ok ? back()->with('success', 'Sinov hisobot Telegramga yuborildi.') : back()->withErrors(['telegram_chat_id' => "Yuborib bo'lmadi. Direktor botni bloklagan bo'lishi mumkin; qayta ulang."]);
    }

    /** Barcha direktorlar uchun BITTA umumiy ruxsatlar sahifasi (faqat sAdmin). */
    public function permissions()
    {
        $configured = DirectorPermissions::isConfigured();

        return view('directors.permissions', [
            'groups' => PermissionRegistry::groupsForRole(Role::Director),
            'current' => $configured ? DirectorPermissions::keys() : DirectorPermissions::commonOfExisting(),
            'configured' => $configured,
            'directors' => User::where('role', Role::Director)->whereNull('archived_at')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function updatePermissions(Request $request): RedirectResponse
    {
        $data = $request->validate(['permissions' => ['nullable', 'array'], 'permissions.*' => ['string']]);
        $count = DirectorPermissions::save($data['permissions'] ?? [], $request->user());

        return redirect()->route('directors.permissions.edit')->with('success', "Ruxsatlar saqlandi va {$count} ta direktorga qo'llandi.");
    }

    /** Bloklash yoki qayta faollashtirish. */
    public function toggle(Request $request, int $id): RedirectResponse
    {
        $director = $this->find($id);

        if ($director->isActive()) {
            $director->status = UserStatus::Blocked;
            $director->save();
            AuditLog::record('director.blocked', $director, "Direktor bloklandi: {$director->name}");
            $msg = "{$director->name} bloklandi.";
        } else {
            $director->status = UserStatus::Active;
            $director->save();
            AuditLog::record('director.unblocked', $director, "Direktor faollashtirildi: {$director->name}");
            $msg = "{$director->name} qayta faollashtirildi.";
        }

        return redirect()->route('directors.index')->with('success', $msg);
    }

    /**
     * Direktorni olib tashlash: tarix (jurnal, to'lovlar) saqlanishi uchun yozuv o'chirilmaydi - bloklanadi,
     * parol almashtiriladi, ruxsatlari olinadi va ro'yxatdan yashiriladi.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $director = $this->find($id);

        $director->status = UserStatus::Blocked;
        $director->archived_at = now();
        $director->password = bin2hex(random_bytes(16));
        $director->save();
        $director->syncPermissions([]);

        AuditLog::record('director.removed', $director, "Direktor olib tashlandi: {$director->name}");

        return redirect()->route('directors.index')->with('success', "{$director->name} olib tashlandi.");
    }

    /** Forma uchun: barcha filiallar va direktorga berilganlari. */
    private function branchData(?User $director): array
    {
        return [
            'branchList' => \App\Models\Branch::orderBy('name')->get(['id', 'name', 'status']),
            'selectedBranchIds' => $director ? \Illuminate\Support\Facades\DB::table('director_branches')->where('user_id', $director->id)->pluck('branch_id')->all() : [],
        ];
    }

    private function branchSummary(User $director): string
    {
        if ($director->all_branches ?? true) {
            return 'barcha filiallar';
        }

        $names = \Illuminate\Support\Facades\DB::table('director_branches')->join('branches', 'branches.id', '=', 'director_branches.branch_id')
            ->where('director_branches.user_id', $director->id)->orderBy('branches.name')->pluck('branches.name')->all();

        return $names ? implode(', ', $names) : 'hech qaysi filial';
    }

    /** Filial ruxsatini saqlaydi: "all" - barcha filiallar, "selected" - tanlanganlar (kamida bittasi). */
    private function saveBranches(User $director, array $data): string
    {
        if (empty($data['branch_mode'])) {
            return $this->branchSummary($director);   // forma yuborilmagan - ruxsat o'zgarmaydi
        }

        $all = $data['branch_mode'] === 'all';
        $ids = array_values(array_unique(array_map('intval', $data['branch_ids'] ?? [])));

        if (! $all && $ids === []) {
            throw ValidationException::withMessages(['branch_ids' => 'Kamida bitta filialni belgilang yoki \u00abBarcha filiallar\u00bb ni tanlang.']);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($director, $all, $ids) {
            $director->forceFill(['all_branches' => $all])->save();
            \Illuminate\Support\Facades\DB::table('director_branches')->where('user_id', $director->id)->delete();

            if (! $all) {
                \Illuminate\Support\Facades\DB::table('director_branches')->insert(array_map(fn ($id) => ['user_id' => $director->id, 'branch_id' => $id], $ids));
            }
        });

        \App\Support\BranchContext::forgetRestricted($director);

        return $this->branchSummary($director->refresh());
    }

    private function find(int $id): User
    {
        return User::where('role', Role::Director)->whereNull('archived_at')->findOrFail($id);
    }
}
