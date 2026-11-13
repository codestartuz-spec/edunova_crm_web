<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v14: hodimlarga bildirishnoma — rol/filial tanlovi, qo'ng'iroqcha lentasi, o'qildi holati. */
class V14NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_permitted_users_can_send(): void
    {
        $branch = $this->branch();
        $admin = $this->user(Role::Admin, $branch, ['staff.view']);
        $director = $this->user(Role::Director);

        $payload = ['title' => 'Xabar', 'body' => 'Matn', 'roles' => ['manager'], 'all_branches' => 1];
        $this->actingAs($admin)->post(route('notifications.store'), $payload)->assertForbidden();
        $this->actingAs($director)->post(route('notifications.store'), $payload)->assertForbidden();

        $director->syncPermissions(['notifications.send']);
        $this->actingAs($director->fresh())->post(route('notifications.store'), $payload)->assertRedirect();
    }

    public function test_recipients_are_filtered_by_roles_and_branches_and_students_never_receive(): void
    {
        $a = $this->branch('A');
        $b = $this->branch('B');
        $sadmin = $this->user(Role::SAdmin);
        $mA = $this->user(Role::Manager, $a);
        $mB = $this->user(Role::Manager, $b);
        $tA = $this->user(Role::Teacher, $a);
        $dir = $this->user(Role::Director);
        $student = $this->student($a);

        $this->actingAs($sadmin)->post(route('notifications.store'), [
            'title' => 'Yig\'ilish', 'body' => 'Ertaga 10:00', 'roles' => ['manager', 'director'], 'branch_ids' => [$a->id], 'is_important' => 1,
        ])->assertRedirect();

        $n = Notification::firstOrFail();
        $got = NotificationRecipient::where('notification_id', $n->id)->pluck('user_id')->all();
        $this->assertEqualsCanonicalizing([$mA->id, $dir->id], $got);
        $this->assertTrue($n->is_important);
        $this->assertTrue(AuditLog::where('action', 'notification.sent')->exists());
        $this->assertNotContains($student->id, $got);
    }

    public function test_must_choose_a_role_and_a_branch_or_all(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $this->branch();

        $this->actingAs($sadmin)->post(route('notifications.store'), ['title' => 'x', 'body' => 'y', 'all_branches' => 1])->assertSessionHasErrors('roles');
        $this->actingAs($sadmin)->post(route('notifications.store'), ['title' => 'x', 'body' => 'y', 'roles' => ['manager']])->assertSessionHasErrors('branch_ids');
        $this->actingAs($sadmin)->post(route('notifications.store'), ['title' => 'x', 'body' => 'y', 'roles' => ['student'], 'all_branches' => 1])->assertSessionHasErrors('roles.0');
    }

    public function test_feed_and_read_state(): void
    {
        $branch = $this->branch();
        $sadmin = $this->user(Role::SAdmin);
        $manager = $this->user(Role::Manager, $branch);

        $this->actingAs($sadmin)->post(route('notifications.store'), ['title' => 'Salom', 'body' => 'Matn', 'roles' => ['manager'], 'all_branches' => 1, 'url' => '/groups']);
        $n = Notification::firstOrFail();

        $feed = $this->actingAs($manager)->getJson(route('notifications.feed'))->assertOk();
        $feed->assertJsonPath('count', 1)->assertJsonPath('items.0.title', 'Salom')->assertJsonPath('items.0.url', '/groups');

        $this->actingAs($manager)->postJson(route('notifications.read', $n->id))->assertOk()->assertJsonPath('count', 0);
        $this->assertNotNull(NotificationRecipient::where('user_id', $manager->id)->first()->read_at);

        // Boshqa hodimning bildirishnomasini o'qilgan qilib bo'lmaydi
        $other = $this->user(Role::Manager, $branch);
        $this->actingAs($other)->postJson(route('notifications.read', $n->id))->assertOk();
        $this->assertNull(NotificationRecipient::where('user_id', $other->id)->first());
    }

    public function test_student_cannot_use_feed(): void
    {
        $this->actingAs($this->student($this->branch()))->getJson(route('notifications.feed'))->assertForbidden();
    }

    public function test_url_must_be_internal_path_or_http(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $this->branch();
        $this->actingAs($sadmin)->post(route('notifications.store'), ['title' => 'x', 'body' => 'y', 'roles' => ['manager'], 'all_branches' => 1, 'url' => 'javascript:alert(1)'])->assertSessionHasErrors('url');
    }
}
