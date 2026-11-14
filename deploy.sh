#!/usr/bin/env bash
# Serverda ishga tushirish:  cd ~/progects/atko_crm && bash deploy.sh
# Nima qiladi: kodni yangilaydi -> konteynerni qayta quradi -> migratsiya -> sog'liqni tekshiradi.
# Xato bo'lsa avvalgi versiyaga avtomatik qaytadi.
set -euo pipefail

APP_DIR="${APP_DIR:-$HOME/progects/atko_crm}"
STACK_DIR="${STACK_DIR:-$HOME/progects/stack}"
SERVICE="${SERVICE:-atko_crm}"
CONTAINER="${CONTAINER:-pg-atko-crm}"
URL="${URL:-http://localhost:7008}"

cd "$APP_DIR"
PREV="$(git rev-parse HEAD)"
echo "==> Hozirgi versiya: $PREV"

git fetch origin
git pull --ff-only origin main
NEW="$(git rev-parse HEAD)"
if [ "$PREV" = "$NEW" ]; then echo "==> Yangi o'zgarish yo'q (baribir qayta quriladi)"; fi

build() {
  (cd "$STACK_DIR" && docker compose up -d --build "$SERVICE")
}

healthy() {
  for i in $(seq 1 30); do
    code="$(curl -s -o /dev/null -w '%{http_code}' "$URL/up" || true)"
    [ "$code" = "200" ] && return 0
    sleep 2
  done
  return 1
}

rollback() {
  echo "!!! Xato. Avvalgi versiyaga qaytilmoqda: $PREV"
  git reset --hard "$PREV" || git reset "$PREV"
  build || true
  healthy && echo "==> Avvalgi versiya ishlayapti." || echo "!!! Avvalgi versiya ham javob bermayapti, qo'lda tekshiring."
  exit 1
}

echo "==> Konteyner qurilmoqda..."
build || rollback
healthy || rollback

echo "==> Migratsiya..."
docker exec "$CONTAINER" php artisan migrate --force || rollback
docker exec "$CONTAINER" php artisan optimize:clear
docker exec "$CONTAINER" php artisan queue:restart

echo "==> Tekshiruv: kirish sahifasi"
code="$(curl -s -o /dev/null -w '%{http_code}' "$URL/login" || true)"
[ "$code" = "200" ] || rollback

echo "==> Tayyor: $NEW"
docker exec "$CONTAINER" php artisan migrate:status | tail -3
