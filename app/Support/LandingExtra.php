<?php

namespace App\Support;

/** v25: landingning animatsion bloklari (sotuv hikoyasi, demo forma) matnlari: o'zbek va rus tillarida. */
class LandingExtra
{
    public static function get(string $locale): array
    {
        return $locale === 'ru' ? self::ru() : self::uz();
    }

    private static function uz(): array
    {
        return [
            'hero_words_label' => 'Bugun nazorat qiling:',
            'hero_words' => ["o'quvchilarni", "to'lovlarni", 'davomatni', 'filiallarni', 'sertifikatlarni', 'hisobotlarni'],
            'scroll_hint' => 'Pastga suring: bir kun CRM ichida',
            'marquee' => ["O'quvchilar", "To'lovlar", 'Davomat', 'Guruhlar', 'Lidlar', 'Sertifikatlar', 'Telegram bot', 'SMS', 'Filiallar', 'Hisobotlar', 'Oylik hisob', 'Kassa'],
            'pain' => [
                'title' => "Hali ham Excel va daftarda yuryapsizmi?",
                'before_title' => 'Avval',
                'after_title' => 'CRM bilan',
                'before' => ['Excel jadvallari va qog\'oz daftarlar', "Qarzdorni eslab qolish: faqat boshingizda", 'Davomat qog\'ozda, hisobot oy oxirida', "Arizalar Telegram, Instagram va qo'ng'iroqlarda yo'qoladi"],
                'after' => ['Hamma narsa bitta tizimda', 'Qarzdorlar o\'zi ko\'rinadi, eslatma avtomatik ketadi', 'Davomat telefondan, hisobot real vaqtda', "Har ariza voronkada, manbasi bilan"],
            ],
            'story' => [
                'eyebrow' => "Bir kun {n} ichida",
                'title' => "Arizadan hisobotgacha: hammasi bir oqimda",
                'lead' => "Pastga suring va markazingizdagi bir kun qanday o'tishini ko'ring.",
                'scenes' => [
                    ['tag' => '1', 'title' => 'Ariza keladi', 'text' => "Ariza sayt formasi, Instagram yoki qo'ng'iroqdan tushadi. Operator uni manbasi bilan voronkaga joylaydi.", 'chip' => 'Yangi ariza'],
                    ['tag' => '2', 'title' => 'Guruhga qabul', 'text' => 'Bir necha bosishda o\'quvchi guruhga yoziladi: kurs, narx rejasi, chegirma va shartnoma tayyor.', 'chip' => 'Guruhga qo\'shildi'],
                    ['tag' => '3', 'title' => 'Davomat belgilanadi', 'text' => "O'qituvchi darsdan keyin davomatni telefondan belgilaydi. Qatnashmaganlar darrov ko'rinadi.", 'chip' => 'Davomat'],
                    ['tag' => '4', 'title' => "To'lov qabul qilinadi", 'text' => "To'lov kassaga tushadi, qarz yangilanadi, eslatma esa Telegram yoki SMS orqali ketadi.", 'chip' => "To'landi"],
                    ['tag' => '5', 'title' => 'Hisobot tayyor', 'text' => "Direktor daromad, qarz va davomatni real vaqtda ko'radi. Kunlik hisobot Telegramga o'zi keladi.", 'chip' => 'Hisobot'],
                ],
            ],
            'demo' => [
                'title' => "Tizimni o'z ko'zingiz bilan ko'ring",
                'text' => "Demo so'rang: biz bog'lanib, tizimni sizning markazingiz misolida ko'rsatamiz. Majburiyat yo'q.",
                'points' => ["Markazingizga mos sozlab ko'rsatamiz", 'Savollaringizga jonli javob beramiz', "Eski ro'yxatlarni import qilish yo'lini tushuntiramiz"],
                'form' => ['name' => 'Ismingiz', 'phone' => 'Telefon raqamingiz', 'center' => "Markaz nomi (ixtiyoriy)", 'size' => "O'quvchilar soni (ixtiyoriy)", 'size_ph' => 'Tanlang', 'message' => "Savol yoki istak (ixtiyoriy)", 'submit' => "Demo so'rash", 'sending' => 'Yuborilmoqda…', 'privacy' => "Yuborish orqali siz maxfiylik siyosatiga rozilik bildirasiz.", 'error' => "Yuborib bo'lmadi. Birozdan keyin qayta urinib ko'ring."],
                'ok' => ['title' => "So'rovingiz qabul qilindi!", 'text' => "Tez orada siz bilan bog'lanamiz. Rahmat!"],
            ],
            'float_cta' => "Demo so'rash",
        ];
    }

    private static function ru(): array
    {
        return [
            'hero_words_label' => 'Контролируйте сегодня:',
            'hero_words' => ['учеников', 'оплаты', 'посещаемость', 'филиалы', 'сертификаты', 'отчёты'],
            'scroll_hint' => 'Листайте вниз: один день внутри CRM',
            'marquee' => ['Ученики', 'Оплаты', 'Посещаемость', 'Группы', 'Лиды', 'Сертификаты', 'Telegram-бот', 'SMS', 'Филиалы', 'Отчёты', 'Зарплаты', 'Касса'],
            'pain' => [
                'title' => 'Всё ещё живёте в Excel и тетрадях?',
                'before_title' => 'Раньше',
                'after_title' => 'С CRM',
                'before' => ['Таблицы Excel и бумажные тетради', 'Должников приходится помнить самому', 'Посещаемость на бумаге, отчёт — в конце месяца', 'Заявки теряются в Telegram, Instagram и звонках'],
                'after' => ['Всё в одной системе', 'Должники видны сразу, напоминания уходят сами', 'Посещаемость с телефона, отчёты в реальном времени', 'Каждая заявка в воронке, с указанием источника'],
            ],
            'story' => [
                'eyebrow' => 'Один день в {n}',
                'title' => 'От заявки до отчёта — в едином потоке',
                'lead' => 'Листайте вниз и посмотрите, как проходит день вашего центра.',
                'scenes' => [
                    ['tag' => '1', 'title' => 'Приходит заявка', 'text' => 'Заявка поступает с формы сайта, из Instagram или по звонку. Оператор помещает её в воронку с указанием источника.', 'chip' => 'Новая заявка'],
                    ['tag' => '2', 'title' => 'Зачисление в группу', 'text' => 'В несколько кликов ученик зачислен в группу: курс, тариф, скидка и договор готовы.', 'chip' => 'Добавлен в группу'],
                    ['tag' => '3', 'title' => 'Отмечается посещаемость', 'text' => 'После урока преподаватель отмечает посещаемость с телефона. Отсутствующие видны сразу.', 'chip' => 'Посещаемость'],
                    ['tag' => '4', 'title' => 'Принимается оплата', 'text' => 'Оплата попадает в кассу, долг обновляется, а напоминание уходит в Telegram или по SMS.', 'chip' => 'Оплачено'],
                    ['tag' => '5', 'title' => 'Отчёт готов', 'text' => 'Директор видит доходы, долги и посещаемость в реальном времени. Ежедневный отчёт приходит в Telegram сам.', 'chip' => 'Отчёт'],
                ],
            ],
            'demo' => [
                'title' => 'Увидьте систему своими глазами',
                'text' => 'Запросите демо: мы свяжемся и покажем систему на примере вашего центра. Без обязательств.',
                'points' => ['Настроим и покажем под ваш центр', 'Ответим на вопросы вживую', 'Объясним, как импортировать старые списки'],
                'form' => ['name' => 'Ваше имя', 'phone' => 'Номер телефона', 'center' => 'Название центра (необязательно)', 'size' => 'Количество учеников (необязательно)', 'size_ph' => 'Выберите', 'message' => 'Вопрос или пожелание (необязательно)', 'submit' => 'Запросить демо', 'sending' => 'Отправляем…', 'privacy' => 'Отправляя форму, вы соглашаетесь с политикой конфиденциальности.', 'error' => 'Не удалось отправить. Попробуйте ещё раз чуть позже.'],
                'ok' => ['title' => 'Заявка принята!', 'text' => 'Скоро мы свяжемся с вами. Спасибо!'],
            ],
            'float_cta' => 'Запросить демо',
        ];
    }
}
