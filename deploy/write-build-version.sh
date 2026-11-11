#!/usr/bin/env bash
#
# Write the build id the portal's service worker is named after.
#
# One line, the deployed commit, into storage/app/build-version. App\Support\
# BuildVersion reads it and PortalController::serviceWorker stamps it into
# sw.js, which is what makes every deploy a new worker and so a new shell on
# every installed phone. See RUNBOOK.md, "The portal updates itself".
#
# Its own file rather than four lines inside deploy.sh so it can be run by
# hand — and tested — without running a deploy.
#
# Usage:  bash deploy/write-build-version.sh [APP_DIR]
#
set -euo pipefail

APP_DIR=${1:-$(cd "$(dirname "$0")/.." && pwd)}

[ -d "$APP_DIR" ] || { echo "write-build-version: $APP_DIR is not a directory" >&2; exit 1; }

OUT=$APP_DIR/storage/app/build-version

# The commit, when there is one. Twelve characters: long enough not to collide
# on a repository this size, short enough to read in a cache name.
VERSION=$(git -C "$APP_DIR" rev-parse --short=12 HEAD 2>/dev/null || true)

# No git — a tarball deploy, or a checkout without its history. A timestamp is
# a worse id than a commit (two servers deployed from the same code disagree)
# but it is still a different id per deploy, which is the whole job.
[ -n "$VERSION" ] || VERSION=$(date -u +%Y%m%d%H%M%S)

# Only ever characters that survive being interpolated into a JavaScript
# string and a cache name. BuildVersion sanitises this again on the way out;
# doing it here too means what is on disk is readable by a human debugging it.
VERSION=$(printf '%s' "$VERSION" | tr -cd 'A-Za-z0-9' | cut -c1-40)

[ -n "$VERSION" ] || { echo "write-build-version: could not determine a version" >&2; exit 1; }

mkdir -p "$(dirname "$OUT")"

# Through a temp file in the same directory, then moved into place: a request
# that arrives mid-write must read the old version or the new one, never half
# of each.
TMP=$(mktemp "$(dirname "$OUT")/.build-version.XXXXXX")
printf '%s\n' "$VERSION" > "$TMP"
chmod 644 "$TMP"
mv -f "$TMP" "$OUT"

printf '%s\n' "$VERSION"
