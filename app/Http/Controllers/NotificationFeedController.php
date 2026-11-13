<?php

namespace App\Http\Controllers;

use App\Models\NotificationRecipient;
use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** v14: hodimning o'z bildirishnomalari (qo'ng'iroqcha) va Web Push obunasi. */
class NotificationFeedController extends Controller
{
    public function feed(Request $request): JsonResponse
    {
        $rows = NotificationRecipient::with('notification:id,title,body,url,is_important,created_at')
            ->where('user_id', $request->user()->id)->orderByDesc('id')->limit(30)->get();

        return response()->json([
            'count' => NotificationRecipient::where('user_id', $request->user()->id)->whereNull('read_at')->count(),
            'items' => $rows->map(fn ($r) => [
                'id' => $r->notification_id, 'title' => $r->notification->title, 'body' => $r->notification->body, 'url' => $r->notification->url,
                'important' => (bool) $r->notification->is_important, 'read' => (bool) $r->read_at, 'at' => $r->notification->created_at->format('d.m.Y H:i'),
            ])->values(),
        ]);
    }

    public function read(Request $request, int $id): JsonResponse
    {
        NotificationRecipient::where('user_id', $request->user()->id)->where('notification_id', $id)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['count' => NotificationRecipient::where('user_id', $request->user()->id)->whereNull('read_at')->count()]);
    }

    public function readAll(Request $request): JsonResponse
    {
        NotificationRecipient::where('user_id', $request->user()->id)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['count' => 0]);
    }

    public function key(WebPushService $push): JsonResponse
    {
        $keys = $push->keys();

        return response()->json(['key' => $keys['public'] ?? null]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $d = $request->validate(['endpoint' => ['required', 'url', 'max:1000'], 'keys.p256dh' => ['required', 'string', 'max:255'], 'keys.auth' => ['required', 'string', 'max:255']]);

        PushSubscription::updateOrCreate(['endpoint_hash' => hash('sha256', $d['endpoint'])], [
            'user_id' => $request->user()->id, 'endpoint' => $d['endpoint'], 'public_key' => $d['keys']['p256dh'], 'auth_token' => $d['keys']['auth'],
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->json(['ok' => true]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => ['required', 'string']]);
        PushSubscription::where('user_id', $request->user()->id)->where('endpoint_hash', hash('sha256', $request->input('endpoint')))->delete();

        return response()->json(['ok' => true]);
    }
}
