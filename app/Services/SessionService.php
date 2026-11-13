<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** v15: faol qurilmalar (sessions jadvali, SESSION_DRIVER=database kerak). */
class SessionService
{
    public function available(): bool
    {
        return config('session.driver') === 'database' && Schema::hasTable(config('session.table', 'sessions'));
    }

    private function table()
    {
        return DB::table(config('session.table', 'sessions'));
    }

    /** @return list<array{id:string,device:string,browser:string,os:string,ip:?string,last_active:\Carbon\Carbon,current:bool}> */
    public function list(User $user, ?string $currentId): array
    {
        return $this->table()->where('user_id', $user->id)->orderByDesc('last_activity')->get()
            ->map(function ($s) use ($currentId) {
                $p = $this->parse((string) $s->user_agent);

                return [
                    'id' => $s->id, 'device' => $p['device'], 'browser' => $p['browser'], 'os' => $p['os'],
                    'ip' => $s->ip_address, 'last_active' => \Carbon\Carbon::createFromTimestamp($s->last_activity),
                    'current' => $s->id === $currentId,
                ];
            })->all();
    }

    public function revoke(User $user, string $sessionId): int
    {
        return $this->table()->where('user_id', $user->id)->where('id', $sessionId)->delete();
    }

    public function revokeOthers(User $user, ?string $currentId): int
    {
        return $this->table()->where('user_id', $user->id)->when($currentId, fn ($q) => $q->where('id', '!=', $currentId))->delete();
    }

    public function revokeAll(User $user): int
    {
        return $this->table()->where('user_id', $user->id)->delete();
    }

    /** @return array{device:string,browser:string,os:string} */
    public function parse(string $ua): array
    {
        $os = match (true) {
            (bool) preg_match('/Windows/i', $ua) => 'Windows',
            (bool) preg_match('/Android/i', $ua) => 'Android',
            (bool) preg_match('/iPhone|iPad|iOS/i', $ua) => 'iOS',
            (bool) preg_match('/Mac OS X|Macintosh/i', $ua) => 'macOS',
            (bool) preg_match('/Linux/i', $ua) => 'Linux',
            default => 'Noma\'lum',
        };
        $browser = match (true) {
            (bool) preg_match('/Edg\//i', $ua) => 'Edge',
            (bool) preg_match('/OPR\/|Opera/i', $ua) => 'Opera',
            (bool) preg_match('/Firefox/i', $ua) => 'Firefox',
            (bool) preg_match('/Chrome|CriOS/i', $ua) => 'Chrome',
            (bool) preg_match('/Safari/i', $ua) => 'Safari',
            default => 'Brauzer',
        };
        $device = preg_match('/Mobile|Android|iPhone/i', $ua) ? 'Telefon' : (preg_match('/iPad|Tablet/i', $ua) ? 'Planshet' : 'Kompyuter');

        return ['device' => $device, 'browser' => $browser, 'os' => $os];
    }
}
