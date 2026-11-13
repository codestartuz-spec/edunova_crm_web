<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\Role;
use App\Services\SessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class V15SessionsTest extends TestCase
{
    use RefreshDatabase;

    private function row(string $id, int $uid, string $ua = 'Mozilla/5.0 (Windows NT 10.0) Chrome/120 Safari/537'): void
    {
        DB::table('sessions')->insert(['id' => $id, 'user_id' => $uid, 'ip_address' => '1.2.3.4', 'user_agent' => $ua, 'payload' => '', 'last_activity' => time()]);
    }

    public function test_parse_and_list_and_revoke(): void
    {
        $u = $this->user(Role::Admin, $this->branch('F'));
        $o = $this->user(Role::Admin, $this->branch('G'));
        $this->row('a', $u->id);
        $this->row('b', $u->id, 'Mozilla/5.0 (iPhone; CPU iPhone OS 17) Mobile Safari');
        $this->row('c', $o->id);
        $svc = app(SessionService::class);

        $list = $svc->list($u, 'a');
        $this->assertCount(2, $list);
        $this->assertSame('Windows', collect($list)->firstWhere('id', 'a')['os']);
        $this->assertTrue(collect($list)->firstWhere('id', 'a')['current']);
        $this->assertSame('Telefon', collect($list)->firstWhere('id', 'b')['device']);

        $this->assertSame(1, $svc->revokeOthers($u, 'a'));
        $this->assertSame(1, DB::table('sessions')->where('user_id', $u->id)->count());
        $this->assertSame(1, DB::table('sessions')->where('user_id', $o->id)->count());
        $this->assertSame(1, $svc->revokeAll($u));
    }
}
