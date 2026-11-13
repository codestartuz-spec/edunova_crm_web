<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Models\AuditLog;
use App\Models\LegalAcceptance;
use App\Models\LegalDocument;
use App\Models\User;
use App\Services\LegalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** v14: TIZIM → Huquqiy hujjatlar: matnlarni tahrirlash, versiya chiqarish va rozilik jurnali. */
class LegalAdminController extends Controller
{
    public function index(Request $request, LegalService $legal)
    {
        $legal->ensure();
        $type = array_key_exists((string) $request->query('type'), LegalService::TYPES) ? $request->query('type') : 'privacy';
        $docs = LegalDocument::get()->keyBy('type');
        $users = User::withoutGlobalScopes()->where('status', UserStatus::Active->value)->count();

        $stats = [];
        foreach (LegalService::TYPES as $t => $label) {
            $accepted = LegalAcceptance::where('document_type', $t)->where('version', $docs[$t]->version)->whereNotNull('user_id')->distinct()->count('user_id');
            $stats[$t] = ['accepted' => $accepted, 'pending' => max(0, $users - $accepted)];
        }

        return view('legal.admin', ['type' => $type, 'doc' => $docs[$type], 'docs' => $docs, 'stats' => $stats, 'users' => $users, 'types' => LegalService::TYPES]);
    }

    public function update(Request $request, string $type, LegalService $legal): RedirectResponse
    {
        abort_unless(array_key_exists($type, LegalService::TYPES), 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:60000'],
            'new_version' => ['nullable', 'boolean'],
        ], [], ['title' => 'Sarlavha', 'body' => 'Matn']);

        $doc = $legal->document($type);
        $bump = $request->boolean('new_version');

        $doc->fill(['title' => $data['title'], 'body' => $data['body'], 'updated_by' => $request->user()->id]);
        if ($bump) {
            $doc->version++;
            $doc->published_at = now();
        }
        $doc->save();
        $legal->forgetCache();

        AuditLog::record('legal.updated', null, LegalService::TYPES[$type].($bump ? " — yangi versiya e'lon qilindi (v{$doc->version})" : ' — matn tahrirlandi'));

        return redirect()->route('legal.admin', ['type' => $type])->with('success', $bump
            ? "Yangi versiya (v{$doc->version}) e'lon qilindi. Barcha foydalanuvchilardan qayta rozilik so'raladi."
            : 'Matn saqlandi (versiya o\'zgarmadi, qayta rozilik so\'ralmaydi).');
    }

    public function reset(string $type, LegalService $legal): RedirectResponse
    {
        abort_unless(array_key_exists($type, LegalService::TYPES), 404);

        $doc = $legal->document($type);
        $doc->update(['title' => LegalService::TYPES[$type], 'body' => LegalService::defaultBody($type), 'updated_by' => auth()->id()]);
        AuditLog::record('legal.reset', null, LegalService::TYPES[$type].' standart matnga qaytarildi');

        return redirect()->route('legal.admin', ['type' => $type])->with('success', 'Standart matn tiklandi (versiya o\'zgarmadi).');
    }

    public function journal(Request $request)
    {
        return view('legal.journal', [
            'rows' => $this->query($request)->paginate(25)->withQueryString(),
            'types' => LegalService::TYPES,
            'filters' => $request->only(['type', 'q', 'from', 'to']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $q = $this->query($request);

        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Sana', 'Foydalanuvchi', 'Lavozim', 'Hujjat', 'Versiya', 'IP', 'Qurilma'], ';');
            $q->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [$r->accepted_at->timezone('Asia/Tashkent')->format('d.m.Y H:i:s'), $r->user_name, $r->user_role, LegalService::TYPES[$r->document_type] ?? $r->document_type, $r->version, $r->ip, $r->user_agent], ';');
                }
            });
            fclose($out);
        }, 'rozilik-jurnali-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function query(Request $request)
    {
        $q = LegalAcceptance::query()->orderByDesc('id');

        if (array_key_exists((string) $request->query('type'), LegalService::TYPES)) {
            $q->where('document_type', $request->query('type'));
        }
        if (filled($request->query('q'))) {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], (string) $request->query('q')).'%';
            $q->where(fn ($w) => $w->where('user_name', 'like', $like)->orWhere('ip', 'like', $like));
        }
        foreach (['from' => '>=', 'to' => '<='] as $k => $op) {
            if (filled($request->query($k)) && strtotime((string) $request->query($k))) {
                $q->whereDate('accepted_at', $op, date('Y-m-d', strtotime((string) $request->query($k))));
            }
        }

        return $q;
    }
}
