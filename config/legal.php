<?php

return [
    // false bo'lsa rozilik oynasi so'ralmaydi (avtomatik testlar uchun). Ishlab chiqarishda har doim true.
    'enforce' => env('LEGAL_ENFORCE', true),

    // Cookie rozilik tanlovi saqlanadigan cookie nomi (shifrlanmaydi - JS ham o'qiydi)
    'cookie' => 'cookie_consent',
];
