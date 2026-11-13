<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Certificate;
use App\Models\Group;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V15CertificateTest extends TestCase
{
    use RefreshDatabase;

    private function setupCert(): array
    {
        $branch = $this->branch('Markaz');
        $admin = $this->user(Role::Admin, $branch, ['certificates.issue', 'certificates.manage']);
        $student = $this->student($branch);
        $cert = app(CertificateService::class)->issue($student, null, $admin);

        return [$branch, $admin, $student, $cert];
    }

    public function test_issue_creates_unique_number_and_code(): void
    {
        [, $admin, $student, $c1] = $this->setupCert();
        $c2 = app(CertificateService::class)->issue($student, null, $admin);

        $this->assertMatchesRegularExpression('/^EDU-\d{4}-\d{6}$/', $c1->number);
        $this->assertSame(12, strlen($c1->code));
        $this->assertNotSame($c1->number, $c2->number);
        $this->assertNotSame($c1->code, $c2->code);
    }

    public function test_public_verify_shows_only_safe_fields(): void
    {
        [, , $student, $c] = $this->setupCert();
        $r = $this->get('/verify/'.$c->code)->assertOk()->assertSee('Sertifikat haqiqiy')->assertSee($c->number);
        if (filled($student->phone)) {
            $r->assertDontSee((string) $student->phone);
        }
        $this->get('/verify/ZZZZZZZZZZZZ')->assertNotFound();
    }

    public function test_revoked_shows_status_only_and_reissue_creates_new(): void
    {
        [, $admin, $student, $c] = $this->setupCert();
        $this->actingAs($admin)->post(route('certificates.revoke', $c), ['reason' => 'Xato berilgan'])->assertSessionHas('success');
        $this->assertTrue($c->fresh()->isRevoked());
        $r = $this->get('/verify/'.$c->code)->assertOk()->assertSee('Bekor qilingan');
        $this->assertStringNotContainsString($student->name, $r->getContent());

        $c2 = Certificate::create(['branch_id' => $c->branch_id, 'student_id' => $student->id, 'number' => 'EDU-9999-000001', 'code' => 'ABCDEFGH1234', 'student_name' => $student->name, 'course_name' => 'X', 'issued_on' => today(), 'status' => 'issued']);
        $new = app(CertificateService::class)->reissue($c2, $admin);
        $this->assertTrue($c2->fresh()->isRevoked());
        $this->assertSame($new->id, $c2->fresh()->replaced_by_id);
    }

    public function test_permissions_enforced(): void
    {
        [$branch, , , $c] = $this->setupCert();
        $nobody = $this->user(Role::Manager, $branch, []);
        $this->actingAs($nobody)->get(route('certificates.index'))->assertForbidden();
        $issuer = $this->user(Role::Manager, $branch, ['certificates.issue']);
        $this->actingAs($issuer)->get(route('certificates.index'))->assertOk();
        $this->actingAs($issuer)->post(route('certificates.revoke', $c), ['reason' => 'x'])->assertForbidden();
    }

    public function test_pdf_when_packages_installed(): void
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) || ! class_exists(\BaconQrCode\Writer::class)) {
            $this->markTestSkipped('composer require barryvdh/laravel-dompdf bacon/bacon-qr-code kerak');
        }
        [, $admin, , $c] = $this->setupCert();
        $res = $this->actingAs($admin)->get(route('certificates.pdf', $c))->assertOk();
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_editor_pages_render_and_issue_with_overrides(): void
    {
        [$branch, $admin, $student] = $this->setupCert();
        $this->actingAs($admin)->get(route('certificates.create-student', $student->id))->assertOk()->assertSee('Jonli ko', false);

        $this->actingAs($admin)->post(route('certificates.issue-student', $student->id), [
            'student_name' => 'Tahrirlangan Ism', 'course_name' => 'Koreys tili', 'level_name' => 'A1', 'title' => 'SERTIFIKAT',
            'body' => 'matn', 'signer_name' => 'Direktor Ismi', 'signer_title' => 'Direktor', 'issued_on' => today()->toDateString(),
        ])->assertRedirect(route('certificates.index'));

        $c = Certificate::where('student_name', 'Tahrirlangan Ism')->first();
        $this->assertNotNull($c);
        $this->assertSame('Direktor Ismi', $c->signer_name);
        $this->assertSame('Koreys tili', $c->course_name);
    }

    public function test_group_editor_renders(): void
    {
        $branch = $this->branch('Markaz');
        $admin = $this->user(Role::Admin, $branch, ['certificates.issue', 'groups.view']);
        $c = $this->catalog($branch);
        $group = Group::create(['branch_id' => $branch->id, 'course_id' => $c['course']->id, 'teacher_id' => $c['teacher']->id, 'room_id' => $c['room']->id,
            'lesson_time_id' => $c['time']->id, 'name' => 'a1', 'schedule' => 'odd', 'price' => 500000, 'lesson_count' => 4, 'starts_on' => '2026-09-21', 'ends_on' => '2026-10-21']);
        $this->actingAs($admin)->get(route('certificates.create-group', $group))->assertOk()->assertSee('Tasdiqlash va berish');
    }
}
