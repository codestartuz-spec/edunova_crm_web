<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\StudentNote;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Tests\TestCase;

/** v21: xavfsizlik va sifat tuzatishlari. */
class V21HardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_import_is_hidden_unless_feature_enabled(): void
    {
        $sadmin = $this->user(Role::SAdmin);

        config(['features.legacy_import' => false]);
        $this->actingAs($sadmin)->get(route('legacy.index'))->assertNotFound();

        config(['features.legacy_import' => true]);
        $this->actingAs($sadmin)->get(route('legacy.index'))->assertOk();
    }

    public function test_default_password_rule_requires_10_chars_with_letters_and_digits(): void
    {
        $rule = fn (string $p) => Validator::make(['password' => $p], ['password' => [Password::defaults()]])->passes();

        $this->assertFalse($rule('qisqa12'));          // 7 belgi
        $this->assertFalse($rule('faqat-harflar-bor')); // raqam yo'q
        $this->assertFalse($rule('1234567890123'));     // harf yo'q
        $this->assertTrue($rule('uzun-parol-12'));
    }

    public function test_login_is_blocked_after_too_many_failures_from_one_ip(): void
    {
        $user = $this->user(Role::Admin);

        for ($i = 0; $i < 30; $i++) {
            RateLimiter::hit('login-ip|127.0.0.1', 900);
        }

        // To'g'ri parol bilan ham IP bloklangan
        $this->post('/login', ['login' => $user->username, 'password' => 'parol12345'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_login_is_blocked_after_too_many_failures_for_one_account(): void
    {
        $user = $this->user(Role::Admin);

        for ($i = 0; $i < 15; $i++) {
            RateLimiter::hit('login-user|'.strtolower($user->username), 900);
        }

        $this->post('/login', ['login' => $user->username, 'password' => 'parol12345'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_successful_login_clears_the_account_counter(): void
    {
        $user = $this->user(Role::Admin);
        $key = 'login-user|'.strtolower($user->username);

        RateLimiter::hit($key, 900);
        $this->post('/login', ['login' => $user->username, 'password' => 'parol12345'])->assertRedirect(route('dashboard'));

        $this->assertSame(0, RateLimiter::attempts($key));
    }

    public function test_security_headers_include_enforced_csp_subset_and_report_only_policy(): void
    {
        $r = $this->get('/login')->assertOk();

        $csp = $r->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
        $this->assertNotNull($r->headers->get('Content-Security-Policy-Report-Only'));
        $this->assertSame('DENY', $r->headers->get('X-Frame-Options'));
    }

    public function test_backup_zip_password_prefers_env_value(): void
    {
        config(['features.backup_zip_password' => 'env-parol-123456789012345']);

        $this->assertSame('env-parol-123456789012345', app(BackupService::class)->zipPassword());
    }

    public function test_deleted_student_note_is_soft_deleted(): void
    {
        $branch = $this->branch('Toshkent');
        $admin = $this->user(Role::Admin, $branch);
        $student = $this->student($branch);

        $note = StudentNote::create(['branch_id' => $branch->id, 'student_id' => $student->id, 'user_id' => $admin->id, 'body' => 'Qo\'ng\'iroq qilish']);
        $note->delete();

        $this->assertSoftDeleted('student_notes', ['id' => $note->id]);
        $this->assertSame(1, StudentNote::withoutGlobalScopes()->withTrashed()->count());
    }
}
