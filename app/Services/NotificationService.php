<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * v14: faqat HODIMLAR uchun bildirishnoma: rollar va filiallar bo'yicha tanlanadi.
 * Qabul qiluvchilar yuborish paytida `notification_recipients`ga yoziladi (o'qildi holati bilan);
 * qo'ng'iroqcha/toast/brauzer bildirishnomasi `notifications/feed` orqali, Web Push esa WebPushService orqali.
 */
class NotificationService
{
    /** Bildirishnoma olishi mumkin bo'lgan hodim rollari (sAdmin yuboruvchi, o'quvchilar kirmaydi). */
    public const STAFF_ROLES = ['director', 'admin', 'manager', 'teacher', 'operator'];

    /**
     * @param  array<int,string>  $roles  STAFF_ROLES dan
     * @param  array<int,int>|null  $branchIds  null/[] = barcha filiallar
     * @return array{0:Notification,1:int}
     */
    public function send(string $title, string $body, array $roles, ?array $branchIds, bool $important, ?string $url, User $sender): array
    {
        $roles = array_values(array_intersect($roles, self::STAFF_ROLES));
        $branchIds = $branchIds ? array_values(array_unique(array_map('intval', $branchIds))) : null;

        [$notification, $userIds] = DB::transaction(function () use ($title, $body, $roles, $branchIds, $important, $url, $sender) {
            $notification = Notification::create([
                'branch_id' => $branchIds && count($branchIds) === 1 ? $branchIds[0] : null,
                'title' => $title,
                'body' => $body,
                'is_important' => $important,
                'url' => $url,
                'roles' => $roles,
                'branch_ids' => $branchIds,
                'sent_by' => $sender->id,
            ]);

            $userIds = User::withoutGlobalScopes()
                ->whereIn('role', $roles)
                ->whereNull('archived_at')->where('status', 'active')
                ->where('id', '!=', $sender->id)
                // Direktor filialga bog'lanmagan - filial tanlovidan qat'i nazar, roli tanlangan bo'lsa oladi
                ->when($branchIds, fn ($q) => $q->where(fn ($w) => $w->whereIn('branch_id', $branchIds)->orWhereNull('branch_id')))
                ->pluck('id');

            $now = now();
            $rows = $userIds->map(fn ($id) => ['notification_id' => $notification->id, 'user_id' => $id, 'push_status' => NotificationRecipient::PENDING, 'created_at' => $now, 'updated_at' => $now])->all();
            foreach (array_chunk($rows, 500) as $chunk) {
                NotificationRecipient::insert($chunk);
            }
            $notification->update(['recipients_count' => count($rows)]);

            return [$notification, $userIds];
        });

        app(WebPushService::class)->deliver($notification, $userIds->all());

        return [$notification->fresh(), $userIds->count()];
    }

    public static function roleOptions(): array
    {
        return collect(self::STAFF_ROLES)->mapWithKeys(fn ($r) => [$r => Role::from($r)->label()])->all();
    }
}
