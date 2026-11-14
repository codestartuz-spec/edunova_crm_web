<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\User;
use App\Services\CertificateService;
use App\Support\BranchContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function __construct(private CertificateService $svc) {}

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $status = $request->query('status');

        $certs = Certificate::query()->with('branch:id,name')
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x->where('student_name', 'like', "%{$q}%")->orWhere('number', 'like', "%{$q}%")->orWhere('course_name', 'like', "%{$q}%")))
            ->when(in_array($status, [Certificate::ISSUED, Certificate::REVOKED], true), fn ($w) => $w->where('status', $status))
            ->latest('id')->paginate(20)->withQueryString();

        return view('certificates.index', ['certs' => $certs, 'q' => $q, 'status' => $status, 'svc' => $this->svc]);
    }

    private const FIELD_RULES = [
        'student_name' => ['nullable', 'string', 'max:120'],
        'course_name' => ['nullable', 'string', 'max:120'],
        'level_name' => ['nullable', 'string', 'max:120'],
        'title' => ['required', 'string', 'max:60'],
        'body' => ['required', 'string', 'max:300'],
        'signer_name' => ['nullable', 'string', 'max:80'],
        'signer_title' => ['nullable', 'string', 'max:60'],
        'issued_on' => ['required', 'date', 'before_or_equal:today'],
        'group_id' => ['nullable', 'integer'],
        'force' => ['nullable', 'boolean'],
    ];

    /** Tahrirlash oynasi uchun umumiy boshlang'ich qiymatlar (sozlamalardan). */
    private function defaults(?int $branchId): array
    {
        $g = fn ($k, $d = '') => $this->svc->setting($k, $branchId, $d);

        return [
            'title' => $g('title', 'SERTIFIKAT'),
            'body' => $g('body', "ushbu sertifikat egasi o'quv kursini muvaffaqiyatli tamomlaganini tasdiqlaydi."),
            'signer_name' => $g('signer_name'),
            'signer_title' => $g('signer_title', 'Direktor'),
            'issued_on' => today()->toDateString(),
            'logo' => $this->svc->imageData($g('logo', null)) ?? $this->svc->logoDataUri(),
            'signature' => $this->svc->imageData($g('signature', null)),
        ];
    }

    private function editorBase(?int $branchId): array
    {
        $branch = \App\Models\Branch::withoutGlobalScopes()->find($branchId);
        $color = (string) $branch?->effectiveBrandColor();

        return [
            'd' => $this->defaults($branchId),
            'branchName' => (string) $branch?->name,
            'accent' => preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : '#e11d34',
        ];
    }

    /** Berishdan oldin: jonli ko'rinish, tahrirlash va tekshiruv (bitta o'quvchi). */
    public function createStudent(Request $request, User $student)
    {
        abort_unless($student->role === Role::Student, 404);

        $groups = Group::whereIn('id', GroupStudent::where('student_id', $student->id)->pluck('group_id'))->with('course')->orderByDesc('id')->get();
        $group = $request->filled('group_id') ? $groups->firstWhere('id', (int) $request->query('group_id')) : ($groups->first());
        $base = $this->editorBase((int) ($group?->branch_id ?? $student->branch_id));

        $checks = [];
        if (! $group) {
            $checks[] = ['level' => 'warn', 'text' => "O'quvchi hech qaysi guruhga biriktirilmagan: kurs nomini qo'lda kiriting."];
        }
        if ($group && Certificate::where('student_id', $student->id)->where('group_id', $group->id)->where('status', Certificate::ISSUED)->exists()) {
            $checks[] = ['level' => 'block', 'text' => "Bu guruh uchun amaldagi sertifikat allaqachon berilgan."];
        }
        if ($student->balance < 0) {
            $checks[] = ['level' => 'warn', 'text' => "O'quvchida qarzdorlik bor: ".\App\Support\Format::money(-$student->balance)."."];
        }
        if ($group && $group->status !== Group::FINISHED) {
            $checks[] = ['level' => 'warn', 'text' => "Guruh hali tugamagan (holati: {$group->status_label})."];
        }
        if (blank($base['d']['signer_name'])) {
            $checks[] = ['level' => 'warn', 'text' => "Imzolovchi F.I.O kiritilmagan (quyida yozishingiz yoki Sozlamalarda saqlashingiz mumkin)."];
        }
        if (! $base['d']['signature']) {
            $checks[] = ['level' => 'warn', 'text' => "Imzo rasmi yuklanmagan (Sertifikatlar → Sozlamalar)."];
        }

        return view('certificates.create', $base + [
            'mode' => 'student', 'action' => route('certificates.issue-student', $student->id), 'student' => $student, 'groups' => $groups,
            'init' => ['student_name' => $student->name, 'course_name' => $group?->course?->name ?? '', 'level_name' => $group?->name ?? '', 'group_id' => $group?->id],
            'checks' => $checks, 'members' => collect(),
        ]);
    }

    /** Guruh bo'yicha ommaviy berishdan oldin: umumiy matnlarni tahrirlash va o'quvchilarni tanlash. */
    public function createGroup(Group $group)
    {
        $group->loadMissing('course');
        $base = $this->editorBase((int) $group->branch_id);

        $ids = GroupStudent::where('group_id', $group->id)->where('is_active', true)->pluck('student_id');
        $issued = Certificate::where('group_id', $group->id)->where('status', Certificate::ISSUED)->pluck('student_id')->all();
        $members = User::withoutGlobalScopes()->whereIn('id', $ids)->where('role', Role::Student->value)->orderBy('name')->get()
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'debt' => $u->balance < 0 ? -$u->balance : 0, 'has' => in_array($u->id, $issued, true)]);

        $checks = [];
        if ($members->isEmpty()) {
            $checks[] = ['level' => 'block', 'text' => "Guruhda faol o'quvchi yo'q."];
        }
        if ($group->status !== Group::FINISHED) {
            $checks[] = ['level' => 'warn', 'text' => "Guruh hali tugamagan (holati: {$group->status_label})."];
        }
        if ($members->where('has', true)->isNotEmpty()) {
            $checks[] = ['level' => 'info', 'text' => $members->where('has', true)->count()." ta o'quvchida allaqachon amaldagi sertifikat bor: ular avtomatik o'tkazib yuboriladi."];
        }
        if ($members->where('debt', '>', 0)->isNotEmpty()) {
            $checks[] = ['level' => 'warn', 'text' => $members->where('debt', '>', 0)->count()." ta o'quvchida qarzdorlik bor (ro'yxatda belgilangan)."];
        }
        if (blank($base['d']['signer_name'])) {
            $checks[] = ['level' => 'warn', 'text' => "Imzolovchi F.I.O kiritilmagan."];
        }
        if (! $base['d']['signature']) {
            $checks[] = ['level' => 'warn', 'text' => "Imzo rasmi yuklanmagan (Sertifikatlar → Sozlamalar)."];
        }

        return view('certificates.create', $base + [
            'mode' => 'group', 'action' => route('certificates.issue-group', $group), 'student' => null, 'groups' => collect(),
            'init' => ['student_name' => $members->first()['name'] ?? "O'quvchi F.I.O", 'course_name' => $group->course?->name ?? '', 'level_name' => $group->name, 'group_id' => $group->id],
            'checks' => $checks, 'members' => $members,
        ]);
    }

    /** Guruhdagi tanlangan o'quvchilarga ommaviy berish (umumiy tahrirlangan matnlar bilan). */
    public function issueGroup(Request $request, Group $group): RedirectResponse
    {
        $rules = collect(self::FIELD_RULES)->except(['student_name', 'group_id', 'force'])->all() + ['student_ids' => ['required', 'array', 'min:1'], 'student_ids.*' => ['integer']];
        $data = $request->validate($rules, [], ['student_ids' => "O'quvchilar", 'issued_on' => 'Sana', 'title' => 'Sarlavha', 'body' => 'Matn']);

        $members = GroupStudent::where('group_id', $group->id)->where('is_active', true)->whereIn('student_id', $data['student_ids'])->pluck('student_id');
        $over = collect($data)->only(['course_name', 'level_name', 'title', 'body', 'signer_name', 'signer_title'])->all();

        $done = 0;
        foreach (User::withoutGlobalScopes()->whereIn('id', $members)->where('role', Role::Student->value)->get() as $student) {
            if (Certificate::where('student_id', $student->id)->where('group_id', $group->id)->where('status', Certificate::ISSUED)->exists()) {
                continue;
            }
            $this->svc->issue($student, $group, $request->user(), \Carbon\Carbon::parse($data['issued_on']), $over);
            $done++;
        }

        return redirect()->route('certificates.index')->with('success', $done ? "{$done} ta sertifikat berildi." : "Yangi sertifikat berilmadi (hammasiga allaqachon berilgan).");
    }

    public function issueStudent(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->role === Role::Student, 404);
        $data = $request->validate(self::FIELD_RULES, [], ['student_name' => 'F.I.O', 'course_name' => 'Kurs', 'issued_on' => 'Sana', 'title' => 'Sarlavha', 'body' => 'Matn']);
        $group = ! empty($data['group_id']) ? Group::findOrFail($data['group_id']) : null;

        if ($group && empty($data['force']) && Certificate::where('student_id', $student->id)->where('group_id', $group->id)->where('status', Certificate::ISSUED)->exists()) {
            return back()->withInput()->withErrors(['group_id' => "Bu guruh uchun sertifikat allaqachon berilgan."]);
        }
        $over = collect($data)->only(['student_name', 'course_name', 'level_name', 'title', 'body', 'signer_name', 'signer_title'])->all();
        $cert = $this->svc->issue($student, $group, $request->user(), \Carbon\Carbon::parse($data['issued_on']), $over);

        return redirect()->route('certificates.index')->with('success', "Sertifikat berildi: {$cert->number}");
    }

    public function download(Certificate $certificate)
    {
        return $this->pdfResponse($certificate);
    }

    /** O'quvchi o'z kabinetidan faqat o'zining amaldagi sertifikatini yuklaydi. */
    public function mine(Request $request, int $id)
    {
        $cert = Certificate::where('student_id', $request->user()->id)->where('status', Certificate::ISSUED)->findOrFail($id);

        return $this->pdfResponse($cert);
    }

    private function pdfResponse(Certificate $c)
    {
        abort_if($c->isRevoked(), 404);

        return response($this->svc->pdf($c), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$c->number.'.pdf"',
        ]);
    }

    public function revoke(Request $request, Certificate $certificate): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:200']], [], ['reason' => 'Sabab']);
        $this->svc->revoke($certificate, $data['reason'], $request->user());

        return back()->with('success', 'Sertifikat bekor qilindi.');
    }

    public function reissue(Request $request, Certificate $certificate): RedirectResponse
    {
        $new = $this->svc->reissue($certificate, $request->user());

        return back()->with('success', "Qayta berildi: {$new->number}");
    }

    public function settings()
    {
        $b = BranchContext::id() ?: null;
        $get = fn ($k, $d = '') => $this->svc->setting($k, $b, $d);

        return view('certificates.settings', [
            'branchScoped' => (bool) $b,
            'v' => ['title' => $get('title', 'SERTIFIKAT'), 'body' => $get('body', "ushbu sertifikat egasi o'quv kursini muvaffaqiyatli tamomlaganini tasdiqlaydi."), 'signer_name' => $get('signer_name'), 'signer_title' => $get('signer_title', 'Direktor')],
            'signature' => $this->svc->imageData($get('signature', null)),
            'logo' => $this->svc->imageData($get('logo', null)),
            // Jonli ko'rinish uchun (faqat ko'rsatish): zaxira logo, filial nomi/rangi va namunaviy QR
            'fallbackLogo' => $this->svc->logoDataUri(),
            'branchName' => $b ? (string) \App\Models\Branch::withoutGlobalScopes()->whereKey($b)->value('name') : '',
            'accent' => ($br = $b ? \App\Models\Branch::withoutGlobalScopes()->find($b) : null) && preg_match('/^#[0-9a-fA-F]{6}$/', (string) $br->effectiveBrandColor()) ? $br->effectiveBrandColor() : '#e11d34',
            'sampleQr' => 'data:image/svg+xml;base64,'.base64_encode($this->svc->qrSvg(url('/verify/NAMUNA'))),
        ]);
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'body' => ['required', 'string', 'max:300'],
            'signer_name' => ['nullable', 'string', 'max:80'],
            'signer_title' => ['nullable', 'string', 'max:60'],
            'signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ]);
        $b = BranchContext::id() ?: null;
        $prefix = $b ? "certificate.b{$b}." : 'certificate.';

        foreach (['title', 'body', 'signer_name', 'signer_title'] as $k) {
            AppSetting::put($prefix.$k, $data[$k] ?? '');
        }
        foreach (['signature', 'logo'] as $img) {
            if ($request->hasFile($img)) {
                $path = $request->file($img)->store('certificates', 'public');
                AppSetting::put($prefix.$img, $path);
            }
        }
        AuditLog::record('certificate.settings', null, 'Sertifikat sozlamalarini yangiladi');

        return back()->with('success', 'Saqlandi.');
    }
}
