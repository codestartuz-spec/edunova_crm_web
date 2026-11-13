<?php

namespace App\Services\Legacy;

use Illuminate\Support\Facades\File;

/** Ko'chirish jarayoni holati (fayl orqali): fon jarayoni yozadi, brauzer o'qiydi. */
class LegacyProgress
{
    public static function path(string $token): string
    {
        return storage_path('app/legacy-import/progress-'.preg_replace('/[^a-z0-9]/i', '', $token).'.json');
    }

    /** @param array<string,mixed> $data */
    public static function write(string $token, array $data): void
    {
        File::ensureDirectoryExists(dirname(self::path($token)));
        $cur = self::read($token) ?? ['log' => []];
        $merged = array_merge($cur, $data, ['updated_at' => time()]);
        file_put_contents(self::path($token), json_encode($merged, JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    /** @return array<string,mixed>|null */
    public static function read(string $token): ?array
    {
        $p = self::path($token);
        if (! is_file($p)) {
            return null;
        }
        $d = json_decode((string) file_get_contents($p), true);

        return is_array($d) ? $d : null;
    }

    public static function log(string $token, string $line, int $pct, string $label): void
    {
        $cur = self::read($token) ?? ['log' => []];
        $log = $cur['log'] ?? [];
        if (($log[count($log) - 1]['text'] ?? null) !== $line) {
            $log[] = ['t' => date('H:i:s'), 'text' => $line];
        }
        self::write($token, ['status' => 'running', 'percent' => $pct, 'label' => $label, 'log' => array_slice($log, -60)]);
    }
}
