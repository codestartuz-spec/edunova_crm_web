<?php

namespace App\Support;

/**
 * v25: ochiq landing sahifasi matnlari (o'zbek va rus tillarida). Matnlar kalit so'zlarga boy,
 * lekin faqat tizimda HAQIQATAN mavjud imkoniyatlarni yozadi.
 */
class LandingContent
{
    public static function get(string $locale): array
    {
        $base = $locale === 'ru' ? self::ru(Brand::name()) : self::uz(Brand::name());

        // animatsion bloklar (sotuv hikoyasi, demo forma) alohida faylda
        return $base + ['x' => LandingExtra::get($locale)];
    }

    private static function uz(string $n): array
    {
        return [
            'lang' => 'uz',
            'meta' => [
                'title' => "{$n} — o'quv markazlari uchun CRM tizimi",
                'description' => "O'quv markazingizni bitta tizimdan boshqaring: o'quvchilar, guruhlar, davomat, to'lovlar, sertifikatlar, Telegram bot va hisobotlar. Filiallar uchun.",
            ],
            'nav' => ['features' => 'Imkoniyatlar', 'roles' => 'Kim uchun', 'how' => 'Qanday ishlaydi', 'faq' => 'Savollar', 'login' => 'Kirish'],
            'hero' => [
                'badge' => "O'quv markazlari uchun CRM",
                'title' => "O'quv markazingizni bitta tizimdan boshqaring",
                'lead' => "O'quvchilar, guruhlar, davomat, to'lovlar, oylik hisob va hisobotlar bir joyda. Filiallar, rollar va Telegram bot bilan — qog'ozbozlik va Excel jadvallarsiz.",
                'cta' => 'Tizimga kirish',
                'cta2' => 'Imkoniyatlarni ko‘rish',
                'points' => ["O'zbek va rus tillarida", 'Telefon va kompyuterda', 'Telegram bot va SMS'],
            ],
            'features_title' => "Markaz ishining hamma qismi bitta CRM'da",
            'features_lead' => "O'quvchi ariza qoldirgan paytdan sertifikat olgunicha — har qadam nazoratda.",
            'features' => [
                ['icon' => 'users', 'title' => "O'quvchilar va guruhlar", 'text' => "O'quvchilar bazasi, guruhlarga qabul, kurs va narx rejalari, chegirmalar, kitoblar va shartnomalar. Kimning qaysi guruhda o'qishi doim ko'z oldingizda."],
                ['icon' => 'banknote', 'title' => "To'lovlar va kassa", 'text' => "To'lovlarni qabul qilish, qarzdorlarni aniqlash, xarajatlar va kassa hisobi. Oy oxirida pulning qayerga ketgani aniq."],
                ['icon' => 'calendar', 'title' => 'Davomat va dars jadvali', 'text' => "Dars kalendari, xonalar va dars vaqtlari, bayram kunlari. O'qituvchi davomatni telefondan belgilaydi."],
                ['icon' => 'funnel', 'title' => "Lidlar va ochiq ariza shakli", 'text' => "Murojaatlar voronkasi: har bir ariza manbasi bilan hisoblanadi. Ariza shaklini o'z saytingizga joylashtirish mumkin."],
                ['icon' => 'award', 'title' => 'Sertifikatlar va tekshirish', 'text' => "Markaz dizayniga mos PDF sertifikat va QR kod. Har kim sertifikat haqiqiyligini havola orqali tekshira oladi."],
                ['icon' => 'send', 'title' => 'Telegram bot va SMS', 'text' => "Parolni tiklash kodi, obuna va to'lov eslatmalari, kunlik hisobot — Telegram yoki SMS orqali avtomatik yetkaziladi."],
                ['icon' => 'building', 'title' => 'Filiallar va rollar', 'text' => "Har filial alohida ma'lumot, rang va sozlamalarga ega. Direktor, administrator, menejer, o'qituvchi, operator — har kim o'z huquqi doirasida ishlaydi."],
                ['icon' => 'chart', 'title' => 'Hisobotlar va statistika', 'text' => "Daromad, qarzdorlik, davomat va lidlar bo'yicha hisobotlar va grafiklar. Qarorlarni taxminga emas, raqamga tayanib qabul qiling."],
                ['icon' => 'cap', 'title' => "O'quvchi kabineti va testlar", 'text' => "O'quvchi o'z guruhi, to'lovlari va davomatini ko'radi, kurs materiallari bo'yicha onlayn test topshiradi."],
                ['icon' => 'briefcase', 'title' => 'Oylik hisob-kitob', 'text' => "O'qituvchi va xodimlar maoshi, ish haqi hisobi va to'lov tarixi. Hisob-kitoblarda xato va tortishuv kamayadi."],
                ['icon' => 'shield', 'title' => 'Xavfsizlik va zaxira', 'text' => "Amallar jurnali, parol bilan himoyalangan zaxira nusxalar, huquqiy hujjatlarga rozilik. Ma'lumotlaringiz himoyada."],
                ['icon' => 'play', 'title' => 'Video darsliklar', 'text' => "Tizim ichidagi video qo'llanmalar: yangi xodim reklamasiz, qulay pleyerda tizimni tez o'rganadi."],
            ],
            'roles_title' => 'Markazdagi har bir odam uchun',
            'roles_lead' => "Har rol faqat o'ziga kerakli bo'limlarni ko'radi — tizim sodda va xavfsiz.",
            'roles' => [
                ['icon' => 'crown', 'title' => 'Direktor', 'text' => "Barcha filiallar bo'yicha daromad, qarz va o'sish ko'rsatkichlari."],
                ['icon' => 'cog', 'title' => 'Administrator', 'text' => "Guruhlar, kurslar, narxlar, xodimlar va sozlamalarni boshqaradi."],
                ['icon' => 'briefcase', 'title' => 'Menejer', 'text' => "Qabul, to'lovlar va o'quvchilar bilan kundalik ish."],
                ['icon' => 'book', 'title' => "O'qituvchi", 'text' => "O'z guruhlari, davomat va dars jadvali."],
                ['icon' => 'phone', 'title' => 'Operator', 'text' => "Murojaatlarni qabul qilib, lidlarni o'quvchiga aylantiradi."],
                ['icon' => 'cap', 'title' => "O'quvchi", 'text' => "Shaxsiy kabinet: jadval, to'lovlar, davomat, testlar."],
            ],
            'how_title' => 'Ishga tushirish oson',
            'how' => [
                ['title' => 'Markazni sozlang', 'text' => "Filial, kurslar, xonalar, narx rejalari va xodimlarni kiriting. Eski ro'yxatlar Excel'dan import qilinadi."],
                ['title' => "O'quvchilarni qabul qiling", 'text' => "Arizadan guruhga, shartnoma va birinchi to'lovgacha bir necha bosishda."],
                ['title' => 'Nazorat qiling', 'text' => "Davomat, qarzdorlik va hisobotlar real vaqtda; eslatmalar Telegram va SMS orqali o'zi ketadi."],
            ],
            'faq_title' => "Ko'p so'raladigan savollar",
            'faq' => [
                ['q' => "{$n} nima va kimlar uchun?", 'a' => "{$n} — o'quv markazlari, til kurslari va repetitorlik markazlari uchun CRM tizimi. U o'quvchilar, guruhlar, davomat, to'lovlar, oylik hisob va hisobotlarni bitta joyda yuritishga yordam beradi."],
                ['q' => "Bir nechta filialni yuritish mumkinmi?", 'a' => "Ha. Har filial alohida ma'lumot, rang va sozlamalarga ega, direktor esa barcha filiallar bo'yicha umumiy ko'rsatkichlarni ko'radi."],
                ['q' => "Telegram bot va SMS nima uchun kerak?", 'a' => "Bot orqali foydalanuvchilar parolni tiklash kodini oladi, obuna va to'lov haqida eslatma keladi, adminlar esa kunlik hisobot va tizim holatini ko'radi. SMS esa Telegramdan foydalanmaydiganlar uchun muqobil yo'l."],
                ['q' => "Tizim telefonda ishlaydimi?", 'a' => "Ha. Tizim har qanday qurilmada brauzerda ochiladi va ilova sifatida (PWA) telefon ekraniga o'rnatiladi. Qorong'i va yorug' rejimlar mavjud."],
                ['q' => "Ma'lumotlarim xavfsizmi?", 'a' => "Har bir foydalanuvchi faqat o'z roliga ruxsat etilgan bo'limlarni ko'radi, barcha muhim amallar jurnalga yoziladi, zaxira nusxalar parol bilan himoyalanadi."],
                ['q' => "Eski ma'lumotlarni olib o'tish mumkinmi?", 'a' => "Ha. O'quvchilar va boshqa ro'yxatlar Excel/CSV fayllaridan import qilinadi, shu sababli hozirgi jadvallaringizni qayta yozib o'tirish shart emas."],
            ],
            'cta' => ['title' => "{$n} bilan markazni tartibga soling", 'text' => "Hisobingiz bo'lsa, tizimga kiring. Hisob olish yoki demo ko'rish uchun biz bilan bog'laning.", 'login' => 'Tizimga kirish', 'contact' => "Bog'lanish"],
            'footer' => ['rights' => 'Barcha huquqlar himoyalangan.', 'lang' => 'Til', 'docs' => 'Hujjatlar'],
            'aria' => ['theme' => 'Rejimni almashtirish', 'menu' => 'Menyu'],
        ];
    }

    private static function ru(string $n): array
    {
        return [
            'lang' => 'ru',
            'meta' => [
                'title' => "{$n} — CRM-система для учебных центров",
                'description' => 'Управляйте учебным центром из одной системы: ученики, группы, посещаемость, оплаты, сертификаты, Telegram-бот и отчёты. Для сети филиалов.',
            ],
            'nav' => ['features' => 'Возможности', 'roles' => 'Для кого', 'how' => 'Как это работает', 'faq' => 'Вопросы', 'login' => 'Войти'],
            'hero' => [
                'badge' => 'CRM для учебных центров',
                'title' => 'Управляйте учебным центром из одной системы',
                'lead' => 'Ученики, группы, посещаемость, оплаты, зарплаты и отчёты в одном месте. Филиалы, роли и Telegram-бот — без бумажной волокиты и таблиц Excel.',
                'cta' => 'Войти в систему',
                'cta2' => 'Смотреть возможности',
                'points' => ['На узбекском и русском', 'С телефона и компьютера', 'Telegram-бот и SMS'],
            ],
            'features_title' => 'Вся работа центра — в одной CRM',
            'features_lead' => 'От заявки ученика до выдачи сертификата — каждый шаг под контролем.',
            'features' => [
                ['icon' => 'users', 'title' => 'Ученики и группы', 'text' => 'База учеников, зачисление в группы, курсы и тарифные планы, скидки, книги и договоры. Всегда видно, кто и в какой группе учится.'],
                ['icon' => 'banknote', 'title' => 'Оплаты и касса', 'text' => 'Приём оплат, выявление должников, расходы и кассовый учёт. В конце месяца понятно, куда ушли деньги.'],
                ['icon' => 'calendar', 'title' => 'Посещаемость и расписание', 'text' => 'Календарь занятий, аудитории и время уроков, праздничные дни. Преподаватель отмечает посещаемость с телефона.'],
                ['icon' => 'funnel', 'title' => 'Лиды и форма заявки', 'text' => 'Воронка обращений: каждая заявка учитывается по источнику. Форму заявки можно встроить на ваш сайт.'],
                ['icon' => 'award', 'title' => 'Сертификаты и проверка', 'text' => 'PDF-сертификат в дизайне центра с QR-кодом. Подлинность сертификата проверяется по ссылке.'],
                ['icon' => 'send', 'title' => 'Telegram-бот и SMS', 'text' => 'Код восстановления пароля, напоминания о подписке и оплатах, ежедневный отчёт — автоматически в Telegram или по SMS.'],
                ['icon' => 'building', 'title' => 'Филиалы и роли', 'text' => 'У каждого филиала свои данные, цвет и настройки. Директор, администратор, менеджер, преподаватель, оператор — каждый работает в рамках своих прав.'],
                ['icon' => 'chart', 'title' => 'Отчёты и статистика', 'text' => 'Отчёты и графики по доходам, задолженности, посещаемости и лидам. Решения — на основе цифр, а не догадок.'],
                ['icon' => 'cap', 'title' => 'Кабинет ученика и тесты', 'text' => 'Ученик видит свою группу, оплаты и посещаемость, проходит онлайн-тесты по материалам курса.'],
                ['icon' => 'briefcase', 'title' => 'Расчёт зарплат', 'text' => 'Зарплата преподавателей и сотрудников, учёт начислений и история выплат. Меньше ошибок и споров в расчётах.'],
                ['icon' => 'shield', 'title' => 'Безопасность и резервные копии', 'text' => 'Журнал действий, резервные копии под паролем, согласие с правовыми документами. Ваши данные под защитой.'],
                ['icon' => 'play', 'title' => 'Видеоуроки', 'text' => 'Видеоинструкции внутри системы: новый сотрудник быстро осваивает CRM в удобном плеере без рекламы.'],
            ],
            'roles_title' => 'Для каждого человека в центре',
            'roles_lead' => 'Каждая роль видит только нужные ей разделы — система простая и безопасная.',
            'roles' => [
                ['icon' => 'crown', 'title' => 'Директор', 'text' => 'Доходы, долги и показатели роста по всем филиалам.'],
                ['icon' => 'cog', 'title' => 'Администратор', 'text' => 'Группы, курсы, цены, сотрудники и настройки.'],
                ['icon' => 'briefcase', 'title' => 'Менеджер', 'text' => 'Приём, оплаты и повседневная работа с учениками.'],
                ['icon' => 'book', 'title' => 'Преподаватель', 'text' => 'Свои группы, посещаемость и расписание.'],
                ['icon' => 'phone', 'title' => 'Оператор', 'text' => 'Принимает обращения и превращает лиды в учеников.'],
                ['icon' => 'cap', 'title' => 'Ученик', 'text' => 'Личный кабинет: расписание, оплаты, посещаемость, тесты.'],
            ],
            'how_title' => 'Запуск — это просто',
            'how' => [
                ['title' => 'Настройте центр', 'text' => 'Добавьте филиал, курсы, аудитории, тарифы и сотрудников. Старые списки импортируются из Excel.'],
                ['title' => 'Принимайте учеников', 'text' => 'От заявки до группы, договора и первой оплаты — в несколько кликов.'],
                ['title' => 'Контролируйте', 'text' => 'Посещаемость, задолженность и отчёты в реальном времени; напоминания уходят сами через Telegram и SMS.'],
            ],
            'faq_title' => 'Частые вопросы',
            'faq' => [
                ['q' => "Что такое {$n} и для кого он?", 'a' => "{$n} — CRM-система для учебных центров, языковых курсов и центров репетиторства. Помогает вести учеников, группы, посещаемость, оплаты, зарплаты и отчёты в одном месте."],
                ['q' => 'Можно ли вести несколько филиалов?', 'a' => 'Да. У каждого филиала свои данные, цвет и настройки, а директор видит общие показатели по всем филиалам.'],
                ['q' => 'Зачем нужны Telegram-бот и SMS?', 'a' => 'Через бот пользователи получают код восстановления пароля, напоминания о подписке и оплатах, а администраторы — ежедневный отчёт и состояние системы. SMS — альтернатива для тех, кто не пользуется Telegram.'],
                ['q' => 'Работает ли система на телефоне?', 'a' => 'Да. Система открывается в браузере на любом устройстве и устанавливается на экран телефона как приложение (PWA). Есть светлая и тёмная темы.'],
                ['q' => 'Мои данные в безопасности?', 'a' => 'Каждый пользователь видит только разрешённые его роли разделы, все важные действия записываются в журнал, а резервные копии защищены паролем.'],
                ['q' => 'Можно ли перенести старые данные?', 'a' => 'Да. Ученики и другие списки импортируются из файлов Excel/CSV, поэтому текущие таблицы не нужно перепечатывать.'],
            ],
            'cta' => ['title' => "Наведите порядок в центре с {$n}", 'text' => 'Если у вас есть аккаунт — войдите в систему. Чтобы получить доступ или увидеть демо, свяжитесь с нами.', 'login' => 'Войти в систему', 'contact' => 'Связаться'],
            'footer' => ['rights' => 'Все права защищены.', 'lang' => 'Язык', 'docs' => 'Документы'],
            'aria' => ['theme' => 'Переключить тему', 'menu' => 'Меню'],
        ];
    }
}
