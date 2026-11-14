<?php

use App\Jobs\QueueHeartbeatJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

// Har kuni ertalab tug'ilgan kun tabriklari (php artisan schedule:work yoki serverda cron kerak)
Schedule::command('sms:birthdays')->dailyAt('09:00');

// v8 B1: qarzdorlarga avtomatik eslatma (faqat sms_auto_debt yoqilgan filiallarda)
Schedule::command('sms:debts')->dailyAt('09:15');

// v8: eskirgan bir martalik tokenlarni (ikki marta yuborishdan himoya) tozalash
Schedule::command('model:prune', ['--model' => [\App\Models\SubmissionToken::class]])->daily();

// v14: har kuni 01:00 (Toshkent) da zaxira: bazani olib, parollangan ZIP qilib Telegram orqali sAdminga yuboradi (14 kun saqlanadi)
Schedule::command('app:backup')->dailyAt('01:00')->timezone('Asia/Tashkent');

// v8 A4: cron/rejalashtiruvchi ishlab turganini "Tizim holati" sahifasida ko'rsatish uchun signal
Schedule::call(fn () => Cache::put('system.cron_heartbeat', now(), now()->addHours(2)))->everyFiveMinutes();

// v10 (4-band): navbat ishchisi (queue worker) ishlab turganini bilish uchun signal - bu vazifa
// navbatga QO'YILADI, faqat `php artisan queue:work` uni bajarganda signal yangilanadi.
Schedule::job(new QueueHeartbeatJob())->everyFiveMinutes();

// v14: har 15 daqiqada to'liq "Tizim holati" tekshiruvi: tarixga yoziladi, yangi muammo bo'lsa Telegram orqali Admin ID'larga xabar boradi
Schedule::call(fn () => app(\App\Services\HealthService::class)->run())->everyFifteenMinutes()->name('health-check')->withoutOverlapping();

// v15: kunlik hisobot (filiallar kesimida) - har kuni 20:00 Toshkent
// v26: har bir direktor o'z vaqtida oladi - har 5 daqiqada vaqti kelganlarga yuboriladi
Schedule::command('report:daily')->everyFiveMinutes()->withoutOverlapping();

// v22: filial obunasi tugashi haqida eslatma (7/3/1 kun oldin va tugagan kuni) - Telegram orqali direktorga
Schedule::command('subscriptions:notify')->dailyAt('09:30')->timezone('Asia/Tashkent')->withoutOverlapping();
