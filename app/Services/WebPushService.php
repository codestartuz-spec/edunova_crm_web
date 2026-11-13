<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\PushSubscription;

/**
 * v14: Web Push (VAPID). `minishlink/web-push` paketi o'rnatilmagan bo'lsa push o'chiq turadi —
 * qo'ng'iroqcha, toast va brauzer bildirishnomasi baribir ishlayveradi.
 */
class WebPushService
{
    public function available(): bool
    {
        return class_exists(\Minishlink\WebPush\WebPush::class);
    }

    /** @return array{public:string,private:string}|null */
    public function keys(): ?array
    {
        if (! $this->available()) {
            return null;
        }

        $public = AppSetting::get('push.vapid_public');
        $private = AppSetting::get('push.vapid_private');

        if (! $public || ! $private) {
            try {
                $k = \Minishlink\WebPush\VAPID::createVapidKeys();
            } catch (\Throwable $e) {
                // OpenSSL sozlanmagan (masalan Windows/XAMPP'da openssl.cnf topilmasa) - push o'chiq, qolgani ishlayveradi
                report($e);

                return null;
            }
            AppSetting::put('push.vapid_public', $k['publicKey']);
            AppSetting::put('push.vapid_private', $k['privateKey'], true);
            [$public, $private] = [$k['publicKey'], $k['privateKey']];
        }

        return ['public' => $public, 'private' => $private];
    }

    /** @param array<int,int> $userIds */
    public function deliver(Notification $n, array $userIds): void
    {
        $keys = $this->keys();
        if (! $keys || $userIds === []) {
            NotificationRecipient::where('notification_id', $n->id)->update(['push_status' => NotificationRecipient::SKIPPED]);

            return;
        }

        try {
            $webPush = new \Minishlink\WebPush\WebPush(['VAPID' => ['subject' => url('/'), 'publicKey' => $keys['public'], 'privateKey' => $keys['private']]]);
            $payload = json_encode(['title' => $n->title, 'body' => $n->body, 'url' => $n->url ?: url('/'), 'important' => $n->is_important, 'id' => $n->id], JSON_UNESCAPED_UNICODE);

            $subs = PushSubscription::whereIn('user_id', $userIds)->get();
            $byEndpoint = $subs->keyBy('endpoint');
            foreach ($subs as $s) {
                $webPush->queueNotification(\Minishlink\WebPush\Subscription::create(['endpoint' => $s->endpoint, 'publicKey' => $s->public_key, 'authToken' => $s->auth_token, 'contentEncoding' => 'aes128gcm']), $payload);
            }

            $sentUsers = [];
            foreach ($webPush->flush() as $report) {
                $sub = $byEndpoint[$report->getEndpoint()] ?? null;
                if (! $sub) {
                    continue;
                }
                if ($report->isSuccess()) {
                    $sentUsers[$sub->user_id] = true;
                } elseif ($report->isSubscriptionExpired()) {
                    $sub->delete();
                }
            }

            $sentIds = array_keys($sentUsers);
            NotificationRecipient::where('notification_id', $n->id)->whereIn('user_id', $sentIds)->update(['push_status' => NotificationRecipient::SENT]);
            NotificationRecipient::where('notification_id', $n->id)->whereNotIn('user_id', $sentIds)->update(['push_status' => NotificationRecipient::SKIPPED]);
        } catch (\Throwable $e) {
            report($e);
            NotificationRecipient::where('notification_id', $n->id)->update(['push_status' => NotificationRecipient::FAILED]);
        }
    }
}
