<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\TutorialVideo;
use App\Support\MediaEmbed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * v24: «Video darslar».
 *  v26: ko'rish ham, boshqarish ham FAQAT sAdmin (marshrutda `role:sadmin`); boshqa foydalanuvchilarga menyuda ko'rinmaydi.
 */
class TutorialController extends Controller
{
    public function index()
    {
        $videos = TutorialVideo::visible()->ordered()->get();

        return view('tutorials.index', [
            'videos' => $videos,
            'categories' => $videos->pluck('category')->filter()->unique()->values(),
            'canManage' => $this->canManage(),
        ]);
    }

    public function show(TutorialVideo $video)
    {
        abort_unless($video->is_active || $this->canManage(), 404);

        $playlist = TutorialVideo::visible()->ordered()->get();
        $i = $playlist->search(fn ($v) => $v->id === $video->id);

        return view('tutorials.show', [
            'video' => $video,
            'playlist' => $playlist,
            'prev' => $i !== false && $i > 0 ? $playlist[$i - 1] : null,
            'next' => $i !== false ? ($playlist[$i + 1] ?? null) : null,
            'canManage' => $this->canManage(),
        ]);
    }

    // ---------------- sAdmin: Sozlamalar → Video darslar ----------------

    public function admin()
    {
        return view('tutorials.admin', [
            'videos' => TutorialVideo::ordered()->get(),
            'categories' => TutorialVideo::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = ((int) TutorialVideo::max('sort_order')) + 1;
        $data['created_by'] = $request->user()->id;
        $video = TutorialVideo::create($data);
        AuditLog::record('tutorial.created', $video, "Video dars qo'shildi: {$video->title}");

        return redirect()->route('tutorials.admin')->with('success', "Video dars qo'shildi.");
    }

    public function update(Request $request, TutorialVideo $video): RedirectResponse
    {
        $data = $this->validated($request);
        $video->update($data);
        AuditLog::record('tutorial.updated', $video, "Video dars yangilandi: {$video->title}");

        return redirect()->route('tutorials.admin')->with('success', 'Saqlandi.');
    }

    public function toggle(TutorialVideo $video): RedirectResponse
    {
        $video->update(['is_active' => ! $video->is_active]);
        AuditLog::record('tutorial.toggled', $video, $video->title.($video->is_active ? ' — ko\'rsatildi' : ' — yashirildi'));

        return back()->with('success', $video->is_active ? 'Video hammaga ko\'rinadi.' : 'Video yashirildi.');
    }

    /** Bir pog'ona yuqoriga/pastga (sort_order bilan almashtirish). */
    public function move(Request $request, TutorialVideo $video): RedirectResponse
    {
        $dir = $request->validate(['dir' => ['required', Rule::in(['up', 'down'])]])['dir'];

        $all = TutorialVideo::ordered()->get();
        // Tartib raqamlari takrorlansa ham ishlashi uchun avval ketma-ket raqamlab chiqamiz
        foreach ($all as $n => $v) {
            if ($v->sort_order !== $n + 1) {
                $v->update(['sort_order' => $n + 1]);
            }
        }
        $all = TutorialVideo::ordered()->get();
        $i = $all->search(fn ($v) => $v->id === $video->id);
        $j = $dir === 'up' ? $i - 1 : $i + 1;
        if ($i !== false && isset($all[$j])) {
            [$a, $b] = [$all[$i]->sort_order, $all[$j]->sort_order];
            $all[$i]->update(['sort_order' => $b]);
            $all[$j]->update(['sort_order' => $a]);
        }

        return back();
    }

    public function destroy(TutorialVideo $video): RedirectResponse
    {
        AuditLog::record('tutorial.deleted', $video, "Video dars o'chirildi: {$video->title}");
        $video->delete();

        return back()->with('success', "Video dars o'chirildi.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'url' => ['required', 'string', 'max:500', 'url:http,https'],
            'category' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
            'duration' => ['nullable', 'string', 'max:10', 'regex:/^\d{1,2}(:\d{2}){1,2}$/'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'duration.regex' => "Davomiylik 5:30 yoki 1:05:30 ko'rinishida bo'lsin.",
        ], ['title' => 'Sarlavha', 'url' => 'Havola', 'category' => 'Kategoriya', 'description' => 'Tavsif', 'duration' => 'Davomiylik']);

        // Havola tizim ichida ijro etiladigan bo'lishi shart: YouTube, Vimeo, Google Drive yoki to'g'ridan-to'g'ri video fayl
        $media = MediaEmbed::resolve($data['url']);
        if (! in_array($media['kind'], ['iframe', 'video'], true)) {
            back()->withInput()->withErrors(['url' => "Bu havolani tizim ichida ijro etib bo'lmaydi. YouTube, Vimeo, Google Drive havolasi yoki to'g'ridan-to'g'ri video fayl (.mp4) kiriting."])->throwResponse();
        }

        $data['category'] = filled($data['category'] ?? null) ? trim($data['category']) : null;
        $data['description'] = filled($data['description'] ?? null) ? trim($data['description']) : null;
        $data['duration'] = filled($data['duration'] ?? null) ? $data['duration'] : null;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function canManage(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }
}
