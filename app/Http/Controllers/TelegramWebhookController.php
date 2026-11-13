<?php

namespace App\Http\Controllers;

use App\Services\Telegram\TelegramBot;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/** v14: Telegram webhook. Faqat maxfiy sarlavha (X-Telegram-Bot-Api-Secret-Token) to'g'ri bo'lsa ishlaydi. */
class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramService $tg, TelegramBot $bot)
    {
        if (! $tg->configured() || ! hash_equals($tg->secret(), (string) $request->header('X-Telegram-Bot-Api-Secret-Token'))) {
            abort(403);
        }

        try {
            $bot->handle($request->json()->all());
        } catch (\Throwable $e) {
            report($e);
            // "Tizim holati" sahifasida ko'rsatiladi: bot jimgina buzilib qolmasin
            Cache::put('telegram.webhook_last_error', ['at' => now()->toIso8601String(), 'message' => Str::limit($e->getMessage(), 200)], now()->addDay());
        }

        return response()->json(['ok' => true]);
    }
}
