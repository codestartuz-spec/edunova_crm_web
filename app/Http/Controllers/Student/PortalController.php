<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Book;
use App\Models\Course;
use App\Models\Group;
use App\Models\TestAttempt;
use App\Services\AttendanceService;
use App\Services\TestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * v14: O'QUVCHI KABINETI (veb). Mobil ilova/API olib tashlangani uchun o'quvchi ham saytga kiradi
 * va faqat o'z ma'lumotini ko'radi: balans va to'lovlar, guruhlar, davomat, kurs materiallari va testlar.
 * Barcha yo'llar `role:student` bilan himoyalangan; har so'rov faqat joriy foydalanuvchi ma'lumotini qaytaradi.
 */
class PortalController extends Controller
{
    public function __construct(private TestService $tests, private AttendanceService $attendance) {}

    public function home(Request $request)
    {
        $student = $request->user();

        $groups = $this->groups($student)->filter(fn ($g) => $g->is_active_member)->values();
        $summary = $this->attendanceSummary($student);

        return view('student.home', [
            'student' => $student,
            'groups' => $groups,
            'overallRate' => $summary['overall'],
            'certificates' => \App\Models\Certificate::where('student_id', $student->id)->where('status', 'issued')->latest('id')->get(),
            'transactions' => $student->balanceTransactions()->with('group:id,name')->latest('id')->limit(5)->get(),
        ]);
    }

    public function groups(\App\Models\User $student)
    {
        $memberships = $student->memberships()->get()->keyBy('group_id');

        return Group::whereIn('id', $memberships->keys())
            ->with(['course:id,name', 'teacher:id,name'])->orderByDesc('starts_on')->get()
            ->each(fn ($g) => $g->setAttribute('is_active_member', (bool) ($memberships[$g->id]->is_active ?? false)));
    }

    public function groupsPage(Request $request)
    {
        return view('student.groups', ['groups' => $this->groups($request->user())]);
    }

    public function payments(Request $request)
    {
        $student = $request->user();

        return view('student.payments', [
            'student' => $student,
            'transactions' => $student->balanceTransactions()->with('group:id,name')->latest('id')->paginate(20),
        ]);
    }

    public function attendancePage(Request $request)
    {
        $student = $request->user();
        $summary = $this->attendanceSummary($student);

        $recent = Attendance::where('student_id', $student->id)->with('group:id,name')->orderByDesc('date')->orderByDesc('id')->limit(40)->get();

        return view('student.attendance', ['rows' => $summary['rows'], 'overall' => $summary['overall'], 'recent' => $recent]);
    }

    /** @return array{rows: array<int,array<string,mixed>>, overall: ?int} */
    private function attendanceSummary(\App\Models\User $student): array
    {
        $rows = [];
        foreach (Group::whereIn('id', $student->memberships()->select('group_id'))->get() as $group) {
            $row = $this->attendance->studentSummary($group, $student->id);
            if ($row) {
                $rows[] = ['group' => $group->name] + $row;
            }
        }

        $rated = array_filter(array_column($rows, 'rate'), fn ($r) => $r !== null);

        return ['rows' => $rows, 'overall' => $rated ? (int) round(array_sum($rated) / count($rated)) : null];
    }

    public function courses(Request $request)
    {
        $student = $request->user();

        $courses = Course::whereIn('id', $this->tests->accessibleCourseIds($student))
            ->withCount(['videos', 'audios', 'questions'])->orderBy('name')->get();

        $best = TestAttempt::where('student_id', $student->id)->whereNotNull('finished_at')
            ->selectRaw('course_id, max(score) as best, count(*) as attempts')->groupBy('course_id')->get()->keyBy('course_id');

        return view('student.courses', ['courses' => $courses, 'best' => $best, 'books' => Book::active()->orderBy('name')->get()]);
    }

    public function course(Request $request, Course $course)
    {
        $student = $request->user();
        $this->tests->assertAccess($student, $course);

        $videos = $course->videos()->get()->map(fn ($v) => [
            'id' => $v->id, 'number' => $v->number, 'title' => $v->title,
            'kind' => $v->embed['kind'], 'src' => $v->embed['src'], 'label' => $v->embed['label'], 'url' => $v->url,
        ])->values();

        $audios = $course->audios()->get()->map(fn ($a) => [
            'id' => $a->id, 'number' => $a->number, 'title' => $a->title,
            'kind' => $a->media['kind'], 'src' => $a->media['src'], 'label' => $a->media['label'], 'url' => $a->url,
        ])->values();

        $finished = TestAttempt::where('student_id', $student->id)->where('course_id', $course->id)->whereNotNull('finished_at');

        return view('student.course', [
            'course' => $course,
            'videos' => $videos,
            'audios' => $audios,
            'questionCount' => $course->questions()->count(),
            'best' => (clone $finished)->max('score'),
            'attempts' => (clone $finished)->count(),
            'results' => (clone $finished)->latest('id')->limit(10)->get(),
        ]);
    }

    public function startTest(Request $request, Course $course): RedirectResponse
    {
        $attempt = $this->tests->start($request->user(), $course);

        return redirect()->route('student.tests.show', $attempt->id);
    }

    public function test(Request $request, int $attempt)
    {
        $model = TestAttempt::where('student_id', $request->user()->id)->findOrFail($attempt);

        if ($model->finished_at !== null) {
            return redirect()->route('student.tests.result', $model->id);
        }

        return view('student.test', ['attempt' => $model, 'course' => Course::find($model->course_id)]);
    }

    public function submitTest(Request $request, int $attempt): RedirectResponse
    {
        $model = TestAttempt::where('student_id', $request->user()->id)->findOrFail($attempt);

        if ($model->finished_at === null) {
            $data = $request->validate([
                'answers' => ['nullable', 'array'],
                'answers.*' => ['integer', 'min:0', 'max:3'],
            ], [], ['answers' => 'Javoblar']);

            $this->tests->submit($model, $data['answers'] ?? []);
        }

        // Natija alohida sahifada: yangilash (F5) testni qayta topshirmaydi
        return redirect()->route('student.tests.result', $model->id);
    }

    public function result(Request $request, int $attempt)
    {
        $model = TestAttempt::where('student_id', $request->user()->id)->findOrFail($attempt);

        if ($model->finished_at === null) {
            return redirect()->route('student.tests.show', $model->id);
        }

        return view('student.test-result', [
            'course' => Course::find($model->course_id),
            'attempt' => $model,
            'review' => $this->tests->review($model),
        ]);
    }
}
