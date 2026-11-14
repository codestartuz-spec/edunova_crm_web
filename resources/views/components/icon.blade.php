{{--
    Maxsus duotone ikonkalar to'plami. Har bir ikonka 3 qatlamdan iborat:
      f - yumshoq to'ldirma, l - asosiy chiziq, a - aksent (ikkinchi rang) detallari.
    Oddiy holatda hamma qatlam currentColor (matn rangi) bilan chiziladi, ya'ni eski joylarda ham ishlaydi.
      colored  - ikonka o'z bo'limining ikki rangida
      bubble   - ranglangan yumshoq plitka ichida,  solid - to'liq gradient plitka ichida
      tone     - rang tonini qo'lda berish (violet, sky, emerald, amber, rose, cyan, indigo, pink, teal, orange, slate)
--}}
@props(['name', 'class' => 'h-5 w-5', 'colored' => false, 'bubble' => false, 'solid' => false, 'tone' => null])
@php
    $dot = 'stroke-width="2.7"';
    $i = [
        // [ton, to'ldirma, chiziq, aksent]
        'home' => ['violet', '<path d="M5 9.8 12 4.2l7 5.6V20H5z"/>', '<path d="M3 10.8 12 3.5l9 7.3"/><path d="M5.2 9.5v11h13.6v-11"/>', '<path d="M10 20.5v-5a2 2 0 0 1 4 0v5"/>'],
        'building' => ['indigo', '<rect x="4.5" y="3.5" width="15" height="17" rx="2"/>', '<rect x="4.5" y="3.5" width="15" height="17" rx="2"/><path d="M3 20.5h18"/>', '<path d="M9 8h.01M15 8h.01M9 12h.01M15 12h.01" '.$dot.'/><path d="M10 20.5v-3.5h4v3.5"/>'],
        'users' => ['sky', '<circle cx="9" cy="8.2" r="3.6"/>', '<circle cx="9" cy="8.2" r="3.6"/><path d="M2.8 20c0-3.5 2.8-5.8 6.2-5.8s6.2 2.3 6.2 5.8"/>', '<path d="M16.2 4.9a3.5 3.5 0 0 1 0 6.6"/><path d="M18.6 14.6c1.8.7 2.9 2.4 2.9 5.4"/>'],
        'user' => ['violet', '<circle cx="12" cy="8" r="4"/>', '<circle cx="12" cy="8" r="4"/><path d="M4.5 20.5c0-4 3.4-6.3 7.5-6.3s7.5 2.3 7.5 6.3"/>', ''],
        'cap' => ['violet', '<path d="M2.5 9 12 4l9.5 5-9.5 5z"/>', '<path d="M2.5 9 12 4l9.5 5-9.5 5z"/><path d="M6 11.7V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.3"/>', '<path d="M21.5 9v5.5"/>'],
        'shield' => ['teal', '<path d="M12 3 4.6 6v5.6c0 4.5 3 8.2 7.4 9.4 4.4-1.2 7.4-4.9 7.4-9.4V6z"/>', '<path d="M12 3 4.6 6v5.6c0 4.5 3 8.2 7.4 9.4 4.4-1.2 7.4-4.9 7.4-9.4V6z"/>', '<path d="m8.8 12 2.4 2.4 4.2-4.6"/>'],
        'badge' => ['indigo', '<rect x="3.5" y="4.5" width="17" height="15" rx="2.4"/>', '<rect x="3.5" y="4.5" width="17" height="15" rx="2.4"/>', '<circle cx="9" cy="10.6" r="2"/><path d="M5.9 16c.5-1.5 1.6-2.2 3.1-2.2s2.6.7 3.1 2.2"/><path d="M14.6 9.5h2.9M14.6 12.7h2.9"/>'],
        'calendar' => ['emerald', '<rect x="3.5" y="5" width="17" height="15" rx="2.4"/>', '<rect x="3.5" y="5" width="17" height="15" rx="2.4"/><path d="M3.5 10h17M8 3v4M16 3v4"/>', '<path d="m9.2 15 2 2 3.8-3.8"/>'],
        'pulse' => ['teal', '<rect x="3" y="5" width="18" height="14" rx="3"/>', '<rect x="3" y="5" width="18" height="14" rx="3"/>', '<path d="M6 12h2.5l1.8-3.6 3 7.2 1.8-3.6H18"/>'],
        'chart' => ['indigo', '<rect x="4" y="11" width="4" height="9" rx="1.2"/><rect x="10" y="4" width="4" height="16" rx="1.2"/><rect x="16" y="8" width="4" height="12" rx="1.2"/>', '<rect x="4" y="11" width="4" height="9" rx="1.2"/><rect x="10" y="4" width="4" height="16" rx="1.2"/><rect x="16" y="8" width="4" height="12" rx="1.2"/>', '<path d="M2.5 21h19"/>'],
        'funnel' => ['amber', '<path d="M3.5 5h17l-6.5 7.8V19l-4 2v-8.2z"/>', '<path d="M3.5 5h17l-6.5 7.8V19l-4 2v-8.2z"/>', '<path d="M7 8.2h10"/>'],
        'message' => ['cyan', '<path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5v9a1.5 1.5 0 0 1-1.5 1.5H10l-4 4v-4h-.5A1.5 1.5 0 0 1 4 14.5z"/>', '<path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5v9a1.5 1.5 0 0 1-1.5 1.5H10l-4 4v-4h-.5A1.5 1.5 0 0 1 4 14.5z"/>', '<path d="M8 8.4h8M8 11.7h5"/>'],
        'banknote' => ['emerald', '<rect x="2.5" y="6" width="19" height="12" rx="2.2"/>', '<rect x="2.5" y="6" width="19" height="12" rx="2.2"/>', '<circle cx="12" cy="12" r="2.6"/><path d="M6 12h.01M18 12h.01" '.$dot.'/>'],
        'wallet' => ['orange', '<rect x="3" y="6" width="18" height="13" rx="2.4"/>', '<rect x="3" y="6" width="18" height="13" rx="2.4"/><path d="M3 10h18"/>', '<path d="M16 14.6h2"/>'],
        'cashbox' => ['orange', '<rect x="3.5" y="9.5" width="17" height="11" rx="2.2"/>', '<rect x="3.5" y="9.5" width="17" height="11" rx="2.2"/><path d="M7 9.5V6a1.5 1.5 0 0 1 1.5-1.5h7A1.5 1.5 0 0 1 17 6v3.5"/>', '<path d="M7.5 14h9M12 14v3.2"/>'],
        'coins' => ['amber', '<ellipse cx="9.5" cy="7.5" rx="5.5" ry="2.6"/>', '<ellipse cx="9.5" cy="7.5" rx="5.5" ry="2.6"/><path d="M4 7.5v4.2c0 1.4 2.4 2.6 5.5 2.6M4 11.7v4c0 1.4 2.4 2.6 5.5 2.6"/>', '<ellipse cx="15.5" cy="14" rx="5.5" ry="2.6"/><path d="M10 14v4.2c0 1.4 2.4 2.6 5.5 2.6s5.5-1.2 5.5-2.6V14"/>'],
        'award' => ['rose', '<circle cx="12" cy="9" r="5.8"/>', '<circle cx="12" cy="9" r="5.8"/>', '<path d="M8.6 13.6 7 21l5-2.6 5 2.6-1.6-7.4"/><path d="m12 6.3.9 1.9 2 .3-1.5 1.4.4 2-1.8-1-1.8 1 .4-2L9.1 8.5l2-.3z"/>'],
        'report' => ['orange', '<path d="M6.5 3.5h8l4 4v12a1.5 1.5 0 0 1-1.5 1.5h-10.5A1.5 1.5 0 0 1 5 19.5v-14A1.5 1.5 0 0 1 6.5 3.5z"/>', '<path d="M6.5 3.5h8l4 4v12a1.5 1.5 0 0 1-1.5 1.5h-10.5A1.5 1.5 0 0 1 5 19.5v-14A1.5 1.5 0 0 1 6.5 3.5z"/><path d="M14.5 3.5v4h4"/>', '<path d="M8.6 17v-3M12 17v-5.5M15.4 17v-4"/>'],
        'log' => ['slate', '<rect x="5.5" y="4.5" width="13" height="16" rx="2.2"/>', '<rect x="5.5" y="4.5" width="13" height="16" rx="2.2"/><path d="M9 4.5V3h6v1.5"/>', '<path d="M9 11h6M9 14.5h6M9 18h3"/>'],
        'file' => ['slate', '<path d="M14 3H7.5A1.5 1.5 0 0 0 6 4.5v15A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V7z"/>', '<path d="M14 3H7.5A1.5 1.5 0 0 0 6 4.5v15A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V7z"/><path d="M14 3v4h4"/>', '<path d="M9 12.5h6M9 16h6"/>'],
        'cog' => ['slate', '<circle cx="12" cy="12" r="5.3"/>', '<circle cx="12" cy="12" r="5.3"/><circle cx="12" cy="12" r="1.9"/>', '<path d="M12 2.8v2.6M12 18.6v2.6M2.8 12h2.6M18.6 12h2.6M5.5 5.5l1.9 1.9M16.6 16.6l1.9 1.9M5.5 18.5l1.9-1.9M16.6 7.4l1.9-1.9" stroke-width="2.4"/>'],
        'note' => ['amber', '<path d="M5 5.5A1.5 1.5 0 0 1 6.5 4h11A1.5 1.5 0 0 1 19 5.5V15l-5 5H6.5A1.5 1.5 0 0 1 5 18.5z"/>', '<path d="M5 5.5A1.5 1.5 0 0 1 6.5 4h11A1.5 1.5 0 0 1 19 5.5V15l-5 5H6.5A1.5 1.5 0 0 1 5 18.5z"/>', '<path d="M8.5 9h7M8.5 12.5h4M19 15h-4a1 1 0 0 0-1 1v4"/>'],
        'crown' => ['amber', '<path d="M3.5 8.5 7.6 12 12 5l4.4 7 4.1-3.5-1.7 10.5H5.2z"/>', '<path d="M3.5 8.5 7.6 12 12 5l4.4 7 4.1-3.5-1.7 10.5H5.2z"/>', '<path d="M6 21h12"/>'],
        'briefcase' => ['indigo', '<rect x="3" y="7" width="18" height="13" rx="2.4"/>', '<rect x="3" y="7" width="18" height="13" rx="2.4"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/>', '<path d="M3 13h18M11 13h2"/>'],
        'palette' => ['pink', '<path d="M12 3.5a8.5 8.5 0 1 0 0 17c1.4 0 2-.9 2-1.8 0-1.2-1.1-1.5-1.1-2.7 0-1 .8-1.6 1.9-1.6H17a3.5 3.5 0 0 0 3.5-3.5C20.5 6.6 16.7 3.5 12 3.5z"/>', '<path d="M12 3.5a8.5 8.5 0 1 0 0 17c1.4 0 2-.9 2-1.8 0-1.2-1.1-1.5-1.1-2.7 0-1 .8-1.6 1.9-1.6H17a3.5 3.5 0 0 0 3.5-3.5C20.5 6.6 16.7 3.5 12 3.5z"/>', '<path d="M7.7 11.6h.01M10 7.9h.01M14.2 7.9h.01" '.$dot.'/>'],
        'send' => ['cyan', '<path d="M21.5 2.5 14 21.5l-3.5-8-8-3.5z"/>', '<path d="M21.5 2.5 14 21.5l-3.5-8-8-3.5z"/>', '<path d="M21.5 2.5 10.5 13.5"/>'],
        'megaphone' => ['rose', '<path d="M3 10.5v3a1 1 0 0 0 1 1h2.5l8 4V5.5l-8 4H4a1 1 0 0 0-1 1z"/>', '<path d="M3 10.5v3a1 1 0 0 0 1 1h2.5l8 4V5.5l-8 4H4a1 1 0 0 0-1 1z"/>', '<path d="M18.2 9a4 4 0 0 1 0 6"/>'],
        'database' => ['slate', '<ellipse cx="12" cy="6" rx="7.5" ry="3"/>', '<ellipse cx="12" cy="6" rx="7.5" ry="3"/><path d="M4.5 6v6c0 1.7 3.4 3 7.5 3s7.5-1.3 7.5-3V6"/>', '<path d="M4.5 12v6c0 1.7 3.4 3 7.5 3s7.5-1.3 7.5-3v-6"/>'],
        'cloud' => ['sky', '<path d="M7 18.5a4.5 4.5 0 0 1-.7-8.9A6 6 0 0 1 17.7 9a4.8 4.8 0 0 1 .3 9.5z"/>', '<path d="M7 18.5a4.5 4.5 0 0 1-.7-8.9A6 6 0 0 1 17.7 9a4.8 4.8 0 0 1 .3 9.5z"/>', '<path d="M12 16v-5.5M9.7 12.7l2.3-2.3 2.3 2.3"/>'],
        'archive' => ['slate', '<rect x="3" y="4" width="18" height="5" rx="1.4"/>', '<rect x="3" y="4" width="18" height="5" rx="1.4"/><path d="M5 9v9.2A1.8 1.8 0 0 0 6.8 20h10.4a1.8 1.8 0 0 0 1.8-1.8V9"/>', '<path d="M10 13h4"/>'],
        'scale' => ['indigo', '<path d="M5 7.5 2.2 14a2.8 2.8 0 0 0 5.6 0z"/><path d="M19 7.5 16.2 14a2.8 2.8 0 0 0 5.6 0z"/>', '<path d="M5 7.5 2.2 14a2.8 2.8 0 0 0 5.6 0z"/><path d="M19 7.5 16.2 14a2.8 2.8 0 0 0 5.6 0z"/>', '<path d="M12 4v16M7.5 20.5h9M5 7.5h14"/>'],
        'heartbeat' => ['rose', '<path d="M12 20.2s-7.5-4.4-7.5-10.2A4.2 4.2 0 0 1 12 7.6a4.2 4.2 0 0 1 7.5 2.4c0 5.8-7.5 10.2-7.5 10.2z"/>', '<path d="M12 20.2s-7.5-4.4-7.5-10.2A4.2 4.2 0 0 1 12 7.6a4.2 4.2 0 0 1 7.5 2.4c0 5.8-7.5 10.2-7.5 10.2z"/>', '<path d="M7 11.5h2.3l1.4-2.6 2 4.8 1.2-2.2H17"/>'],
        'bell' => ['amber', '<path d="M6 9.5a6 6 0 1 1 12 0c0 4.2 1.5 5.7 1.5 5.7h-15S6 13.7 6 9.5z"/>', '<path d="M6 9.5a6 6 0 1 1 12 0c0 4.2 1.5 5.7 1.5 5.7h-15S6 13.7 6 9.5z"/>', '<path d="M10 18.5a2 2 0 0 0 4 0"/>'],
        'history' => ['slate', '<circle cx="12" cy="12" r="8.2"/>', '<path d="M3.6 12a8.4 8.4 0 1 0 2.5-6L3.6 8.4"/><path d="M3.6 4v4.4H8"/>', '<path d="M12 7.6V12l3 2"/>'],
        'sparkles' => ['pink', '<path d="m11 3.5 1.9 5.1 5.1 1.9-5.1 1.9L11 17.5 9.1 12.4 4 10.5l5.1-1.9z"/>', '<path d="m11 3.5 1.9 5.1 5.1 1.9-5.1 1.9L11 17.5 9.1 12.4 4 10.5l5.1-1.9z"/>', '<path d="m19 15 .8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/>'],
        'book' => ['teal', '<path d="M5 4h11a3 3 0 0 1 3 3v13H8a3 3 0 0 1-3-3z"/>', '<path d="M5 4h11a3 3 0 0 1 3 3v13H8a3 3 0 0 1-3-3z"/>', '<path d="M5 17a3 3 0 0 1 3-3h11M9 8h6"/>'],
        'phone' => ['emerald', '<path d="M6.5 3.5h3l1.5 4-2 1.5a11 11 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2 2A16 16 0 0 1 4.5 5.5a2 2 0 0 1 2-2z"/>', '<path d="M6.5 3.5h3l1.5 4-2 1.5a11 11 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2 2A16 16 0 0 1 4.5 5.5a2 2 0 0 1 2-2z"/>', ''],
        'key' => ['amber', '<circle cx="8" cy="15" r="4"/>', '<circle cx="8" cy="15" r="4"/><path d="m11 12 8-8"/>', '<path d="m16 7 3 3"/>'],
        'lock' => ['violet', '<rect x="5" y="10" width="14" height="10.5" rx="2.4"/>', '<rect x="5" y="10" width="14" height="10.5" rx="2.4"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>', '<path d="M12 14.5v2.2"/>'],
        'pin' => ['rose', '<path d="M12 21s7-5.7 7-11a7 7 0 1 0-14 0c0 5.3 7 11 7 11z"/>', '<path d="M12 21s7-5.7 7-11a7 7 0 1 0-14 0c0 5.3 7 11 7 11z"/>', '<circle cx="12" cy="10" r="2.5"/>'],
        'gift' => ['pink', '<rect x="3.5" y="9" width="17" height="11.5" rx="2"/>', '<rect x="3.5" y="9" width="17" height="11.5" rx="2"/><path d="M2.5 9h19V6.5h-19z"/>', '<path d="M12 6.5v14M12 6.5c-1-3-5-3.2-5-1s3 2.2 5 1zM12 6.5c1-3 5-3.2 5-1s-3 2.2-5 1z"/>'],
        'clock' => ['sky', '<circle cx="12" cy="12" r="8.5"/>', '<circle cx="12" cy="12" r="8.5"/>', '<path d="M12 7.5V12l3 2"/>'],
        'eye' => ['indigo', '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/>', '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/>', '<circle cx="12" cy="12" r="2.8"/>'],
        'download' => ['emerald', '<path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3z"/>', '<path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>', '<path d="M12 4v11M8 11l4 4 4-4"/>'],
        'upload' => ['sky', '<path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3z"/>', '<path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>', '<path d="M12 15V4M8 8l4-4 4 4"/>'],
        'filter' => ['violet', '<path d="M4 5h16l-6 7v6l-4 2v-8z"/>', '<path d="M4 5h16l-6 7v6l-4 2v-8z"/>', ''],
        'refresh' => ['teal', '', '<path d="M20 11a8 8 0 0 0-14.5-3.5M4 13a8 8 0 0 0 14.5 3.5"/>', '<path d="M5.5 3.5v4h4M18.5 20.5v-4h-4"/>'],
        'link' => ['sky', '', '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/>', '<path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>'],
        'info' => ['sky', '<circle cx="12" cy="12" r="8.5"/>', '<circle cx="12" cy="12" r="8.5"/>', '<path d="M12 11v5M12 8h.01" '.$dot.'/>'],
        'globe' => ['cyan', '<circle cx="12" cy="12" r="8.5"/>', '<circle cx="12" cy="12" r="8.5"/>', '<path d="M3.5 12h17M12 3.5c2.4 2.4 3.6 5.2 3.6 8.5S14.4 18.1 12 20.5M12 3.5C9.6 5.9 8.4 8.7 8.4 12s1.2 6.1 3.6 8.5"/>'],
        'copy' => ['slate', '<rect x="9" y="9" width="11" height="11" rx="2.2"/>', '<rect x="9" y="9" width="11" height="11" rx="2.2"/>', '<path d="M5 15V6a2 2 0 0 1 2-2h9"/>'],
        'alert' => ['amber', '<path d="M12 3.5 2.5 20h19z"/>', '<path d="M12 3.5 2.5 20h19z"/>', '<path d="M12 10v4.2M12 17.4h.01" '.$dot.'/>'],
        // Oddiy interfeys belgilari
        'plus' => ['violet', '', '<path d="M12 5v14M5 12h14"/>', ''],
        'check' => ['emerald', '', '<path d="m5 12.5 4.5 4.5L19 7.5"/>', ''],
        'x' => ['slate', '', '<path d="M6 6l12 12M18 6 6 18"/>', ''],
        'menu' => ['slate', '', '<path d="M4 6.5h16M4 12h16"/>', '<path d="M4 17.5h10"/>'],
        'search' => ['violet', '<circle cx="11" cy="11" r="6.5"/>', '<circle cx="11" cy="11" r="6.5"/>', '<path d="m20 20-4.2-4.2"/>'],
        'play' => ['rose', '<rect x="3" y="5" width="18" height="14" rx="3.4"/>', '<rect x="3" y="5" width="18" height="14" rx="3.4"/>', '<path d="M10.2 9.2v5.6l4.7-2.8z"/>'],
        'chevron' => ['slate', '', '<path d="m6 9 6 6 6-6"/>', ''],
        'edit' => ['sky', '<path d="M4 20h4L19 9l-4-4L4 16z"/>', '<path d="M4 20h4L19 9l-4-4L4 16z"/>', '<path d="m13.5 6.5 4 4"/>'],
        'trash' => ['rose', '<path d="M6 7l1 13h10l1-13z"/>', '<path d="M6 7l1 13h10l1-13"/><path d="M4 7h16M9 7V4h6v3"/>', '<path d="M10 11v5M14 11v5"/>'],
        'logout' => ['slate', '', '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="m10 17-5-5 5-5"/>', '<path d="M5 12h11"/>'],
        'sun' => ['amber', '<circle cx="12" cy="12" r="4"/>', '<circle cx="12" cy="12" r="4"/>', '<path d="M12 2.5v2M12 19.5v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2.5 12h2M19.5 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>'],
        'moon' => ['indigo', '<path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/>', '<path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/>', '<path d="M17.5 4.5v3M16 6h3"/>'],
    ];
    $def = $i[$name] ?? null;
    $tones = $tone ?: ($def[0] ?? 'violet');
    $mode = $bubble || $solid;
    $svgClass = 'ic'.(($colored || $mode) ? ' ic-c' : '');
    $svgSize = $mode ? 'h-3/5 w-3/5' : $class;
    if ($colored && ! $mode) {
        $attributes = $attributes->class(['ic-t-'.$tones]);
    }
@endphp
@if ($mode)<span {{ $attributes->class(['ic-bubble', 'ic-solid' => $solid, 'ic-t-'.$tones, $class]) }}>@endif<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" data-ic="{{ $name }}" @if ($mode) class="{{ $svgClass }} {{ $svgSize }}" @else {{ $attributes->merge(['class' => $svgClass.' '.$svgSize]) }} @endif aria-hidden="true">@if ($def)<g class="ic-f">{!! $def[1] !!}</g><g class="ic-l">{!! $def[2] !!}</g><g class="ic-a">{!! $def[3] !!}</g>@endif</svg>@if ($mode)</span>@endif
