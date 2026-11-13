<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\WebPushService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** v14: hodimlarga bildirishnoma yuborish (notifications.send: sAdmin yoki ruxsat berilgan direktor). */
class NotificationController extends Controller
{
    public function index(WebPushService $push)
    {
        return view('notifications.index', [
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'roles' => NotificationService::roleOptions(),
            'pushAvailable' => $push->available(),
            'history' => Notification::with('sender:id,name')->withCount(['recipients as read_count' => fn ($q) => $q->whereNotNull('read_at')])->orderByDesc('id')->paginate(20),
        ]);
    }

    public function show(Notification $notification)
    {
        return view('notifications.show', ['n' => $notification->load('sender:id,name'), 'recipients' => $notification->recipients()->with('user:id,name,role,branch_id')->orderBy('read_at')->get()]);
    }

    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:1000'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::in(NotificationService::STAFF_ROLES)],
            'all_branches' => ['nullable', 'boolean'],
            'branch_ids' => ['nullable', 'array'],
            'branch_ids.*' => ['integer', \App\Support\BranchContext::branchExists(false)],
            'url' => ['nullable', 'string', 'max:500', 'regex:#^(https?://|/)\S*$#'],
            'is_important' => ['nullable', 'boolean'],
        ], [], ['title' => 'Sarlavha', 'body' => 'Matn', 'roles' => 'Rollar', 'branch_ids' => 'Filiallar', 'url' => 'Havola']);

        $branchIds = $request->boolean('all_branches') ? null : ($data['branch_ids'] ?? []);
        if ($branchIds !== null && $branchIds === []) {
            return back()->withInput()->withErrors(['branch_ids' => 'Filial tanlang yoki «Barcha filiallar»ni belgilang.']);
        }

        [$n, $count] = $notifications->send($data['title'], $data['body'], $data['roles'], $branchIds, $request->boolean('is_important'), $data['url'] ?? null, $request->user());

        AuditLog::record('notification.sent', $n, "Bildirishnoma yuborildi: \"{$data['title']}\" — ".($branchIds === null ? 'barcha filiallar' : count($branchIds).' ta filial')." ({$count} ta hodim)", branchId: $n->branch_id);

        return back()->with('success', "Bildirishnoma {$count} ta hodimga yuborildi.");
    }
}
