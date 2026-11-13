<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Models\PasswordResetLog;
use App\Models\User;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** v14: TIZIM → Telegram bot (faqat sAdmin): token, webhook, Admin ID lar, ulangan foydalanuvchilar, parol tiklash jurnali. */
class TelegramAdminController extends Controller
{
    public function index(TelegramService $tg)
    {
        $me = $tg->configured() ? ($tg->call('getMe')['result'] ?? null) : null;
        $hook = $tg->configured() ? ($tg->call('getWebhookInfo')['result'] ?? []) : [];

        return view('telegram.index', [
            'configured' => $tg->configured(),
            'bot' => $me,
            'hook' => $hook,
            'webhookUrl' => $tg->webhookUrl(),
            'admins' => $tg->admins(),
            'linked' => User::withoutGlobalScopes()->with('branch')->whereNotNull('telegram_chat_id')->orderByDesc('telegram_linked_at')->paginate(15, ['*'], 'linked_page'),
            'logs' => PasswordResetLog::with('user:id,name,role')->orderByDesc('id')->paginate(20, ['*'], 'log_page'),
        ]);
    }

    public function saveToken(Request $request, TelegramService $tg): RedirectResponse
    {
        $data = $request->validate(['token' => ['required', 'string', 'regex:/^\d{6,12}:[A-Za-z0-9_-]{30,}$/']], [], ['token' => 'Bot tokeni']);

        AppSetting::put('telegram.token', $data['token'], true);
        $me = $tg->call('getMe');

        if (! ($me['ok'] ?? false)) {
            AppSetting::forget('telegram.token');

            return back()->with('error', "Token noto'g'ri yoki Telegramga ulanib bo'lmadi: ".($me['description'] ?? ''));
        }

        AppSetting::put('telegram.username', $me['result']['username'] ?? '');
        AuditLog::record('telegram.token_saved', null, 'Telegram bot tokeni saqlandi (@'.($me['result']['username'] ?? '').')');

        return back()->with('success', 'Bot ulandi: @'.($me['result']['username'] ?? ''));
    }

    public function setWebhook(TelegramService $tg): RedirectResponse
    {
        if (! str_starts_with($tg->webhookUrl(), 'https://')) {
            return back()->with('error', 'Webhook faqat HTTPS manzilga o\'rnatiladi (hozirgi manzil: '.$tg->webhookUrl().').');
        }

        $r = $tg->setWebhook();
        $ok = (bool) ($r['ok'] ?? false);
        AuditLog::record('telegram.webhook_set', null, "Telegram webhook o'rnatildi");

        return back()->with($ok ? 'success' : 'error', $ok ? "Webhook o'rnatildi." : 'Xato: '.($r['description'] ?? ''));
    }

    public function deleteWebhook(TelegramService $tg): RedirectResponse
    {
        $r = $tg->call('deleteWebhook', ['drop_pending_updates' => true]);
        $ok = (bool) ($r['ok'] ?? false);

        return back()->with($ok ? 'success' : 'error', $ok ? "Webhook o'chirildi." : 'Xato: '.($r['description'] ?? ''));
    }

    public function test(TelegramService $tg): RedirectResponse
    {
        $ids = $tg->adminIds();
        if (! $ids) {
            return back()->with('error', "Avval Admin ID qo'shing.");
        }

        $ok = collect($ids)->map(fn ($id) => $tg->sendMessage($id, '✅ '.\App\Support\Brand::name().': sinov xabari '.now()->format('d.m.Y H:i')))->every(fn ($v) => $v);

        return back()->with($ok ? 'success' : 'error', $ok ? 'Sinov xabari yuborildi.' : "Ba'zi Admin ID larga yuborib bo'lmadi (bot bilan /start qilinganini tekshiring).");
    }

    public function addAdmin(Request $request, TelegramService $tg): RedirectResponse
    {
        $data = $request->validate(['chat_id' => ['required', 'integer', 'min:1'], 'label' => ['nullable', 'string', 'max:60']], [], ['chat_id' => 'Telegram ID']);
        $tg->addAdmin((int) $data['chat_id'], $data['label'] ?? '');
        AuditLog::record('telegram.admin_added', null, "Telegram Admin ID qo'shildi: {$data['chat_id']}");

        return back()->with('success', "Admin ID qo'shildi.");
    }

    public function removeAdmin(int $id, TelegramService $tg): RedirectResponse
    {
        $tg->removeAdmin($id);
        AuditLog::record('telegram.admin_removed', null, "Telegram Admin ID o'chirildi: {$id}");

        return back()->with('success', "Admin ID o'chirildi.");
    }

    public function unlink(int $id): RedirectResponse
    {
        $u = User::withoutGlobalScopes()->findOrFail($id);
        $u->forceFill(['telegram_chat_id' => null, 'telegram_username' => null, 'telegram_linked_at' => null])->save();
        AuditLog::record('telegram.unlinked', $u, "Telegram uzildi: {$u->name}");

        return back()->with('success', 'Telegram uzildi.');
    }
}
