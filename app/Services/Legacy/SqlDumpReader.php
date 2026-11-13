<?php

namespace App\Services\Legacy;

use RuntimeException;

/**
 * v15: mysqldump / phpMyAdmin dampidagi `INSERT INTO ... VALUES (...)` buyruqlarini FAQAT O'QIYDI.
 * Fayldagi SQL hech qachon bazada bajarilmaydi (CREATE/DROP/ALTER e'tiborsiz qoldiriladi), shuning uchun
 * zararli fayl baza tuzilmasiga tega olmaydi. Qatorlar PHP massivlari sifatida beriladi.
 */
class SqlDumpReader
{
    public function __construct(private string $path) {}

    /**
     * @return \Generator<int, array{table:string, columns:list<string>, rows:list<list<int|float|string|null>>}>
     */
    public function inserts(): \Generator
    {
        $h = str_ends_with(strtolower($this->path), '.gz') ? @gzopen($this->path, 'rb') : @fopen($this->path, 'rb');
        if (! $h) {
            throw new RuntimeException('Faylni ochib bo\'lmadi.');
        }
        $read = str_ends_with(strtolower($this->path), '.gz') ? fn () => gzgets($h) : fn () => fgets($h);

        try {
            while (($line = $read()) !== false) {
                if (! str_starts_with($line, 'INSERT INTO') && ! str_starts_with($line, 'INSERT IGNORE INTO')) {
                    continue;
                }
                $stmt = $line;
                while (! $this->complete($stmt)) {
                    $next = $read();
                    if ($next === false) {
                        break;
                    }
                    $stmt .= $next;
                }
                $parsed = $this->parseInsert($stmt);
                if ($parsed) {
                    yield $parsed;
                }
            }
        } finally {
            str_ends_with(strtolower($this->path), '.gz') ? gzclose($h) : fclose($h);
        }
    }

    /** Buyruq tugadimi: oxirgi belgi `;` va tirnoqlar juft. */
    private function complete(string $s): bool
    {
        if (! str_ends_with(rtrim($s), ';')) {
            return false;
        }
        $clean = preg_replace('/\\\\./s', '', $s);

        return substr_count($clean, "'") % 2 === 0;
    }

    /** @return array{table:string, columns:list<string>, rows:list<list<mixed>>}|null */
    private function parseInsert(string $stmt): ?array
    {
        if (! preg_match('/^INSERT (?:IGNORE )?INTO `?(\w+)`?\s*(?:\(([^)]*)\))?\s*VALUES\s*/i', $stmt, $m, 0)) {
            return null;
        }
        $columns = isset($m[2]) && $m[2] !== '' ? array_map(fn ($c) => trim($c, " `\t"), explode(',', $m[2])) : [];
        $body = substr($stmt, strlen($m[0]));

        preg_match_all(
            '~\'((?:[^\'\\\\]++|\\\\.|\'\')*+)\'|(-?\d+(?:\.\d+)?(?:[eE][-+]?\d+)?)|(NULL)|([(),;])|(0x[0-9A-Fa-f]+)~si',
            $body,
            $tokens,
            PREG_SET_ORDER
        );

        $rows = [];
        $row = null;
        foreach ($tokens as $t) {
            if (isset($t[4]) && $t[4] !== '') {
                if ($t[4] === '(') {
                    $row = [];
                } elseif ($t[4] === ')') {
                    if ($row !== null) {
                        $rows[] = $row;
                    }
                    $row = null;
                } elseif ($t[4] === ';') {
                    break;
                }

                continue;
            }
            if ($row === null) {
                continue;
            }
            if (isset($t[5]) && $t[5] !== '') {
                throw new RuntimeException('Ikkilik (hex) qiymatlar qo\'llab-quvvatlanmaydi: '.$m[1]);
            }
            if (isset($t[3]) && $t[3] !== '') {
                $row[] = null;
            } elseif (isset($t[2]) && $t[2] !== '') {
                $row[] = preg_match('/^-?\d{1,18}$/', $t[2]) ? (int) $t[2] : $t[2];
            } else {
                $row[] = self::unescape($t[1] ?? '');
            }
        }

        if ($columns === [] && $rows !== []) {
            throw new RuntimeException("Ustun nomlari yo'q INSERT: {$m[1]} (dump 'to'liq INSERT' bilan olinishi kerak).");
        }
        foreach ($rows as $i => $r) {
            if (count($r) !== count($columns)) {
                throw new RuntimeException("{$m[1]}: ".($i + 1).'-qatorda qiymatlar soni ustunlar soniga teng emas.');
            }
        }

        return ['table' => $m[1], 'columns' => $columns, 'rows' => $rows];
    }

    public static function unescape(string $s): string
    {
        if (! str_contains($s, '\\') && ! str_contains($s, "''")) {
            return $s;
        }

        return preg_replace_callback("/\\\\(.)|''/s", function ($m) {
            if (! isset($m[1])) {
                return "'";
            }

            return match ($m[1]) {
                'n' => "\n", 'r' => "\r", 't' => "\t", '0' => "\0", 'Z' => "\x1a", 'b' => "\x08",
                default => $m[1],
            };
        }, $s);
    }
}
