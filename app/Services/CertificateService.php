<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Certificate;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** v15: sertifikat berish, bekor qilish, qayta berish, PDF va QR. */
class CertificateService
{
    /** @param array<string,mixed> $over qo'lda tahrirlangan maydonlar: student_name, course_name, level_name, title, body, signer_name, signer_title */
    public function issue(User $student, ?Group $group, ?User $by, ?\Carbon\CarbonInterface $on = null, array $over = []): Certificate
    {
        $group ??= null;
        $branchId = (int) ($group?->branch_id ?? $student->branch_id);
        $course = $group?->loadMissing('course')->course?->name ?? 'Kurs';

        for ($try = 0; $try < 5; $try++) {
            try {
                return DB::transaction(function () use ($student, $group, $by, $on, $branchId, $course, $over) {
                    $cert = Certificate::create([
                        'branch_id' => $branchId,
                        'student_id' => $student->id,
                        'group_id' => $group?->id,
                        'number' => $this->nextNumber(),
                        'code' => $this->newCode(),
                        'student_name' => filled($over['student_name'] ?? null) ? $over['student_name'] : $student->name,
                        'course_name' => filled($over['course_name'] ?? null) ? $over['course_name'] : $course,
                        'level_name' => array_key_exists('level_name', $over) ? ($over['level_name'] ?: null) : $group?->name,
                        'title' => $over['title'] ?? null,
                        'body' => $over['body'] ?? null,
                        'signer_name' => $over['signer_name'] ?? null,
                        'signer_title' => $over['signer_title'] ?? null,
                        'issued_on' => ($on ?? now())->toDateString(),
                        'status' => Certificate::ISSUED,
                        'issued_by' => $by?->id,
                    ]);
                    AuditLog::record('certificate.issued', $cert, "Sertifikat berdi: {$cert->number} ({$student->name})", [], [], $branchId);

                    return $cert;
                });
            } catch (QueryException $e) {
                if ($try === 4) {
                    throw $e; // raqam/kod to'qnashuvi - qayta urinildi
                }
            }
        }
        throw new \RuntimeException('Sertifikat yaratib bo\'lmadi.');
    }

    public function revoke(Certificate $cert, string $reason, ?User $by): void
    {
        if ($cert->isRevoked()) {
            return;
        }
        $cert->update(['status' => Certificate::REVOKED, 'revoked_at' => now(), 'revoked_by' => $by?->id, 'revoke_reason' => $reason]);
        AuditLog::record('certificate.revoked', $cert, "Sertifikat bekor qilindi: {$cert->number} — {$reason}", [], [], $cert->branch_id);
    }

    /** Eskisini bekor qilib, yangi raqam va kod bilan qayta beradi. */
    public function reissue(Certificate $cert, ?User $by): Certificate
    {
        $student = User::withoutGlobalScopes()->findOrFail($cert->student_id);
        $group = $cert->group_id ? Group::withTrashed()->find($cert->group_id) : null;
        $new = $this->issue($student, $group, $by, null, $cert->only(['student_name', 'course_name', 'level_name', 'title', 'body', 'signer_name', 'signer_title']));
        $this->revoke($cert, 'Qayta berildi: '.$new->number, $by);
        $cert->update(['replaced_by_id' => $new->id]);

        return $new;
    }

    private function nextNumber(): string
    {
        $year = now()->year;
        $n = Certificate::withoutGlobalScopes()->whereYear('created_at', $year)->count() + 1;

        return sprintf('EDU-%d-%06d', $year, $n);
    }

    private function newCode(): string
    {
        return strtoupper(Str::random(12));
    }

    /** Qisqa tekshiruv manzili: /public siz (APP_URL ning faqat domen qismi). */
    public function verifyUrl(Certificate $cert): string
    {
        $base = config('certificates.verify_base');
        if (blank($base)) {
            $p = parse_url((string) config('app.url'));
            $base = ($p['scheme'] ?? 'https').'://'.($p['host'] ?? request()->getHost()).(isset($p['port']) ? ':'.$p['port'] : '');
        }

        return rtrim($base, '/').'/verify/'.$cert->code;
    }

    public function qrSvg(string $text, int $size = 220): string
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size, 1),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );

        return (new \BaconQrCode\Writer($renderer))->writeString($text);
    }

    /** Sozlama: avval filialniki, bo'lmasa umumiysi. */
    public function setting(string $key, ?int $branchId, mixed $default = null): mixed
    {
        if ($branchId) {
            $v = AppSetting::get("certificate.b{$branchId}.{$key}");
            if (filled($v)) {
                return $v;
            }
        }
        $v = AppSetting::get("certificate.{$key}");

        return filled($v) ? $v : $default;
    }

    public function imageData(?string $path): ?string
    {
        if (blank($path) || ! Storage::disk('public')->exists($path)) {
            return null;
        }
        $mime = Storage::disk('public')->mimeType($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(Storage::disk('public')->get($path));
    }

    public function pdf(Certificate $cert): string
    {
        $branch = Branch::withoutGlobalScopes()->find($cert->branch_id);
        $b = $cert->branch_id;
        $html = view('certificates.pdf', [
            'c' => $cert,
            'branch' => $branch,
            'accent' => preg_match('/^#[0-9a-fA-F]{6}$/', (string) $branch?->effectiveBrandColor()) ? $branch->effectiveBrandColor() : '#e11d34',
            'qr' => 'data:image/svg+xml;base64,'.base64_encode($this->qrSvg($this->verifyUrl($cert))),
            'title' => $cert->title ?: $this->setting('title', $b, 'SERTIFIKAT'),
            'body' => $cert->body ?: $this->setting('body', $b, "ushbu sertifikat egasi o'quv kursini muvaffaqiyatli tamomlaganini tasdiqlaydi."),
            'signer' => $cert->signer_name ?: $this->setting('signer_name', $b, ''),
            'signerTitle' => $cert->signer_title ?: $this->setting('signer_title', $b, 'Direktor'),
            'signature' => $this->imageData($this->setting('signature', $b)),
            'logo' => $this->imageData($this->setting('logo', $b)) ?? $this->logoFallback(),
        ])->render();

        return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape')->output();
    }

    public function logoDataUri(): ?string
    {
        return $this->logoFallback();
    }

    private function logoFallback(): ?string
    {
        $f = public_path('images/logo-edunova.png');

        return is_file($f) ? 'data:image/png;base64,'.base64_encode(file_get_contents($f)) : null;
    }
}
