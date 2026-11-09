#!/usr/bin/env bash
#
# Deploy nexobarbers.com.
#
# Scoped to ONE site on an account that hosts eighteen. Every path below is
# absolute and under nexobarbers.com; nothing else on the box is read or
# written. See RUNBOOK.md for why each step is here.
#
# Usage:  bash /home/u171331366/domains/nexobarbers.com/nexo/deploy/deploy.sh
#
set -euo pipefail

APP_DIR=/home/u171331366/domains/nexobarbers.com/nexo
DOCROOT=/home/u171331366/domains/nexobarbers.com/public_html
SITE=https://nexobarbers.com
LOG=$APP_DIR/storage/logs/deploy-$(date -u +%Y%m%d-%H%M%S).log

say() { printf '\n== %s\n' "$*" | tee -a "$LOG"; }
run() { "$@" 2>&1 | tee -a "$LOG"; }

# ---------------------------------------------------------------- guards ----

[ -d "$APP_DIR" ]       || { echo "FATAL: $APP_DIR missing"; exit 1; }
[ -f "$APP_DIR/artisan" ] || { echo "FATAL: no artisan in $APP_DIR — wrong directory"; exit 1; }
cd "$APP_DIR"

# Never let a half-finished deploy leave the site in maintenance mode. This
# fires on any error or interrupt, not only a clean exit.
MAINTENANCE_ON=0
bring_back_up() {
    if [ "$MAINTENANCE_ON" = "1" ]; then
        echo "--- restoring service (php artisan up) ---" | tee -a "$LOG"
        php "$APP_DIR/artisan" up 2>&1 | tee -a "$LOG" || true
    fi
}
trap bring_back_up EXIT INT TERM

say "Target"
echo "  app:     $APP_DIR"      | tee -a "$LOG"
echo "  docroot: $DOCROOT"      | tee -a "$LOG"
echo "  log:     $LOG"          | tee -a "$LOG"
echo "  load:    $(uptime | sed 's/.*average: //')" | tee -a "$LOG"

# Show which database we are about to migrate BEFORE touching it. On a shared
# account the cost of being in the wrong directory is someone else's data.
say "Database (confirm this is nexo before continuing)"
run php "$APP_DIR/artisan" db:show --json 2>/dev/null \
  | tr ',' '\n' | grep -iE '"(database|host)"' || run php "$APP_DIR/artisan" db:show

# ------------------------------------------------------------ maintenance ----

SECRET=$(openssl rand -hex 16 2>/dev/null || date +%s%N | sha256sum | cut -c1-32)
say "Maintenance mode"
run php "$APP_DIR/artisan" down --secret="$SECRET" --render="errors::503"
MAINTENANCE_ON=1
echo "  bypass: $SITE/$SECRET" | tee -a "$LOG"

# ------------------------------------------------------------------ code ----

HEAD_BEFORE=$(git -C "$APP_DIR" rev-parse --short HEAD)
LOCK_BEFORE=$(md5sum "$APP_DIR/composer.lock" 2>/dev/null | cut -d' ' -f1 || echo none)
CONFIG_CACHED_BEFORE=0
[ -f "$APP_DIR/bootstrap/cache/config.php" ] && CONFIG_CACHED_BEFORE=1

say "Pulling"
echo "  HEAD before: $HEAD_BEFORE" | tee -a "$LOG"
run git -C "$APP_DIR" pull --ff-only
HEAD_AFTER=$(git -C "$APP_DIR" rev-parse --short HEAD)
echo "  HEAD after:  $HEAD_AFTER" | tee -a "$LOG"

if [ "$HEAD_BEFORE" = "$HEAD_AFTER" ]; then
    echo "  (nothing new — continuing anyway so caches and migrations are reconciled)" | tee -a "$LOG"
fi

# -------------------------------------------------------------- composer ----

# Only when the lock file actually moved. Dependencies are unchanged in most
# releases, and this box cannot resolve them anyway: PHP 8.3 CLI here has no
# ext-sodium, which lcobucci/jwt requires, so a needless `composer install`
# fails loudly and wastes ten minutes on a machine already at load 14.
LOCK_AFTER=$(md5sum "$APP_DIR/composer.lock" 2>/dev/null | cut -d' ' -f1 || echo none)

if [ "$LOCK_BEFORE" != "$LOCK_AFTER" ]; then
    say "composer.lock changed — installing (nice, backgrounded)"
    CLOG=$APP_DIR/storage/logs/deploy-composer-$(date -u +%Y%m%d-%H%M%S).log
    nohup nice -n 10 composer install --no-dev --optimize-autoloader --no-interaction \
        > "$CLOG" 2>&1 &
    CPID=$!
    echo "  pid $CPID, log $CLOG" | tee -a "$LOG"
    wait "$CPID" && CSTATUS=0 || CSTATUS=$?
    tail -15 "$CLOG" | tee -a "$LOG"
    if [ "$CSTATUS" != "0" ]; then
        echo "  composer FAILED (exit $CSTATUS) — stopping before migrate." | tee -a "$LOG"
        echo "  If it is the ext-sodium platform gap, see RUNBOOK.md." | tee -a "$LOG"
        exit "$CSTATUS"
    fi
else
    say "composer.lock unchanged — skipping composer install"
fi

# ------------------------------------------------------------- migrations ----

# Runs while the site is down, on purpose. Code that reads a column added by
# the migration it ships with will 500 on every request in the gap between
# pull and migrate — which is exactly what happened on 8 Nov 2026.
say "Migrations"
run php "$APP_DIR/artisan" migrate --force

# ------------------------------------------------------------------ cache ----

say "Caches"
run php "$APP_DIR/artisan" config:clear
run php "$APP_DIR/artisan" route:clear
run php "$APP_DIR/artisan" view:clear

if [ "$CONFIG_CACHED_BEFORE" = "1" ]; then
    echo "  config was cached before — restoring that state" | tee -a "$LOG"
    run php "$APP_DIR/artisan" config:cache
else
    echo "  config was NOT cached before — leaving it uncached" | tee -a "$LOG"
fi

# ------------------------------------------------------------------- live ----

say "Back up"
run php "$APP_DIR/artisan" up
MAINTENANCE_ON=0

# ------------------------------------------------------------------ smoke ----

say "Smoke checks"
FAILED=0
check() {
    local path="$1" want="$2"
    local got
    got=$(curl -sS -o /dev/null -w '%{http_code}' -m 25 "$SITE$path" || echo ERR)
    if [ "$got" = "$want" ]; then
        printf '  ok    %-44s %s\n' "$path" "$got" | tee -a "$LOG"
    else
        printf '  FAIL  %-44s %s (wanted %s)\n' "$path" "$got" "$want" | tee -a "$LOG"
        FAILED=1
    fi
}

check "/"                                        200
check "/service"                                 200
check "/gallery"                                 200
check "/contact_us"                              200
check "/admin/login"                             200
check "/barbers/definitely-not-a-barber"         404
check "/bookings/available-slots?date=$(date -u +%Y-%m-%d)" 200
check "/api/pos/v1/health"                       200

say "Summary"
echo "  $HEAD_BEFORE -> $HEAD_AFTER" | tee -a "$LOG"
if [ "$FAILED" = "0" ]; then
    echo "  all smoke checks passed" | tee -a "$LOG"
else
    echo "  SMOKE CHECKS FAILED — see $LOG and RUNBOOK.md 'If it goes wrong'" | tee -a "$LOG"
    exit 1
fi
