<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\TutorialVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v24: «Video darslar» — ko'rish hamma uchun, boshqarish faqat sAdmin uchun. */
class TutorialVideoTest extends TestCase
{
    use RefreshDatabase;

    private function make(array $a = []): TutorialVideo
    {
        return TutorialVideo::create($a + ['title' => 'Kirish', 'url' => 'https://youtu.be/dQw4w9WgXcQ', 'sort_order' => 1, 'is_active' => true]);
    }

    public function test_everyone_watches_active_videos_and_hidden_one_is_sadmin_only(): void
    {
        $b = $this->branch();
        $this->make(['title' => 'Ochiq dars']);
        $hidden = $this->make(['title' => 'Yashirin dars', 'is_active' => false, 'sort_order' => 2]);

        foreach ([Role::SAdmin, Role::Director, Role::Admin, Role::Manager, Role::Teacher, Role::Operator, Role::Student] as $role) {
            $this->actingAs($this->user($role, $b))->get('/tutorials')->assertOk()->assertSee('Ochiq dars')->assertDontSee('Yashirin dars');
        }
        $this->actingAs($this->user(Role::Manager, $b))->get("/tutorials/{$hidden->id}")->assertNotFound();
        $this->actingAs($this->user(Role::SAdmin))->get("/tutorials/{$hidden->id}")->assertOk();
    }

    public function test_without_active_videos_section_is_closed_for_everyone_but_sadmin(): void
    {
        $b = $this->branch();
        $this->make(['title' => 'Faqat yashirin', 'is_active' => false]);

        foreach ([Role::Director, Role::Admin, Role::Manager, Role::Teacher, Role::Operator, Role::Student] as $role) {
            $this->actingAs($this->user($role, $b))->get('/tutorials')->assertNotFound();
        }
        $this->actingAs($this->user(Role::SAdmin))->get('/tutorials')->assertOk();
    }

    public function test_player_page_uses_privacy_embed(): void
    {
        $v = $this->make();
        $this->actingAs($this->user(Role::Student, $this->branch()))->get("/tutorials/{$v->id}")
            ->assertOk()->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)->assertSee('autoplay=1', false);
    }

    public function test_only_sadmin_can_manage(): void
    {
        $b = $this->branch();
        $v = $this->make();
        foreach ([Role::Director, Role::Admin, Role::Manager, Role::Teacher, Role::Operator, Role::Student] as $role) {
            $this->actingAs($this->user($role, $b));
            $this->get('/settings/tutorials')->assertForbidden();
            $this->post('/settings/tutorials', ['title' => 'x', 'url' => 'https://youtu.be/abcdefghijk'])->assertForbidden();
            $this->put("/settings/tutorials/{$v->id}", ['title' => 'x', 'url' => 'https://youtu.be/abcdefghijk'])->assertForbidden();
            $this->delete("/settings/tutorials/{$v->id}")->assertForbidden();
        }
        $this->assertSame('Kirish', $v->fresh()->title);
    }

    public function test_sadmin_crud_validation_toggle_and_order(): void
    {
        $this->actingAs($this->user(Role::SAdmin));
        $this->get('/settings/tutorials')->assertOk();

        $this->post('/settings/tutorials', ['title' => 'Bir', 'url' => 'https://youtu.be/abcdefghijk', 'category' => 'Boshlash', 'duration' => '5:30', 'is_active' => 1])->assertSessionHasNoErrors();
        $this->post('/settings/tutorials', ['title' => 'Ikki', 'url' => 'https://example.com/video.mp4', 'is_active' => 1])->assertSessionHasNoErrors();
        $this->post('/settings/tutorials', ['title' => 'Xato', 'url' => 'https://example.com/sahifa'])->assertSessionHasErrors('url');
        $this->post('/settings/tutorials', ['title' => 'Xato2', 'url' => 'https://youtu.be/abcdefghijk', 'duration' => 'abc'])->assertSessionHasErrors('duration');
        $this->assertSame(2, TutorialVideo::count());

        [$a, $b] = [TutorialVideo::where('title', 'Bir')->first(), TutorialVideo::where('title', 'Ikki')->first()];
        $this->assertLessThan($b->sort_order, $a->sort_order);

        $this->post("/settings/tutorials/{$b->id}/move", ['dir' => 'up'])->assertRedirect();
        $this->assertSame(['Ikki', 'Bir'], TutorialVideo::ordered()->pluck('title')->all());

        $this->post("/settings/tutorials/{$a->id}/toggle")->assertRedirect();
        $this->assertFalse($a->fresh()->is_active);

        $this->put("/settings/tutorials/{$a->id}", ['title' => 'Bir (yangi)', 'url' => 'https://youtu.be/abcdefghijk'])->assertSessionHasNoErrors();
        $this->assertSame('Bir (yangi)', $a->fresh()->title);

        $this->delete("/settings/tutorials/{$b->id}")->assertRedirect();
        $this->assertSame(1, TutorialVideo::count());
    }

    public function test_menu_link_appears_only_when_active_video_exists_and_settings_entry_only_for_sadmin(): void
    {
        $b = $this->branch();
        $dir = fn () => $this->actingAs($this->user(Role::Director))->withSession(['current_branch_id' => $b->id]);

        // video yo'q: sAdmin'dan boshqaga menyuda yo'q
        $this->actingAs($this->user(Role::Student, $b))->get('/student')->assertOk()->assertDontSee('Video darslar');
        $dir()->get('/')->assertOk()->assertDontSee('Video darslar');
        $this->actingAs($this->user(Role::SAdmin))->withSession(['current_branch_id' => $b->id])->get('/')->assertOk()->assertSee('Video darslar');

        // faol video qo'shilgach hammaga ochiladi
        $this->make();
        $this->actingAs($this->user(Role::Student, $b))->get('/student')->assertOk()->assertSee('Video darslar');
        $dir()->get('/')->assertOk()->assertSee('Video darslar');

        $this->actingAs($this->user(Role::SAdmin))->withSession(['current_branch_id' => $b->id])->get('/settings')->assertOk()->assertSee('settings/tutorials', false);
        $dir()->get('/settings')->assertDontSee('settings/tutorials', false);
    }
}
