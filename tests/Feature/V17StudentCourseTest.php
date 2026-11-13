<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Course;
use App\Models\CourseAudio;
use App\Models\CourseQuestion;
use App\Models\CourseVideo;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\TestAttempt;
use App\Support\MediaEmbed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V17StudentCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_embed_recognises_supported_sources(): void
    {
        $yt = MediaEmbed::resolve('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=90s');
        $this->assertSame('iframe', $yt['kind']);
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', $yt['src']);
        $this->assertStringContainsString('start=90', $yt['src']);

        $this->assertSame('iframe', MediaEmbed::resolve('https://youtu.be/dQw4w9WgXcQ')['kind']);
        $this->assertSame('https://player.vimeo.com/video/123456789?dnt=1', MediaEmbed::resolve('https://vimeo.com/123456789')['src']);
        $this->assertSame('https://drive.google.com/file/d/ABC_def-123/preview', MediaEmbed::resolve('https://drive.google.com/file/d/ABC_def-123/view?usp=sharing')['src']);

        $this->assertSame('video', MediaEmbed::resolve('https://example.com/dars1.mp4')['kind']);
        $this->assertSame('audio', MediaEmbed::resolve('https://example.com/tinglash.mp3')['kind']);

        $dropbox = MediaEmbed::resolve('https://www.dropbox.com/s/abc/tinglash.mp3?dl=0');
        $this->assertSame('audio', $dropbox['kind']);
        $this->assertStringContainsString('raw=1', $dropbox['src']);
    }

    public function test_media_embed_rejects_unsafe_and_unknown_links(): void
    {
        $this->assertSame('link', MediaEmbed::resolve('https://example.com/sahifa')['kind']);
        $this->assertSame('none', MediaEmbed::resolve('javascript:alert(1)')['kind']);
        $this->assertSame('none', MediaEmbed::resolve('ftp://example.com/a.mp4')['kind']);
        $this->assertSame('none', MediaEmbed::resolve('')['kind']);
        $this->assertFalse(MediaEmbed::playable(MediaEmbed::resolve('https://example.com/sahifa')));
    }

    private function setupStudent(): array
    {
        $branch = $this->branch('Markaz');
        $c = $this->catalog($branch);
        $course = $c['course'];
        $group = Group::create(['branch_id' => $branch->id, 'course_id' => $course->id, 'teacher_id' => $c['teacher']->id, 'room_id' => $c['room']->id,
            'lesson_time_id' => $c['time']->id, 'name' => 'a1', 'schedule' => 'odd', 'price' => 500000, 'lesson_count' => 4, 'starts_on' => '2026-09-21', 'ends_on' => '2099-12-31']);
        $student = $this->student($branch);
        GroupStudent::create(['branch_id' => $branch->id, 'group_id' => $group->id, 'student_id' => $student->id, 'is_active' => true]);

        CourseVideo::create(['branch_id' => $branch->id, 'course_id' => $course->id, 'number' => 1, 'title' => '1-dars', 'url' => 'https://youtu.be/dQw4w9WgXcQ']);
        CourseAudio::create(['branch_id' => $branch->id, 'course_id' => $course->id, 'number' => 1, 'title' => 'Tinglash', 'path' => 'https://example.com/a.mp3', 'is_external' => true]);
        foreach (['Bir', 'Ikki', 'Uch'] as $n => $word) {
            CourseQuestion::create(['branch_id' => $branch->id, 'course_id' => $course->id, 'question' => "Savol {$word}?", 'correct' => 'To\'g\'ri', 'wrong' => ['x', 'y', 'z']]);
        }

        return [$student, $course, $branch];
    }

    public function test_course_page_embeds_players_inside_the_site(): void
    {
        [$student, $course] = $this->setupStudent();

        $this->actingAs($student)->get(route('student.courses.show', $course))
            ->assertOk()
            ->assertSee('coursePlayer')
            ->assertSee('dQw4w9WgXcQ', false)
            ->assertSee('Tinglash');
    }

    public function test_test_is_taken_inside_the_platform_and_result_survives_refresh(): void
    {
        [$student, $course] = $this->setupStudent();
        $this->actingAs($student);

        $this->post(route('student.tests.start', $course))->assertRedirect();
        $attempt = TestAttempt::firstOrFail();

        $page = $this->get(route('student.tests.show', $attempt->id))->assertOk()->assertSee('quiz');
        $this->assertStringNotContainsString('answer_key', $page->getContent());

        $answers = [];
        foreach ($attempt->questions as $i => $q) {
            $answers[$q['id']] = $attempt->answer_key[$i];
        }

        $this->post(route('student.tests.submit', $attempt->id), ['answers' => $answers])->assertRedirect(route('student.tests.result', $attempt->id));

        $fresh = $attempt->fresh();
        $this->assertSame(100, $fresh->score);
        $this->assertNotNull($fresh->answers);

        $this->get(route('student.tests.result', $attempt->id))->assertOk()->assertSee('Javoblar tahlili')->assertSee('100%');

        // Qayta yuborish yoki sahifani yangilash xato bermaydi, natijaga olib boradi
        $this->post(route('student.tests.submit', $attempt->id), ['answers' => []])->assertRedirect(route('student.tests.result', $attempt->id));
        $this->assertSame(100, $attempt->fresh()->score);
        $this->get(route('student.tests.show', $attempt->id))->assertRedirect(route('student.tests.result', $attempt->id));
    }

    public function test_other_student_cannot_open_foreign_result(): void
    {
        [$student, $course, $branch] = $this->setupStudent();
        $this->actingAs($student)->post(route('student.tests.start', $course));
        $attempt = TestAttempt::firstOrFail();
        $this->post(route('student.tests.submit', $attempt->id), ['answers' => []]);

        $other = $this->student($branch);
        $this->actingAs($other)->get(route('student.tests.result', $attempt->id))->assertNotFound();
    }
}
