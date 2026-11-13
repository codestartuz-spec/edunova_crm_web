<?php

return [
    /*
     | Eski bazani veb orqali ko'chirish (sAdmin -> "Eski bazani ko'chirish").
     | Xavfsizlik uchun odatda O'CHIQ: ko'chirish paytida .env ga LEGACY_IMPORT_ENABLED=true qo'ying,
     | tugagach olib tashlang. Buyruq qatori (php artisan app:import-legacy) bu flagga bog'liq emas.
     */
    'legacy_import' => (bool) env('LEGACY_IMPORT_ENABLED', false),

    /*
     | Zaxira ZIP paroli. Bo'sh bo'lsa, tizim parolni o'zi yaratib, APP_KEY bilan shifrlab bazada saqlaydi.
     | Berilsa (tavsiya: 24+ belgi), baza va APP_KEY dan alohida, faqat .env da turadi.
     */
    'backup_zip_password' => env('BACKUP_ZIP_PASSWORD'),
];
