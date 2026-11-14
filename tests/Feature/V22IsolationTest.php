<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v22: filiallararo izolyatsiya (ma'lumot boshqa filialga oqib chiqmasligi) uchun xavfsizlik to'ri. */
class V22IsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_admin_cannot_open_other_branch_student_or_group(): void
    {
        $a = $this->branch('A filial');
        $b = $this->branch('B filial');
        $adminA = $this->user(Role::Admin, $a, ['students.view', 'groups.view']);
        $studentB = $this->user(Role::Student, $b);

        $this->actingAs($adminA)->get(route('students.show', $studentB))->assertNotFound();
        $this->actingAs($adminA)->get(route('students.edit', $studentB))->assertNotFound();
    }

    public function test_restricted_director_cannot_notify_all_branches(): void
    {
        $a = $this->branch('A');
        $b = $this->branch('B');
        $director = $this->user(Role::Director, null, ['notifications.send']);
        \Illuminate\Support\Facades\DB::table('users')->where('id', $director->id)->update(['all_branches' => false]);
        $director = $director->fresh();
        \Illuminate\Support\Facades\DB::table('director_branches')->insert(['user_id' => $director->id, 'branch_id' => $a->id]);
        $inA = $this->user(Role::Teacher, $a);
        $inB = $this->user(Role::Teacher, $b);

        $this->actingAs($director)->post(route('notifications.store'), [
            'title' => 'Test', 'body' => 'Matn', 'roles' => ['teacher'], 'all_branches' => 1,
        ]);

        // «Barcha filiallar» e'tiborga olinmaydi: hech bir filial tanlanmagani uchun xato, hech kimga bormaydi
        $this->assertSame(0, NotificationRecipient::count());

        $this->actingAs($director)->post(route('notifications.store'), [
            'title' => 'Test', 'body' => 'Matn', 'roles' => ['teacher'], 'branch_ids' => [$a->id],
        ]);
        $ids = NotificationRecipient::pluck('user_id')->all();
        $this->assertContains($inA->id, $ids);
        $this->assertNotContains($inB->id, $ids);
        $this->assertSame(1, Notification::count());
    }
}
