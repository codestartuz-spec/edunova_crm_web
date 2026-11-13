<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CashboxController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\ContractSettingsController;
use App\Http\Controllers\CourseContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\DiscountRuleController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupStudentController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicLeadController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\Student\PortalController as StudentPortalController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentNoteController;
use App\Http\Controllers\SystemStatusController;
use Illuminate\Support\Facades\Route;

// v14: Telegram webhook (CSRF'dan istisno; maxfiy sarlavha bilan himoyalangan)
Route::post('telegram/webhook', [\App\Http\Controllers\TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

// v14: Web Push service worker (har qanday docroot'da ildiz manzildan beriladi)
Route::get('sw.js', [\App\Http\Controllers\PwaController::class, 'worker'])->name('sw');
Route::get('manifest.webmanifest', [\App\Http\Controllers\PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('offline', [\App\Http\Controllers\PwaController::class, 'offline'])->name('pwa.offline');
Route::get('brand/{file}', [\App\Http\Controllers\BrandController::class, 'asset'])->where('file', '[a-z0-9-]+\.png')->name('brand.asset');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:30,1');
    Route::post('login/choose', [LoginController::class, 'choose'])->middleware('throttle:30,1')->name('login.choose');

    // v14: parolni tiklash (telefon -> Telegram/SMS kodi)
    Route::get('forgot-password', [PasswordResetController::class, 'phone'])->name('password.forgot');
    Route::post('forgot-password', [PasswordResetController::class, 'sendCode'])->middleware('throttle:10,1')->name('password.send');
    Route::get('forgot-password/code', [PasswordResetController::class, 'code'])->name('password.code');
    Route::post('forgot-password/code', [PasswordResetController::class, 'verify'])->middleware('throttle:10,1')->name('password.verify');
    Route::get('forgot-password/choose', [PasswordResetController::class, 'choose'])->name('password.choose');
    Route::post('forgot-password/choose', [PasswordResetController::class, 'pick'])->name('password.pick');
    Route::get('forgot-password/new', [PasswordResetController::class, 'newPassword'])->name('password.new');
    Route::post('forgot-password/new', [PasswordResetController::class, 'update'])->middleware('throttle:10,1')->name('password.update');
});

// v14: huquqiy hujjatlar (hamma o'qiy oladi)
Route::get('verify/{code}', [\App\Http\Controllers\CertificateVerifyController::class, 'show'])->where('code', '[A-Za-z0-9]{8,20}')->middleware('throttle:30,1')->name('certificates.verify');
Route::get('monitor/beat', function (\Illuminate\Http\Request $r) {
    $key = (string) config('services.monitor.beat_key');
    abort_if($key === '' || ! hash_equals($key, (string) $r->query('key')), 404);
    \Illuminate\Support\Facades\Cache::forever('monitor.beat', now()->timestamp);

    return response('ok');
})->middleware('throttle:10,1')->name('monitor.beat');

Route::get('legal/{type}', [\App\Http\Controllers\LegalController::class, 'show'])->whereIn('type', ['privacy', 'terms', 'cookies'])->name('legal.show');

// Saytdan murojaat qoldirish (ochiq)
Route::get('apply', [PublicLeadController::class, 'index'])->name('apply.index');
Route::get('apply/{branch:code}', [PublicLeadController::class, 'show'])->name('apply.show');
Route::post('apply/{branch:code}', [PublicLeadController::class, 'store'])->middleware('throttle:10,60')->name('apply.store');


Route::middleware(['auth', 'active', 'legal'])->group(function () {
    // v14: rozilik sahifasi (middleware bu marshrutlarni o'tkazib yuboradi)
    Route::get('legal-consent', [\App\Http\Controllers\LegalController::class, 'consent'])->name('legal.consent');
    Route::post('legal-consent', [\App\Http\Controllers\LegalController::class, 'accept'])->middleware('throttle:20,1')->name('legal.accept');

    // v14: TIZIM → Huquqiy hujjatlar va rozilik jurnali
    Route::middleware('can:legal.manage')->group(function () {
        Route::get('system/legal', [\App\Http\Controllers\LegalAdminController::class, 'index'])->name('legal.admin');
        Route::put('system/legal/{type}', [\App\Http\Controllers\LegalAdminController::class, 'update'])->whereIn('type', ['privacy', 'terms', 'cookies'])->name('legal.admin.update');
        Route::post('system/legal/{type}/reset', [\App\Http\Controllers\LegalAdminController::class, 'reset'])->whereIn('type', ['privacy', 'terms', 'cookies'])->name('legal.admin.reset');
        Route::get('system/legal-journal', [\App\Http\Controllers\LegalAdminController::class, 'journal'])->name('legal.journal');
        Route::get('system/legal-journal/export', [\App\Http\Controllers\LegalAdminController::class, 'export'])->middleware('throttle:10,1')->name('legal.journal.export');
    });

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('tasks/dismiss', [DashboardController::class, 'dismissTask'])->name('tasks.dismiss');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::delete('profile/sessions/{session}', [ProfileController::class, 'revokeSession'])->name('profile.sessions.revoke');
    Route::post('profile/sessions/revoke-others', [ProfileController::class, 'revokeOthers'])->middleware('throttle:5,1')->name('profile.sessions.others');

    // Filial almashtirish: sAdmin (barcha filiallar) va qo'shimcha filialli operator (faqat o'ziga berilganlar)
    Route::post('branch/switch', [BranchController::class, 'switch'])->name('branches.switch');

    // Filiallar (v14: sAdmin yoki shu ruxsat berilgan direktor)
    Route::middleware('can:branches.manage')->group(function () {
        Route::resource('branches', BranchController::class)->except(['destroy']);
        Route::put('branches/{branch}/sms', [BranchController::class, 'sms'])->name('branches.sms');
        Route::post('branches/{branch}/payments', [BranchController::class, 'storePayment'])->middleware('role:sadmin')->name('branches.payments.store');
        Route::post('branches/{branch}/close', [BranchController::class, 'close'])->name('branches.close');
        Route::post('branches/{branch}/reopen', [BranchController::class, 'reopen'])->name('branches.reopen');
    });
    // v10 (7-band): filialni BUTUNLAY o'chirish (barcha ma'lumoti bilan) - alohida ruxsat, nom tasdiqlash bilan
    Route::delete('branches/{branch}', [BranchController::class, 'destroy'])->middleware('can:branches.delete')->name('branches.destroy');

    // v8 A4: butun tizim darajasidagi holat (cron, zaxira, disk, ...) - filialga bog'liq emas
    Route::get('system-status', [SystemStatusController::class, 'index'])->middleware('can:system.view')->name('system-status.index');

    // v14: hodimlarga bildirishnoma yuborish
    // v14: hodimning o'z bildirishnomalari va Web Push obunasi (o'quvchilar uchun emas)
    Route::middleware('role:sadmin,director,admin,manager,teacher,operator')->group(function () {
        Route::get('notifications/feed', [\App\Http\Controllers\NotificationFeedController::class, 'feed'])->name('notifications.feed');
        Route::post('notifications/read-all', [\App\Http\Controllers\NotificationFeedController::class, 'readAll'])->name('notifications.read-all');
        Route::post('notifications/{id}/read', [\App\Http\Controllers\NotificationFeedController::class, 'read'])->whereNumber('id')->name('notifications.read');
        Route::get('push/key', [\App\Http\Controllers\NotificationFeedController::class, 'key'])->name('push.key');
        Route::post('push/subscribe', [\App\Http\Controllers\NotificationFeedController::class, 'subscribe'])->middleware('throttle:20,1')->name('push.subscribe');
        Route::post('push/unsubscribe', [\App\Http\Controllers\NotificationFeedController::class, 'unsubscribe'])->name('push.unsubscribe');
    });

    Route::middleware('can:notifications.send')->group(function () {
        Route::get('notifications/{notification}', [NotificationController::class, 'show'])->whereNumber('notification')->name('notifications.show');
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications', [NotificationController::class, 'store'])->middleware('throttle:20,1')->name('notifications.store');
    });

    // v14: faqat YAGONA sAdmin: direktorlarni qo'shish, bloklash, olib tashlash (ruxsatlari - staff.permissions.edit orqali)
    Route::middleware('role:sadmin')->group(function () {
        Route::post('system-status/run', [SystemStatusController::class, 'runNow'])->middleware('throttle:10,1')->name('system-status.run');
        Route::post('system-status/backup', [SystemStatusController::class, 'runBackupNow'])->middleware('throttle:5,1')->name('system-status.backup');

        // v14: Telegram bot va zaxira (faqat sAdmin)
        Route::get('telegram-bot', [\App\Http\Controllers\TelegramAdminController::class, 'index'])->name('telegram.index');
        Route::post('telegram-bot/token', [\App\Http\Controllers\TelegramAdminController::class, 'saveToken'])->name('telegram.token');
        Route::post('telegram-bot/webhook', [\App\Http\Controllers\TelegramAdminController::class, 'setWebhook'])->name('telegram.webhook.set');
        Route::delete('telegram-bot/webhook', [\App\Http\Controllers\TelegramAdminController::class, 'deleteWebhook'])->name('telegram.webhook.delete');
        Route::post('telegram-bot/test', [\App\Http\Controllers\TelegramAdminController::class, 'test'])->middleware('throttle:5,1')->name('telegram.test');
        Route::post('telegram-bot/admins', [\App\Http\Controllers\TelegramAdminController::class, 'addAdmin'])->name('telegram.admins.add');
        Route::delete('telegram-bot/admins/{id}', [\App\Http\Controllers\TelegramAdminController::class, 'removeAdmin'])->whereNumber('id')->name('telegram.admins.remove');
        Route::delete('telegram-bot/users/{id}', [\App\Http\Controllers\TelegramAdminController::class, 'unlink'])->whereNumber('id')->name('telegram.unlink');
        Route::get('legacy-import', [\App\Http\Controllers\LegacyImportController::class, 'index'])->name('legacy.index');
        Route::post('legacy-import', [\App\Http\Controllers\LegacyImportController::class, 'upload'])->middleware('throttle:10,1')->name('legacy.upload');
        Route::get('legacy-import/review', [\App\Http\Controllers\LegacyImportController::class, 'review'])->name('legacy.review');
        Route::get('legacy-import/progress/{token}', [\App\Http\Controllers\LegacyImportController::class, 'progress'])->where('token', '[a-f0-9]{16}')->name('legacy.progress');
        Route::get('legacy-import/status/{token}', [\App\Http\Controllers\LegacyImportController::class, 'status'])->where('token', '[a-f0-9]{16}')->name('legacy.status');
        Route::post('legacy-import/run', [\App\Http\Controllers\LegacyImportController::class, 'run'])->middleware('throttle:20,1')->name('legacy.run');
        Route::get('backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('backups/run', [\App\Http\Controllers\BackupController::class, 'run'])->middleware('throttle:5,1')->name('backups.run');
        Route::post('backups/password', [\App\Http\Controllers\BackupController::class, 'showPassword'])->middleware('throttle:5,1')->name('backups.password');
        Route::post('backups/{backup}/resend', [\App\Http\Controllers\BackupController::class, 'resend'])->whereNumber('backup')->name('backups.resend');
        Route::post('backups/{backup}/download', [\App\Http\Controllers\BackupController::class, 'download'])->whereNumber('backup')->middleware('throttle:5,1')->name('backups.download');
        Route::delete('backups/{backup}', [\App\Http\Controllers\BackupController::class, 'destroy'])->whereNumber('backup')->name('backups.destroy');
        Route::get('brand', [\App\Http\Controllers\BrandController::class, 'edit'])->name('brand.edit');
        Route::post('brand', [\App\Http\Controllers\BrandController::class, 'update'])->middleware('throttle:20,1')->name('brand.update');
        Route::get('directors', [DirectorController::class, 'index'])->name('directors.index');
        Route::get('directors/create', [DirectorController::class, 'create'])->name('directors.create');
        Route::post('directors', [DirectorController::class, 'store'])->name('directors.store');
        Route::get('directors/{id}/edit', [DirectorController::class, 'edit'])->whereNumber('id')->name('directors.edit');
        Route::put('directors/{id}', [DirectorController::class, 'update'])->whereNumber('id')->name('directors.update');
        Route::post('directors/{id}/toggle', [DirectorController::class, 'toggle'])->whereNumber('id')->name('directors.toggle');
        Route::delete('directors/{id}', [DirectorController::class, 'destroy'])->whereNumber('id')->name('directors.destroy');
        // Barcha direktorlar uchun BITTA umumiy ruxsatlar shabloni
        Route::get('directors/permissions', [DirectorController::class, 'permissions'])->name('directors.permissions.edit');
        Route::put('directors/permissions', [DirectorController::class, 'updatePermissions'])->name('directors.permissions.update');
    });

    // Hodimlar va ruxsatlar
    Route::resource('staff', StaffController::class)
        ->parameters(['staff' => 'user'])
        ->except(['show', 'destroy']);
    Route::delete('staff/{user}/sessions', [StaffController::class, 'endSessions'])->name('staff.sessions.end');
    Route::get('staff/{user}/permissions', [PermissionController::class, 'edit'])->name('staff.permissions.edit');
    Route::put('staff/{user}/permissions', [PermissionController::class, 'update'])->name('staff.permissions.update');

    // v15: sertifikatlar
    Route::middleware('can:certificates.issue')->group(function () {
        Route::get('certificates', [\App\Http\Controllers\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('students/{student}/certificates/create', [\App\Http\Controllers\CertificateController::class, 'createStudent'])->whereNumber('student')->name('certificates.create-student');
        Route::get('groups/{group}/certificates/create', [\App\Http\Controllers\CertificateController::class, 'createGroup'])->name('certificates.create-group');
        Route::post('groups/{group}/certificates', [\App\Http\Controllers\CertificateController::class, 'issueGroup'])->name('certificates.issue-group');
        Route::post('students/{student}/certificates', [\App\Http\Controllers\CertificateController::class, 'issueStudent'])->whereNumber('student')->name('certificates.issue-student');
        Route::get('certificates/{certificate}/pdf', [\App\Http\Controllers\CertificateController::class, 'download'])->whereNumber('certificate')->name('certificates.pdf');
    });
    Route::middleware('can:certificates.manage')->group(function () {
        Route::get('certificates/settings', [\App\Http\Controllers\CertificateController::class, 'settings'])->name('certificates.settings');
        Route::post('certificates/settings', [\App\Http\Controllers\CertificateController::class, 'saveSettings'])->name('certificates.settings.save');
        Route::post('certificates/{certificate}/revoke', [\App\Http\Controllers\CertificateController::class, 'revoke'])->whereNumber('certificate')->name('certificates.revoke');
        Route::post('certificates/{certificate}/reissue', [\App\Http\Controllers\CertificateController::class, 'reissue'])->whereNumber('certificate')->name('certificates.reissue');
    });

    // O'quvchilar
    Route::get('students/create', [StudentController::class, 'create'])->middleware('branch')->name('students.create');
    Route::post('students', [StudentController::class, 'store'])->middleware('branch')->name('students.store');
    Route::get('students/search', [StudentController::class, 'search'])->name('students.search');
    Route::get('students', [StudentController::class, 'index'])->name('students.index');
    Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::post('students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');
    Route::post('students/{student}/archive', [StudentController::class, 'archive'])->name('students.archive');
    Route::post('students/{student}/restore', [StudentController::class, 'restore'])->name('students.restore');
    // v13: yuqori paneldagi eslatmalar qo'ng'iroqchasi (har 15 soniyada so'raladi) va faolsizlantirish
    Route::get('notes/feed', [StudentNoteController::class, 'feed'])->middleware('throttle:120,1')->name('notes.feed');
    Route::post('notes/{note}/close', [StudentNoteController::class, 'close'])->name('notes.close');
    Route::post('notes/{note}/reopen', [StudentNoteController::class, 'reopen'])->name('notes.reopen');
    Route::post('students/{student}/notes', [StudentNoteController::class, 'store'])->name('students.notes.store');
    Route::delete('students/{student}/notes/{note}', [StudentNoteController::class, 'destroy'])->name('students.notes.destroy');

    // Guruhlar
    Route::get('groups/create', [GroupController::class, 'create'])->middleware('branch')->name('groups.create');
    Route::post('groups', [GroupController::class, 'store'])->middleware('branch')->name('groups.store');
    Route::get('groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::get('groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::post('groups/{group}/archive', [GroupController::class, 'archiveInitiate'])->name('groups.archive');
    Route::post('groups/{group}/continue', [GroupController::class, 'continue'])->name('groups.continue');
    Route::post('groups/{group}/students', [GroupStudentController::class, 'store'])->name('groups.students.store');
    Route::delete('groups/{group}/students/{student}', [GroupStudentController::class, 'destroy'])->name('groups.students.destroy');
    Route::get('groups/{group}/students/{student}/contract', [GroupStudentController::class, 'contract'])->name('groups.students.contract');

    // Davomad
    Route::get('attendance', [AttendanceController::class, 'today'])->name('attendance.today');
    Route::get('attendance/stats', [AttendanceController::class, 'stats'])->name('attendance.stats');
    Route::post('groups/{group}/attendance', [AttendanceController::class, 'take'])->name('attendance.take');
    Route::get('groups/{group}/attendance/past', [AttendanceController::class, 'pastForm'])->name('attendance.past.show');
    Route::post('groups/{group}/attendance/past', [AttendanceController::class, 'editPast'])->name('attendance.past.store');

    // Excel import
    Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
    Route::get('imports/template', [ImportController::class, 'template'])->name('imports.template');
    Route::post('imports', [ImportController::class, 'upload'])->name('imports.upload');
    Route::get('imports/{batch}', [ImportController::class, 'show'])->name('imports.show');
    Route::post('imports/{batch}/confirm', [ImportController::class, 'confirm'])->name('imports.confirm');
    Route::post('imports/{batch}/cancel', [ImportController::class, 'cancel'])->name('imports.cancel');
    Route::get('imports/{batch}/result', [ImportController::class, 'result'])->name('imports.result');

    // Statistika va hisobotlar
    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::post('statistics/refresh', [StatisticsController::class, 'refresh'])->middleware('throttle:6,1')->name('statistics.refresh');
    Route::get('statistics/data', [StatisticsController::class, 'data'])->middleware('throttle:30,1')->name('statistics.data');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{report}/export', [ReportController::class, 'export'])->name('reports.export');

    // Varonka (lidlar)
    Route::get('leads/create', [LeadController::class, 'create'])->middleware('branch')->name('leads.create');
    Route::post('leads', [LeadController::class, 'store'])->middleware('branch')->name('leads.store');
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::post('leads/{lead}/note', [LeadController::class, 'note'])->name('leads.note');
    Route::post('leads/{lead}/cancel', [LeadController::class, 'cancel'])->name('leads.cancel');
    Route::post('leads/{lead}/analyze', [LeadController::class, 'analyze'])->middleware('throttle:20,1')->name('leads.analyze');
    Route::post('leads/{lead}/reopen', [LeadController::class, 'reopen'])->name('leads.reopen');
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->middleware('branch')->name('leads.convert');

    // SMS
    Route::get('sms', [SmsController::class, 'index'])->name('sms.index');
    Route::middleware('can:sms.gateway')->group(function () {
        Route::get('sms/gateway', [\App\Http\Controllers\SmsGatewayController::class, 'edit'])->name('sms.gateway');
        Route::put('sms/gateway', [\App\Http\Controllers\SmsGatewayController::class, 'update'])->name('sms.gateway.update');
        Route::post('sms/gateway/test', [\App\Http\Controllers\SmsGatewayController::class, 'test'])->middleware('throttle:10,1')->name('sms.gateway.test');
    });
    Route::middleware('branch')->group(function () {
        Route::put('sms/settings', [SmsController::class, 'settings'])->name('sms.settings');
        Route::post('sms/bulk', [SmsController::class, 'bulk'])->name('sms.bulk');
    });

    // Kurs materiallari
    Route::get('courses/{course}', [CourseContentController::class, 'show'])->name('courses.show');
    Route::post('courses/{course}/videos', [CourseContentController::class, 'storeVideo'])->name('courses.videos.store');
    Route::delete('courses/{course}/videos/{video}', [CourseContentController::class, 'destroyVideo'])->name('courses.videos.destroy');
    Route::post('courses/{course}/audios', [CourseContentController::class, 'storeAudio'])->name('courses.audios.store');
    Route::delete('courses/{course}/audios/{audio}', [CourseContentController::class, 'destroyAudio'])->name('courses.audios.destroy');
    Route::post('courses/{course}/questions', [CourseContentController::class, 'storeQuestion'])->name('courses.questions.store');
    Route::post('courses/{course}/questions/ai', [CourseContentController::class, 'aiGenerate'])->middleware('throttle:20,60')->name('courses.questions.ai');
    Route::post('courses/{course}/questions/ai-store', [CourseContentController::class, 'storeAiQuestions'])->name('courses.questions.ai-store');
    Route::delete('courses/{course}/questions/{question}', [CourseContentController::class, 'destroyQuestion'])->name('courses.questions.destroy');

    // To'lovlar
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('students/{student}/payments', [PaymentController::class, 'store'])->middleware('once')->name('payments.store');
    Route::post('students/{student}/discount', [PaymentController::class, 'discount'])->middleware('once')->name('payments.discount');
    Route::post('students/{student}/special-discount', [PaymentController::class, 'specialDiscountInitiate'])->name('payments.special-discount');
    Route::post('students/{student}/refund', [PaymentController::class, 'refund'])->middleware('once')->name('payments.refund');
    Route::post('payments/{payment}/reverse', [PaymentController::class, 'reverseInitiate'])->name('payments.reverse');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // Kassa, moliya, ish haqi (filial tanlangan bo'lishi kerak)
    Route::middleware('branch')->group(function () {
        Route::get('cashbox', [CashboxController::class, 'index'])->name('cashbox.index');
        Route::post('cashbox/requests', [CashboxController::class, 'store'])->middleware('once')->name('cashbox.store');
        Route::post('cashbox/requests/{cashRequest}/approve', [CashboxController::class, 'approve'])->name('cashbox.approve');
        Route::post('cashbox/requests/{cashRequest}/cancel', [CashboxController::class, 'cancel'])->name('cashbox.cancel');
        Route::post('cashbox/refunds/{payment}/confirm', [CashboxController::class, 'confirmRefund'])->name('cashbox.refund-confirm');
        Route::post('cashbox/refunds/{payment}/reject', [CashboxController::class, 'rejectRefund'])->name('cashbox.refund-reject');
        Route::post('cashbox/close', [CashboxController::class, 'closeShift'])->middleware('once')->name('cashbox.close');

        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('finance/withdraw', [FinanceController::class, 'withdraw'])->name('finance.withdraw');
        Route::post('finance/charity-withdraw', [FinanceController::class, 'charityWithdraw'])->name('finance.charity-withdraw');
        Route::post('finance/expense', [FinanceController::class, 'expense'])->name('finance.expense');
        Route::post('finance/charity', [FinanceController::class, 'charity'])->name('finance.charity');
        Route::post('finance/deposit', [FinanceController::class, 'deposit'])->middleware('once')->name('finance.deposit');

        // Ikki bosqichli tasdiqlash ("Tekshiring" sahifasi): pul chiqadigan amallar shu yerda bajariladi
        Route::get('confirm/{token}', [ConfirmationController::class, 'show'])->name('confirm.show');
        Route::post('confirm/{token}', [ConfirmationController::class, 'store'])->name('confirm.store');

        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/{user}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::post('payroll/{user}', [PayrollController::class, 'pay'])->name('payroll.pay');
    });

    // Filial sozlamalari va kurslar (sAdmin uchun filial tanlangan bo'lishi kerak)
    Route::middleware('branch')->group(function () {
        Route::get('settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::get('settings/discount-rules', [DiscountRuleController::class, 'edit'])->name('discount-rules.edit');
        Route::put('settings/discount-rules', [DiscountRuleController::class, 'update'])->name('discount-rules.update');
        Route::get('settings/holidays', [HolidayController::class, 'index'])->name('holidays.index');
        Route::post('settings/holidays', [HolidayController::class, 'store'])->name('holidays.store');
        Route::post('settings/holidays/generate', [HolidayController::class, 'generate'])->name('holidays.generate');
        Route::delete('settings/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
        Route::get('settings/contract', [ContractSettingsController::class, 'edit'])->name('contract-settings.edit');
        Route::put('settings/contract', [ContractSettingsController::class, 'update'])->name('contract-settings.update');
        Route::put('settings/contract/reset', [ContractSettingsController::class, 'reset'])->name('contract-settings.reset');
        Route::get('settings/usluga', [\App\Http\Controllers\UslugaSettingsController::class, 'edit'])->name('usluga-settings.edit');
        Route::put('settings/usluga', [\App\Http\Controllers\UslugaSettingsController::class, 'update'])->name('usluga-settings.update');

        Route::prefix('settings/{catalog}')->whereIn('catalog', ['courses', 'rooms', 'lesson-times', 'price-plans', 'campaigns', 'books', 'lead-sources', 'expense-categories'])->group(function () {
            Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
            Route::post('/', [CatalogController::class, 'store'])->name('catalog.store');
            Route::put('{id}', [CatalogController::class, 'update'])->name('catalog.update');
            Route::post('{id}/toggle', [CatalogController::class, 'toggle'])->name('catalog.toggle');
        });
    });

    Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit.index');

    // v14: O'QUVCHI KABINETI (faqat o'quvchilar; mobil ilova olib tashlangani uchun veb orqali)
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/', [StudentPortalController::class, 'home'])->name('home');
        Route::get('certificates/{id}', [\App\Http\Controllers\CertificateController::class, 'mine'])->whereNumber('id')->name('certificates.mine');
        Route::get('groups', [StudentPortalController::class, 'groupsPage'])->name('groups');
        Route::get('payments', [StudentPortalController::class, 'payments'])->name('payments');
        Route::get('attendance', [StudentPortalController::class, 'attendancePage'])->name('attendance');
        Route::get('courses', [StudentPortalController::class, 'courses'])->name('courses');
        Route::get('courses/{course}', [StudentPortalController::class, 'course'])->name('courses.show');
        Route::post('courses/{course}/test', [StudentPortalController::class, 'startTest'])->name('tests.start');
        Route::get('tests/{attempt}', [StudentPortalController::class, 'test'])->whereNumber('attempt')->name('tests.show');
        Route::post('tests/{attempt}', [StudentPortalController::class, 'submitTest'])->whereNumber('attempt')->name('tests.submit');
        Route::get('tests/{attempt}/result', [StudentPortalController::class, 'result'])->whereNumber('attempt')->name('tests.result');
    });

});
