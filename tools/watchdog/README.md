# Edunova tashqi monitoring

Sayt serveri o'chib qolsa, uning o'zi xabar yubora olmaydi. Bu skript **boshqa** kompyuterda (ofis yoki uydagi Ubuntu) ishlaydi.

## O'rnatish (Ubuntu)

```bash
sudo mkdir -p /opt/edunova-watchdog
sudo cp edunova-watchdog.sh watchdog.env.example /opt/edunova-watchdog/
cd /opt/edunova-watchdog
sudo cp watchdog.env.example watchdog.env && sudo nano watchdog.env   # SITE_URL, BOT_TOKEN, CHAT_IDS
sudo chmod +x edunova-watchdog.sh && sudo chmod 600 watchdog.env
./edunova-watchdog.sh && echo "xatosiz"        # sinov
(crontab -l 2>/dev/null; echo '* * * * * /opt/edunova-watchdog/edunova-watchdog.sh >/dev/null 2>&1') | crontab -
```

## Qanday ishlaydi
- Har daqiqa `SITE_URL/up` ni tekshiradi (Laravel health manzili).
- 3 marta ketma-ket javob bermasa — Telegram'ga «🔴 javob bermayapti».
- O'chiq turganda har 60 daqiqada eslatma, tiklanganda «🟢 qayta ishga tushdi, ~X daqiqa».
- `BEAT_KEY` berilsa, har muvaffaqiyatli tekshiruvda ilovaga signal yuboradi; signal 10 daqiqadan ortiq kelmasa, **Tizim holati** sahifasi va Telegram «monitoring o'chib qolgan» deb ogohlantiradi (ya'ni ikki tomonlama kuzatuv).
- Holat `~/.edunova-watchdog/state` faylida saqlanadi.

## Sinov
`SITE_URL` ni noto'g'ri manzilga o'zgartirib 3 daqiqa kuting, xabar kelishi kerak; so'ng to'g'rilang.
