#!/usr/bin/env bash
# NovaCrm tashqi monitoring (watchdog).
# Boshqa kompyuterda (Ubuntu) ishlaydi: saytni tekshiradi, o'chib qolsa Telegram orqali admin(lar)ga xabar beradi.
# Cron: * * * * * /opt/edunova-watchdog/edunova-watchdog.sh >/dev/null 2>&1
set -u

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONF="${WATCHDOG_CONF:-$DIR/watchdog.env}"
[ -f "$CONF" ] || { echo "Sozlama fayli topilmadi: $CONF" >&2; exit 2; }
# shellcheck disable=SC1090
. "$CONF"

: "${SITE_URL:?SITE_URL kerak}"           # masalan https://temuriy-crm.uz/public
: "${BOT_TOKEN:?BOT_TOKEN kerak}"
: "${CHAT_IDS:?CHAT_IDS kerak}"           # bo'sh joy bilan ajratilgan Telegram ID lar
FAILS_BEFORE_ALERT="${FAILS_BEFORE_ALERT:-3}"     # ketma-ket nechta xatodan keyin xabar beriladi
REMINDER_MINUTES="${REMINDER_MINUTES:-60}"        # o'chiq turganda necha daqiqada eslatma
TIMEOUT="${TIMEOUT:-10}"
NAME="${NAME:-NovaCrm}"
STATE_DIR="${STATE_DIR:-$HOME/.edunova-watchdog}"
mkdir -p "$STATE_DIR"
STATE="$STATE_DIR/state"

now=$(date +%s)
fails=0; down_since=0; last_alert=0
# shellcheck disable=SC1090
[ -f "$STATE" ] && . "$STATE"

send() {
    local text="$1" id
    for id in $CHAT_IDS; do
        curl -s -m 15 -o /dev/null "https://api.telegram.org/bot${BOT_TOKEN}/sendMessage" \
            --data-urlencode "chat_id=${id}" --data-urlencode "text=${text}" || true
    done
}
save() { printf 'fails=%s\ndown_since=%s\nlast_alert=%s\n' "$fails" "$down_since" "$last_alert" > "$STATE"; }

code=$(curl -s -m "$TIMEOUT" -o /dev/null -w '%{http_code}' -L "${SITE_URL%/}/up" 2>/dev/null || echo 000)

if [ "$code" = "200" ]; then
    if [ "$down_since" -gt 0 ]; then
        mins=$(( (now - down_since) / 60 ))
        send "🟢 ${NAME} qayta ishga tushdi.
O'chiq turgan vaqt: ~${mins} daqiqa.
$(date '+%d.%m.%Y %H:%M')"
    fi
    fails=0; down_since=0; last_alert=0; save
    # Ilovaga "monitoring tirik" signalini yuborish (Tizim holatida ko'rinadi); ixtiyoriy
    if [ -n "${BEAT_KEY:-}" ]; then
        curl -s -m "$TIMEOUT" -o /dev/null -G "${SITE_URL%/}/monitor/beat" --data-urlencode "key=${BEAT_KEY}" || true
    fi
    exit 0
fi

fails=$((fails + 1))
if [ "$fails" -ge "$FAILS_BEFORE_ALERT" ]; then
    if [ "$down_since" -eq 0 ]; then
        down_since=$now; last_alert=$now
        send "🔴 ${NAME} javob bermayapti!
Manzil: ${SITE_URL}
HTTP holati: ${code}
Vaqt: $(date '+%d.%m.%Y %H:%M')
Serverni tekshiring (internet, elektr, Apache/Nginx, PHP)."
    elif [ $(( (now - last_alert) / 60 )) -ge "$REMINDER_MINUTES" ]; then
        last_alert=$now
        send "🔴 ${NAME} hali ham o'chiq: ~$(( (now - down_since) / 60 )) daqiqa (HTTP ${code})."
    fi
fi
save
