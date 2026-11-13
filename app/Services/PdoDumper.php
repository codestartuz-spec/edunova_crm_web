<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * mysqldump/mysql dasturi ishlamaganda (masalan Alpine'dagi MariaDB klienti MySQL 8 ning
 * caching_sha2_password bilan ulana olmaydi) PHP'ning o'z ulanishi orqali dump oladi va tiklaydi.
 * Format: har bir SQL buyrug'i BITTA qatorda (qator tashlash belgilari ekranlanadi).
 */
class PdoDumper
{
    public const MARKER = '-- ATKO-PDO-DUMP';

    public function dump(?string $connection = null): string
    {
        $db = DB::connection($connection);
        $pdo = $db->getPdo();
        $out = [self::MARKER, 'SET FOREIGN_KEY_CHECKS=0;', "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';"];

        $tables = [];
        foreach ($db->select('SHOW FULL TABLES') as $row) {
            $vals = array_values((array) $row);
            if (($vals[1] ?? 'BASE TABLE') === 'BASE TABLE') {
                $tables[] = $vals[0];
            }
        }

        foreach ($tables as $t) {
            $q = '`'.str_replace('`', '``', $t).'`';
            $create = (array) $db->selectOne("SHOW CREATE TABLE {$q}");
            $ddl = preg_replace('/\s+/', ' ', (string) ($create['Create Table'] ?? array_values($create)[1] ?? ''));
            $out[] = "DROP TABLE IF EXISTS {$q};";
            $out[] = $ddl.';';

            $stmt = $pdo->query("SELECT * FROM {$q}", \PDO::FETCH_NUM);
            $cols = null;
            $batch = [];
            while (($row = $stmt->fetch(\PDO::FETCH_NUM)) !== false) {
                if ($cols === null) {
                    $names = [];
                    for ($i = 0; $i < $stmt->columnCount(); $i++) {
                        $names[] = '`'.str_replace('`', '``', $stmt->getColumnMeta($i)['name']).'`';
                    }
                    $cols = implode(',', $names);
                }
                $batch[] = '('.implode(',', array_map(fn ($v) => $this->value($v), $row)).')';
                if (count($batch) >= 200) {
                    $out[] = "INSERT INTO {$q} ({$cols}) VALUES ".implode(',', $batch).';';
                    $batch = [];
                }
            }
            if ($batch) {
                $out[] = "INSERT INTO {$q} ({$cols}) VALUES ".implode(',', $batch).';';
            }
        }

        $out[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n", $out)."\n";
    }

    public static function isPdoDump(string $sql): bool
    {
        return str_starts_with(ltrim($sql), self::MARKER);
    }

    public function restore(string $sql, ?string $connection = null): void
    {
        $pdo = DB::connection($connection)->getPdo();
        foreach (preg_split('/\R/', $sql) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '--')) {
                continue;
            }
            $pdo->exec($line);
        }
    }

    private function value(mixed $v): string
    {
        if ($v === null) {
            return 'NULL';
        }
        if (is_int($v) || is_float($v)) {
            return (string) $v;
        }
        $v = (string) $v;
        if ($v !== '' && ! mb_check_encoding($v, 'UTF-8')) {
            return '0x'.bin2hex($v);
        }

        return "'".strtr($v, [
            '\\' => '\\\\', "'" => "\\'", "\n" => '\\n', "\r" => '\\r', "\0" => '\\0', "\x1a" => '\\Z',
        ])."'";
    }
}
