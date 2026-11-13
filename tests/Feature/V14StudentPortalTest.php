<?php

namespace Tests\Feature;

use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v14: o'quvchi veb kabineti va telefon bo'yicha kirish (akkaunt tanlash bilan). */
class V14StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_logs_in_by_username_and_lands_in_cabinet(): void
    {
        $student = $this->student($this->branch(), ['phone' => '+998 90 123 4567']);

        $this->post('/login', ['login' => $student->username, 'password' => 'parol12345'])->assertRedirect(route('dashboard'));
        $this->actingAs($student)->get('/')->assertRedirect(route('student.home'));

        foreach (['student.home', 'student.groups', 'student.payments', 'student.attendance', 'student.courses'] as $route) {
            $this->actingAs($student)->get(route($route))->assertOk();
        }
    }

    public function test_student_cannot_open_staff_pages_and_staff_cannot_open_cabinet(): void
    {
        $branch = $this->branch();
        $student = $this->student($branch);
        $admin = $this->user(Role::Admin, $branch, ['students.view']);

        $this->actingAs($student)->get('/students')->assertForbidden();
        $this->actingAs($admin)->get(route('student.home'))->assertForbidden();
    }

    public function test_login_by_phone_with_single_account(): void
    {
        $student = $this->student($this->branch(), ['phone' => '+998 90 123 4567']);

        $this->post('/login', ['login' => '90 123 45 67', 'password' => 'parol12345'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_login_by_phone_with_many_accounts_shows_choice_screen(): void
    {
        $branch = $this->branch();
        $a = $this->student($branch, ['phone' => '+998 90 123 4567', 'name' => 'BIRINCHI']);
        $b = $this->student($branch, ['phone2' => '+998 90 123 4567', 'name' => 'IKKINCHI']);

        $this->post('/login', ['login' => '998901234567', 'password' => 'parol12345'])
            ->assertOk()->assertSee('BIRINCHI')->assertSee('IKKINCHI');
        $this->assertGuest();

        $this->post('/login/choose', ['user_id' => $b->id])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($b);
    }

    public function test_wrong_password_by_phone_is_rejected(): void
    {
        $this->student($this->branch(), ['phone' => '+998 90 123 4567']);

        $this->post('/login', ['login' => '998901234567', 'password' => 'xato'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_choice_cannot_pick_account_outside_matched_list(): void
    {
        $branch = $this->branch();
        $this->student($branch, ['phone' => '+998 90 123 4567']);
        $this->student($branch, ['phone2' => '+998 90 123 4567']);
        $stranger = $this->student($branch, ['phone' => '+998 91 000 0000']);

        $this->post('/login', ['login' => '998901234567', 'password' => 'parol12345']);
        $this->post('/login/choose', ['user_id' => $stranger->id])->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
