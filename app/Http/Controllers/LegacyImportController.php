<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\Legacy\LegacyImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\Legacy\LegacyProgress;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\PhpExecutableFinder;
use Illuminate\Support\Facades\Hash;

/** v15: eski bazani (SQL dump) hozirgi tizimga ko'chirish — faqat sAdmin. */
class LegacyImportController extends Controller
{
    private const DIR = 'legacy-import';

    public function index()
    {
        $report = null;
        $rp = storage_path('app/'.self::DIR.'/last-report.json');
        if (is_file($rp)) {
            $report = json_decode((string) file_get_contents($rp), true);
        }

        return view('legacy.index', ['report' => $report]);
    }

    public function upload(Request $request, LegacyImporter $importer)
    {
        $request->validate(['dump' => ['required', 'file', 'max:512000']], [], ['dump' => 'Fayl']);
        $file = $request->file('dump');
        $name = strtolower($file->getClientOriginalName());
        abort_unless(str_ends_with($name, '.sql') || str_ends_with($name, '.sql.gz'), 422, 'Faqat .sql yoki .sql.gz fayl.');

        File::ensureDirectoryExists(storage_path('app/'.self::DIR));
        foreach (File::files(storage_path('app/'.self::DIR)) as $old) {
            if (str_ends_with($old->getFilename(), '.sql') || str_ends_with($old->getFilename(), '.sql.gz')) {
                File::delete($old->getPathname());
            }
        }
        $stored = bin2hex(random_bytes(8)).(str_ends_with($name, '.gz') ? '.sql.gz' : '.sql');
        $file->move(storage_path('app/'.self::DIR), $stored);
        $request->session()->put('legacy_file', $stored);

        try {
            $info = $importer->inspect($this->path($stored));
        } catch (\Throwable $e) {
            return back()->with('error', "Faylni o'qib bo'lmadi: ".$e->getMessage());
        }
        $request->session()->put('legacy_info', ['sadmins' => $info['sadmins']]);

        return redirect()->route('legacy.review');
    }

    public function review(Request $request, LegacyImporter $importer)
    {
        $stored = $request->session()->get('legacy_file');
        if (! $stored || ! is_file($this->path($stored))) {
            return redirect()->route('legacy.index')->with('error', 'Avval fayl yuklang.');
        }

        return view('legacy.review', ['info' => $importer->inspect($this->path($stored))]);
    }

    public function run(Request $request, LegacyImporter $importer): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'], 'confirm' => ['required', 'in:TASDIQLAYMAN'],
            'primary' => ['nullable', 'integer'],
        ], ['confirm.in' => "«TASDIQLAYMAN» so'zini aynan shunday yozing."], ['password' => 'Parol', 'confirm' => 'Tasdiq']);
        if (! Hash::check($request->input('password'), $request->user()->password)) {
            return back()->with('error', "Parol noto'g'ri.");
        }
        $stored = $request->session()->get('legacy_file');
        if (! $stored || ! is_file($this->path($stored))) {
            return redirect()->route('legacy.index')->with('error', 'Fayl topilmadi, qayta yuklang.');
        }

        $token = bin2hex(random_bytes(8));
        LegacyProgress::write($token, ['status' => 'queued', 'percent' => 0, 'label' => 'Navbatda', 'log' => [], 'started_at' => time()]);
        $args = [$this->path($stored), '--force', '--token='.$token];
        if ($request->boolean('backup', true)) {
            $args[] = '--backup';
        }
        if ($request->integer('primary')) {
            $args[] = '--primary='.$request->integer('primary');
        }
        $request->session()->forget(['legacy_file', 'legacy_info']);

        if (! $this->spawn($args)) {
            // Fon jarayonini ishga tushirib bo'lmadi: shu so'rov ichida bajaramiz (holat fayli baribir yoziladi)
            set_time_limit(900);
            ignore_user_abort(true);
            Artisan::call('app:import-legacy', ['file' => $args[0], '--force' => true, '--token' => $token, '--backup' => in_array('--backup', $args, true)] + ($request->integer('primary') ? ['--primary' => $request->integer('primary')] : []));
        }

        return redirect()->route('legacy.progress', $token);
    }

    public function progress(string $token)
    {
        abort_unless(LegacyProgress::read($token), 404);

        return view('legacy.progress', ['token' => $token]);
    }

    public function status(string $token)
    {
        $d = LegacyProgress::read($token);
        abort_unless($d, 404);
        if (in_array($d['status'] ?? '', ['running', 'queued'], true) && time() - (int) ($d['updated_at'] ?? time()) > 180) {
            $d['status'] = 'failed';
            $d['error'] = "Jarayon 3 daqiqadan beri javob bermayapti. Bazani tekshiring yoki buyruq orqali qayta urinib ko'ring: php artisan app:import-legacy";
        }
        $d['elapsed'] = time() - (int) ($d['started_at'] ?? time());

        return response()->json($d)->header('Cache-Control', 'no-store');
    }

    /** Alohida (fon) PHP jarayoni: so'rov tugagach ham davom etadi. */
    private function spawn(array $args): bool
    {
        if (! function_exists('exec') || app()->runningUnitTests()) {
            return false;
        }
        $php = (new PhpExecutableFinder)->find(false) ?: 'php';
        $cmd = escapeshellarg($php).' '.escapeshellarg(base_path('artisan')).' app:import-legacy '.implode(' ', array_map('escapeshellarg', $args));
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $h = @popen('start /B "" '.$cmd.' > NUL 2>&1', 'r');
                if ($h === false) {
                    return false;
                }
                pclose($h);
            } else {
                exec('nohup '.$cmd.' > /dev/null 2>&1 &');
            }
        } catch (\Throwable) {
            return false;
        }

        return true;
    }

    private function path(string $stored): string
    {
        return storage_path('app/'.self::DIR.'/'.basename($stored));
    }
}
