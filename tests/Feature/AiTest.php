<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Course;
use App\Models\CourseQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** v14: AI yordamchi (suhbat/tahlil) olib tashlandi; faqat kurs test savollarini AI bilan yaratish qoldi. */
class AiTest extends TestCase
{
    use RefreshDatabase;

    private $branch;
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-09-21 10:00:00');
        config(['services.openai.key' => 'sk-test', 'services.openai.model' => 'gpt-test', 'services.openai.daily_limit' => 5]);

        $this->branch = $this->branch('Toshkent');
        $this->admin = $this->user(Role::Admin, $this->branch, ['statistics.view', 'payments.view', 'finance.view', 'groups.view', 'leads.view', 'attendance.stats', 'courses.manage']);
    }

    public function test_ai_question_generation_review_and_save(): void
    {
        $course = Course::create(['branch_id' => $this->branch->id, 'name' => 'TOPIK 1']);
        $json = json_encode(['questions' => [
            ['question' => 'Salom koreyscha?', 'correct' => '안녕하세요', 'wrong' => ['감사합니다', '죄송합니다', '안녕히 가세요']],
            ['question' => 'Yaroqsiz (2 ta variant)', 'correct' => 'A', 'wrong' => ['B', 'C']],
            ['question' => 'Takror javob', 'correct' => 'A', 'wrong' => ['A', 'B', 'C']],
        ]], JSON_UNESCAPED_UNICODE);
        Http::fake(['api.openai.com/*' => Http::response(['choices' => [['message' => ['role' => 'assistant', 'content' => $json]]]])]);

        $response = $this->actingAs($this->admin)->post("/courses/{$course->id}/questions/ai", ['topic' => 'Salomlashish', 'count' => 3, 'level' => "boshlang'ich"]);
        $response->assertRedirect()->assertSessionHas('ai_questions');
        $questions = session('ai_questions');
        $this->assertCount(1, $questions);                            // faqat yaroqli savol qoldi
        $this->assertSame(0, CourseQuestion::count());                 // hali saqlanmagan

        Http::assertSent(fn (Request $r) => ($r->data()['response_format']['type'] ?? null) === 'json_object');

        $this->post("/courses/{$course->id}/questions/ai-store", ['questions' => $questions, 'selected' => [0]])->assertRedirect();
        $this->assertSame(1, CourseQuestion::count());
        $this->assertSame('안녕하세요', CourseQuestion::first()->correct);

        $this->post("/courses/{$course->id}/questions/ai-store", ['questions' => $questions])->assertSessionHasErrors('selected');
        $this->post("/courses/{$course->id}/questions/ai", ['topic' => '', 'count' => 3, 'level' => "o'rta"])->assertSessionHasErrors('topic');

        // Ruxsatsiz
        $manager = $this->user(Role::Manager, $this->branch, ['courses.view']);
        $this->actingAs($manager)->post("/courses/{$course->id}/questions/ai", ['topic' => 'x', 'count' => 3, 'level' => "o'rta"])->assertForbidden();
    }
}
