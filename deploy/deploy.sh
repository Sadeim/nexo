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
#         DEPLOY_SECRET=<hex> bash .../deploy.sh   # to fix the bypass URL
#
set -euo pipefail

# Run from a copy, always.
#
# This script does `git pull`, and the pull can change THIS FILE. Bash reads a
# script incrementally as it executes, so a file that changes underneath it
# resumes at a byte offset that now means something else — silently running
# half of one version and half of another. Copying to /tmp and re-executing
# from there makes the running script immutable for the duration.
if [ "${DEPLOY_DETACHED:-0}" != "1" ]; then
    SELF_COPY=$(mktemp /tmp/nexo-deploy-XXXXXX.sh)
    cp "$0" "$SELF_COPY"
    chmod +x "$SELF_COPY"
    export DEPLOY_DETACHED=1
    bash "$SELF_COPY" "$@"
    STATUS=$?
    rm -f "$SELF_COPY"
    exit $STATUS
fi

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

# Honour a secret supplied by the operator, so the bypass URL can be known
# and shared before the site goes down rather than scraped from the output.
SECRET=${DEPLOY_SECRET:-$(openssl rand -hex 16 2>/dev/null || date +%s%N | sha256sum | cut -c1-32)}
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

# ----------------------------------------------------------------- assets ----

# The document root is a SIBLING of the app, not $APP_DIR/public. Apache
# serves $DOCROOT and never reaches Laravel's public/ directory, so a static
# file added to the repo gets pulled into public/ and is then silently never
# served. That is how frontend_assets/js/booking.js 404'd for a day while the
# script tag sat in every rendered page and all five Book Now buttons on the
# site did nothing.
#
# Mirror the tracked contents of public/ into the document root. The file
# list comes from `git ls-files`, so an asset a future branch adds is carried
# over without anyone remembering to edit this script.
#
# Five things are deliberately never copied:
#
#   index.php    the document root's copy is customised for this split layout
#                and points at ../nexo/bootstrap. Overwriting it with the
#                stock public/index.php took the site down on 6 Oct 2026.
#   .htaccess    hand-tuned on the host.
#   robots.txt   maintained on the host.
#   favicon.ico  maintained on the host.
#   uploads/     user data. Not in the repo, never mirrored, never removed.
#
# There is no --delete. On an account hosting eighteen sites, a sync that can
# remove files is a sync that can remove someone's uploads.

# The file list goes through a temp file, NOT `done < <(git ls-files)`. This
# host has no /dev/fd, so process substitution fails outright — and it fails
# by running the loop zero times, which looks exactly like "nothing to copy".
# A silent no-op here is the bug this whole section exists to prevent.
say "Assets"
ASSET_LIST=$(mktemp /tmp/nexo-assets.XXXXXX) || { echo "FATAL: cannot create temp file"; exit 1; }
git -C "$APP_DIR" ls-files -z public/ > "$ASSET_LIST"

if [ ! -s "$ASSET_LIST" ]; then
    echo "  FATAL: git listed no files under public/ — refusing to continue" | tee -a "$LOG"
    rm -f "$ASSET_LIST"
    exit 1
fi

ASSETS_COPIED=0
ASSETS_SEEN=0
while IFS= read -r -d '' tracked; do
    rel=${tracked#public/}
    ASSETS_SEEN=$((ASSETS_SEEN + 1))
    case "$rel" in
        index.php|.htaccess|robots.txt|favicon.ico|uploads/*|downloads/*) continue ;;
    esac

    src=$APP_DIR/$tracked
    dst=$DOCROOT/$rel

    [ -f "$src" ] || continue
    if [ ! -f "$dst" ] || ! cmp -s "$src" "$dst"; then
        mkdir -p "$(dirname "$dst")"
        cp -p "$src" "$dst"
        printf '  copy  %s\n' "$rel" | tee -a "$LOG"
        ASSETS_COPIED=$((ASSETS_COPIED + 1))
    fi
done < "$ASSET_LIST"
rm -f "$ASSET_LIST"

if [ "$ASSETS_SEEN" = "0" ]; then
    echo "  FATAL: read nothing from the file list — not copying blind" | tee -a "$LOG"
    exit 1
fi

if [ "$ASSETS_COPIED" = "0" ]; then
    echo "  $ASSETS_SEEN tracked, document root already matches — nothing to copy" | tee -a "$LOG"
else
    echo "  $ASSETS_SEEN tracked, $ASSETS_COPIED copied into $DOCROOT" | tee -a "$LOG"
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

# A 200 on "/" proves the page renders, not that it works. The outage on
# 9 Nov 2026 served a perfect 200 homepage whose booking script was a 404,
# so every Book Now button was inert. Check what the page actually pulls in.
say "Referenced assets"
HOME_HTML=$(curl -sS -m 25 "$SITE/" || true)
ASSET_URLS=$(printf '%s' "$HOME_HTML" \
    | grep -oE '(src|href)="[^"]+\.(js|css)(\?[^"]*)?"' \
    | sed -E 's/^(src|href)="//; s/"$//' \
    | sort -u)

if [ -z "$ASSET_URLS" ]; then
    echo "  FAIL  homepage references no js/css at all" | tee -a "$LOG"
    FAILED=1
else
    while IFS= read -r url; do
        [ -n "$url" ] || continue
        case "$url" in
            "$SITE"/*) check "${url#"$SITE"}" 200 ;;
            /*)        check "$url" 200 ;;
            *)         printf '  skip  %-44s (third party)\n' "$url" | tee -a "$LOG" ;;
        esac
    done <<< "$ASSET_URLS"
fi

# The booking modal and its script are the whole booking funnel. Assert the
# contract the page depends on, not just that the file exists.
say "Booking funnel"
for needle in 'id="booking-modal"' 'frontend_assets/js/booking.js' 'book-now-btn'; do
    if printf '%s' "$HOME_HTML" | grep -qF "$needle"; then
        printf '  ok    homepage contains %s\n' "$needle" | tee -a "$LOG"
    else
        printf '  FAIL  homepage is missing %s\n' "$needle" | tee -a "$LOG"
        FAILED=1
    fi
done

say "Summary"
echo "  $HEAD_BEFORE -> $HEAD_AFTER" | tee -a "$LOG"
if [ "$FAILED" = "0" ]; then
    echo "  all smoke checks passed" | tee -a "$LOG"
else
    echo "  SMOKE CHECKS FAILED — see $LOG and RUNBOOK.md 'If it goes wrong'" | tee -a "$LOG"
    exit 1
fi
