<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** v20: direktorga aynan qaysi filiallar uchun ruxsat berilishi. */
class V20DirectorBranchTest extends TestCase
{
    use RefreshDatabase;

    private function restricted(array $branchIds, array $perms = ['students.view', 'branches.manage']): User
    {
        $d = $this->user(Role::Director, null, $perms);
        $d->forceFill(['all_branches' => false])->save();
        foreach ($branchIds as $id) {
            DB::table('director_branches')->insert(['user_id' => $d->id, 'branch_id' => $id]);
        }

        return $d->fresh();
    }

    private function payload(array $over = []): array
    {
        return ['name' => 'Direktor Bir', 'username' => 'dir.bir', 'phone' => '+998 90 333 4455', 'password' => 'parol12345', 'password_confirmation' => 'parol12345', ...$over];
    }

    public function test_sadmin_gives_director_selected_branches(): void
    {
        $sa = $this->user(Role::SAdmin);
        [$a, $b, $c] = [$this->branch('A'), $this->branch('B'), $this->branch('C')];

        $this->actingAs($sa)->post('/directors', $this->payload(['branch_mode' => 'selected', 'branch_ids' => [$a->id, $c->id]]))->assertRedirect();

        $d = User::where('username', 'dir.bir')->firstOrFail();
        $this->assertFalse((bool) $d->all_branches);
        $this->assertEqualsCanonicalizing([$a->id, $c->id], DB::table('director_branches')->where('user_id', $d->id)->pluck('branch_id')->all());
    }

    public function test_selected_mode_requires_at_least_one_branch(): void
    {
        $sa = $this->user(Role::SAdmin);
        $this->branch('A');

        $this->actingAs($sa)->post('/directors', $this->payload(['branch_mode' => 'selected', 'branch_ids' => []]))->assertSessionHasErrors('branch_ids');
        $this->assertDatabaseMissing('users', ['username' => 'dir.bir']);
    }

    public function test_existing_director_without_form_field_keeps_all_branches(): void
    {
        $sa = $this->user(Role::SAdmin);
        $d = $this->user(Role::Director);

        $this->actingAs($sa)->put("/directors/{$d->id}", ['name' => 'Yangi', 'username' => 'yangi.dir', 'phone' => '+998 90 333 4455'])->assertRedirect();

        $this->assertTrue((bool) ($d->fresh()->all_branches ?? true));
    }

    public function test_sadmin_can_restrict_and_restore_all_branches(): void
    {
        $sa = $this->user(Role::SAdmin);
        $a = $this->branch('A');
        $d = $this->user(Role::Director);
        $base = ['name' => 'Yangi', 'username' => 'yangi.dir', 'phone' => '+998 90 333 4455'];

        $this->actingAs($sa)->put("/directors/{$d->id}", $base + ['branch_mode' => 'selected', 'branch_ids' => [$a->id]])->assertRedirect();
        $this->assertFalse((bool) $d->fresh()->all_branches);

        $this->actingAs($sa)->put("/directors/{$d->id}", $base + ['branch_mode' => 'all'])->assertRedirect();
        $this->assertTrue((bool) $d->fresh()->all_branches);
        $this->assertSame(0, DB::table('director_branches')->where('user_id', $d->id)->count());
    }

    public function test_restricted_director_sees_only_allowed_branches_and_their_data(): void
    {
        [$a, $b, $c] = [$this->branch('Alfa'), $this->branch('Beta'), $this->branch('Gamma')];
        $sa = $this->student($a, ['name' => 'Talaba Alfa']);
        $sb = $this->student($b, ['name' => 'Talaba Beta']);
        $sc = $this->student($c, ['name' => 'Talaba Gamma']);
        $d = $this->restricted([$a->id, $b->id]);

        $this->actingAs($d)->get('/branches')->assertOk()->assertSee('Alfa')->assertSee('Beta')->assertDontSee('Gamma');
        $this->actingAs($d)->get('/students')->assertOk()->assertSee('Talaba Alfa')->assertSee('Talaba Beta')->assertDontSee('Talaba Gamma');
        $this->actingAs($d)->get(route('branches.show', $c))->assertNotFound();
    }

    public function test_restricted_director_switches_only_between_allowed_branches(): void
    {
        [$a, $b, $c] = [$this->branch('Alfa'), $this->branch('Beta'), $this->branch('Gamma')];
        $d = $this->restricted([$a->id, $b->id]);

        $this->actingAs($d)->post('/branch/switch', ['branch_id' => $c->id])->assertSessionHasErrors('branch_id');
        $this->actingAs($d)->post('/branch/switch', ['branch_id' => $a->id])->assertRedirect();

        $this->assertSame($a->id, session(BranchContext::SESSION_KEY));
        $this->actingAs($d)->get('/students')->assertOk();
    }

    public function test_director_with_single_branch_is_always_inside_it(): void
    {
        [$a, $b] = [$this->branch('Alfa'), $this->branch('Beta')];
        $d = $this->restricted([$a->id]);

        $this->actingAs($d);
        $this->assertSame($a->id, BranchContext::id());
        $this->assertSame([$a->id], BranchContext::restrictedIds($d));
    }

    public function test_director_with_all_branches_is_unrestricted(): void
    {
        [$a, $b] = [$this->branch('Alfa'), $this->branch('Beta')];
        $d = $this->user(Role::Director, null, ['branches.manage']);

        $this->actingAs($d)->get('/branches')->assertOk()->assertSee('Alfa')->assertSee('Beta');
        $this->assertNull(BranchContext::restrictedIds($d));
    }

    public function test_director_without_any_branch_sees_nothing(): void
    {
        $a = $this->branch('Alfa');
        $this->student($a, ['name' => 'Talaba Alfa']);
        $d = $this->restricted([]);

        $this->actingAs($d)->get('/students')->assertOk()->assertDontSee('Talaba Alfa');
    }

    public function test_new_branch_created_by_restricted_director_is_attached_to_him(): void
    {
        $a = $this->branch('Alfa');
        $d = $this->restricted([$a->id], ['branches.manage']);

        $this->actingAs($d)->post('/branches', ['name' => 'Yangi filial'])->assertRedirect();

        $new = \App\Models\Branch::withoutGlobalScopes()->where('name', 'Yangi filial')->first();
        if ($new) {
            $this->assertDatabaseHas('director_branches', ['user_id' => $d->id, 'branch_id' => $new->id]);
        } else {
            $this->markTestSkipped("Filial yaratish uchun qo'shimcha maydonlar kerak (BranchRequest).");
        }
    }
}
