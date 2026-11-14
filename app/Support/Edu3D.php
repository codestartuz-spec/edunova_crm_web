<?php

namespace App\Support;

/**
 * v28: sahifa turiga qarab 3D darajasi va animatsion SVG fon sahnasi.
 *
 *  mode  - rich: stat kartalari va bosiladigan kartalar qiyshayadi, fon kuchli parallaks bilan;
 *          calm: jadval/ro'yxat/moliya sahifalari - chuqurlik va soyalar, o'qishga xalaqit bermaydigan sokin fon;
 *          form: forma sahifalari - botiq maydonlar, tugma "klavisha"si, sokin fon.
 *  scene - supplies (o'quv buyumlari), math (formula, raqam, harf, tanga), path (bilim yo'li), orbs (nur va suhbat belgilari).
 */
class Edu3D
{
    public const SCENES = ['supplies', 'math', 'path', 'orbs'];

    public const MODES = ['rich', 'calm', 'form'];

    /** Birinchi segment -> sahna. Aniq nom ({prefix.name}) umumiy prefiksdan ustun turadi. */
    private const SCENE = [
        'dashboard' => 'path', 'statistics' => 'path', 'reports' => 'path', 'attendance.stats' => 'path',
        'payments' => 'math', 'finance' => 'math', 'cashbox' => 'math', 'payroll' => 'math', 'subscriptions' => 'math', 'usluga-settings' => 'math', 'discount-rules' => 'math',
        'leads' => 'orbs', 'sms' => 'orbs', 'telegram' => 'orbs', 'notifications' => 'orbs', 'apply' => 'orbs', 'push' => 'orbs', 'tutorials' => 'orbs', 'demo' => 'orbs',
    ];

    private const RICH = ['dashboard', 'statistics', 'student', 'tutorials', 'demo', 'apply', 'login', 'password', 'confirm', 'brand', 'legal'];

    private const FORM = ['settings', 'contract-settings', 'usluga-settings', 'discount-rules', 'profile', 'imports', 'legacy', 'holidays', 'catalog', 'backups'];

    /** @return array{scene:string, mode:string} */
    public static function forRoute(?string $name): array
    {
        $name = (string) $name;
        $head = explode('.', $name)[0];

        $scene = self::SCENE[$name] ?? self::SCENE[$head] ?? 'supplies';

        $last = substr($name, (int) strrpos($name, '.') + 1);
        $isForm = in_array($last, ['create', 'edit', 'buy', 'new', 'import'], true);

        $mode = match (true) {
            in_array($head, self::RICH, true) => 'rich',
            in_array($head, self::FORM, true) || $isForm => 'form',
            default => 'calm',
        };

        return ['scene' => $scene, 'mode' => $mode];
    }

    /** Joriy so'rov uchun. */
    public static function current(): array
    {
        return self::forRoute(request()->route()?->getName());
    }
}
