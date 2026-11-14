<?php

namespace Tests\Feature;

use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * v22: "tutun testi" — parametrsiz barcha GET sahifalar sAdmin, direktor va admin uchun ochilishi kerak (500 bo'lmasin).
 * Deploydan keyin chiqadigan "Server Error" (masalan, Blade sintaksis xatosi) ni shu yerda ushlaymiz.
 */
class SmokeRoutesTest extends TestCase
{
    use RefreshDatabase;

    /** Tashqi xizmatga chiqadigan yoki fayl yuklab beradigan sahifalar */
    private const SKIP = ['up', 'telegram-bot', 'system-status', 'backups', 'legacy-import', 'monitor', 'logout', 'api/', 'login', 'forgot-password', 'verify', 'apply', 'manifest', 'sw.js', 'offline'];

    private function urls(): array
    {
        $urls = [];
        foreach (Route::getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true) || str_contains($route->uri(), '{')) {
                continue;
            }
            foreach (self::SKIP as $skip) {
                if (str_starts_with($route->uri(), $skip) || str_contains($route->uri(), '/export') || str_contains($route->uri(), 'download') || str_contains($route->uri(), 'sample')) {
                    continue 2;
                }
            }
            $urls[] = '/'.ltrim($route->uri(), '/');
        }

        return array_values(array_unique($urls));
    }

    public function test_every_parameterless_page_opens_without_server_error(): void
    {
        $branch = $this->branch('Tutun filiali');
        $users = [
            'sadmin' => $this->user(Role::SAdmin),
            'director' => $this->user(Role::Director),
            'admin' => $this->user(Role::Admin, $branch),
        ];

        $failures = [];
        foreach ($users as $label => $user) {
            foreach ($this->urls() as $url) {
                try {
                    $status = $this->actingAs($user)->withSession(['current_branch_id' => $branch->id])->get($url)->getStatusCode();
                    if ($status >= 500) {
                        $failures[] = "[$label] $url -> $status";
                    }
                } catch (\Throwable $e) {
                    $failures[] = "[$label] $url -> ".get_class($e).': '.mb_substr($e->getMessage(), 0, 160);
                }
            }
        }

        $this->assertSame([], $failures, "Quyidagi sahifalar xato berdi:\n".implode("\n", $failures));
    }
}
