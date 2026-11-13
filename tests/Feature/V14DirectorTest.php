<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v14: Direktor roli - sAdmin boshqaradi, ruxsatlar sAdmin tomonidan beriladi, barcha filiallar ko'rinadi. */
class V14DirectorTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $over = []): array
    {
        return ['name' => 'Direktor Bir', 'username' => 'dir.bir', 'phone' => '+998 90 333 4455', 'password' => 'parol12345', 'password_confirmation' => 'parol12345', ...$over];
    }

    public function test_only_sadmin_manages_directors(): void
    {
        $admin = $this->user(Role::Admin, $this->branch(), ['staff.manage']);
        $director = $this->user(Role::Director);

        $this->actingAs($admin)->get('/directors')->assertForbidden();
        $this->actingAs($director)->get('/directors')->assertForbidden();
        $this->actingAs($admin)->post('/directors', $this->payload())->assertForbidden();
    }

    public function test_sadmin_creates_director_and_is_sent_to_permissions_page(): void
    {
        $sa = $this->user(Role::SAdmin);

        $this->actingAs($sa)->post('/directors', $this->payload())->assertRedirect();

        $new = User::where('username', 'dir.bir')->firstOrFail();
        $this->assertSame(Role::Director, $new->role);
        $this->assertNull($new->branch_id);
        $this->assertTrue($new->isGlobal());
        $this->assertFalse($new->isSuperAdmin());
        $this->assertSame([], $new->permissionKeys());
    }

    public function test_director_without_permissions_cannot_use_protected_sections(): void
    {
        $director = $this->user(Role::Director);

        $this->actingAs($director)->get('/branches')->assertForbidden();
        $this->actingAs($director)->get('/system-status')->assertForbidden();
        $this->actingAs($director)->get('/notifications')->assertForbidden();
    }

    public function test_sadmin_grants_permissions_and_director_works_within_them(): void
    {
        $sa = $this->user(Role::SAdmin);
        $director = $this->user(Role::Director);

        $this->actingAs($sa)->put(route('directors.permissions.update'), ['permissions' => ['branches.manage']])->assertRedirect();
        $this->assertSame(['branches.manage'], $director->fresh()->permissionKeys());

        $this->actingAs($director->fresh())->get('/branches')->assertOk();
        $this->actingAs($director->fresh())->get('/system-status')->assertForbidden();
    }

    public function test_director_cannot_get_sadmin_only_actions(): void
    {
        $director = $this->user(Role::Director, permissions: ['branches.manage', 'branches.delete', 'system.view']);

        $this->assertFalse($director->isSuperAdmin());
        $this->actingAs($director)->get(route('directors.index'))->assertForbidden();
        $this->actingAs($director)->post(route('system-status.backup'))->assertForbidden();
    }

    public function test_block_and_remove_director(): void
    {
        $sa = $this->user(Role::SAdmin);
        $director = $this->user(Role::Director, permissions: ['branches.manage']);

        $this->actingAs($sa)->post(route('directors.toggle', $director->id))->assertSessionHas('success');
        $this->assertSame(UserStatus::Blocked, $director->fresh()->status);

        $this->actingAs($sa)->post(route('directors.toggle', $director->id))->assertSessionHas('success');
        $this->assertTrue($director->fresh()->isActive());

        $this->actingAs($sa)->delete(route('directors.destroy', $director->id))->assertSessionHas('success');
        $this->assertNotNull($director->fresh()->archived_at);
        $this->assertSame([], $director->fresh()->permissionKeys());
    }

    public function test_cannot_toggle_non_director_through_this_section(): void
    {
        $sa = $this->user(Role::SAdmin);
        $admin = $this->user(Role::Admin, $this->branch());

        $this->actingAs($sa)->post(route('directors.toggle', $admin->id))->assertNotFound();
        $this->actingAs($sa)->delete(route('directors.destroy', $admin->id))->assertNotFound();
    }
}
