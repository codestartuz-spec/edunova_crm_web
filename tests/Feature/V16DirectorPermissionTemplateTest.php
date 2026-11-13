<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Services\PermissionAssignmentService;
use App\Support\DirectorPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V16DirectorPermissionTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_template_applies_same_permissions_to_all_directors(): void
    {
        $sa = $this->user(Role::SAdmin);
        $d1 = $this->user(Role::Director, permissions: ['branches.manage']);
        $d2 = $this->user(Role::Director, permissions: ['system.view']);

        $this->actingAs($sa)->put(route('directors.permissions.update'), ['permissions' => ['students.view', 'branches.manage']])->assertRedirect();

        $this->assertEqualsCanonicalizing(['students.view', 'branches.manage'], $d1->fresh()->permissionKeys());
        $this->assertEqualsCanonicalizing(['students.view', 'branches.manage'], $d2->fresh()->permissionKeys());
    }

    public function test_new_director_gets_the_template(): void
    {
        $sa = $this->user(Role::SAdmin);
        DirectorPermissions::save(['students.view']);

        $this->actingAs($sa)->post('/directors', [
            'name' => 'Yangi Direktor', 'username' => 'dir.yangi', 'phone' => '+998 90 111 2233',
            'password' => 'parol12345', 'password_confirmation' => 'parol12345',
        ])->assertRedirect(route('directors.index'));

        $this->assertSame(['students.view'], User::where('username', 'dir.yangi')->firstOrFail()->permissionKeys());
    }

    public function test_only_sadmin_opens_template_page(): void
    {
        $this->actingAs($this->user(Role::Director, permissions: ['branches.manage']))->get(route('directors.permissions.edit'))->assertForbidden();
        $this->actingAs($this->user(Role::SAdmin))->get(route('directors.permissions.edit'))->assertOk();
    }

    public function test_director_grants_staff_only_permissions_he_has(): void
    {
        $director = $this->user(Role::Director, permissions: ['students.view', 'permissions.assign']);
        $manager = $this->user(Role::Manager);

        app(PermissionAssignmentService::class)->update($director, $manager, ['students.view', 'payments.create']);

        $this->assertSame(['students.view'], $manager->fresh()->permissionKeys());
    }
}
