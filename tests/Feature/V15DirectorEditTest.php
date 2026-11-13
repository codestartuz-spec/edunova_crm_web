<?php

namespace Tests\Feature;

use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V15DirectorEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_sadmin_can_edit_director_and_tabs_are_flat(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $director = $this->user(Role::Director);

        $this->actingAs($sadmin)->get(route('directors.edit', $director->id))->assertOk()->assertSee('Direktorni tahrirlash');
        $this->actingAs($sadmin)->put(route('directors.update', $director->id), [
            'name' => 'Yangi Nom', 'username' => $director->username, 'phone' => '+998 90 111 2233', 'email' => 'dir@example.com',
        ])->assertRedirect(route('directors.index'));

        $this->assertSame('Yangi Nom', $director->fresh()->name);

        $branch = $this->branch('Markaz');
        $this->actingAs($sadmin)->withSession([\App\Support\BranchContext::SESSION_KEY => $branch->id])->get(route('payroll.index', ['tab' => 'teachers']))->assertOk()
            ->assertSee("O'qituvchilar ish haqi", false)->assertSee('Hodimlar ish haqi');
    }
}
