<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Backup;
use App\Services\BackupService;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

/** v14: zaxira holati va boshqaruvi (faqat sAdmin). Veb orqali tiklash yo'q — `php artisan app:restore`. */
class BackupController extends Controller
{
    public function index(TelegramService $tg)
    {
        return view('backups.index', [
            'backups' => Backup::orderByDesc('id')->paginate(15),
            'telegramReady' => $tg->configured() && $tg->adminIds() !== [],
            'password' => session('zip_password'),
        ]);
    }

    public function run(Request $request, BackupService $backups): RedirectResponse
    {
        set_time_limit(600);
        $b = $backups->run('manual', $request->user());
        AuditLog::record('backup.manual', null, "Zaxira qo'lda ishga tushirildi: ".$b->status);

        return back()->with($b->status === 'ok' ? 'success' : 'error', $b->status === 'ok' ? 'Zaxira tayyor.' : 'Zaxira xatosi: '.$b->error);
    }

    public function resend(Backup $backup, BackupService $backups, TelegramService $tg): RedirectResponse
    {
        $path = $backups->path($backup);
        abort_unless($path, 404, "Zaxira fayli topilmadi (14 kundan keyin o'chiriladi).");

        $result = $backups->send($path, (string) $backup->sha256, $tg->adminIds());
        $backup->update(['telegram' => $result]);
        $ok = $result && collect($result)->every(fn ($v) => $v === 'ok');

        return back()->with($ok ? 'success' : 'error', $result ? 'Telegramga yuborish natijasi yangilandi.' : "Telegram sozlanmagan yoki Admin ID yo'q.");
    }

    public function download(Request $request, Backup $backup, BackupService $backups)
    {
        $request->validate(['password' => ['required', 'string']], [], ['password' => 'Parol']);
        abort_unless(Hash::check($request->input('password'), $request->user()->password), 403, "Parol noto'g'ri.");
        $path = $backups->path($backup);
        abort_unless($path, 404);

        AuditLog::record('backup.downloaded', null, "Zaxira yuklab olindi: {$backup->file}");

        return response()->download($path);
    }

    /** ZIP parolini ko'rsatish (hisob parolini tasdiqlagandan keyin). */
    public function showPassword(Request $request, BackupService $backups): RedirectResponse
    {
        $request->validate(['password' => ['required', 'string']], [], ['password' => 'Parol']);

        if (! Hash::check($request->input('password'), $request->user()->password)) {
            return back()->with('error', "Parol noto'g'ri.");
        }

        AuditLog::record('backup.password_shown', null, "Zaxira ZIP paroli ko'rsatildi");

        return back()->with('zip_password', $backups->zipPassword());
    }

    public function destroy(Backup $backup, BackupService $backups): RedirectResponse
    {
        if ($path = $backups->path($backup)) {
            File::delete($path);
        }
        $backup->delete();
        AuditLog::record('backup.deleted', null, "Zaxira yozuvi o'chirildi");

        return back()->with('success', "Zaxira o'chirildi.");
    }
}
