# Deploying nexobarbers.com

One command, from the app directory on the server:

```bash
bash /home/u171331366/domains/nexobarbers.com/nexo/deploy/deploy.sh
```

It logs to `storage/logs/deploy-<timestamp>.log` and prints HEAD before and
after, the migrations it ran, and a smoke check of eight URLs.

To know the maintenance bypass URL in advance — so you can keep viewing the
site while it is down for everyone else — supply the secret:

```bash
DEPLOY_SECRET=$(openssl rand -hex 16) bash .../deploy/deploy.sh
```

---

## First run on a server that does not have the script yet

`deploy.sh` lives in the repository, so it arrives by the very pull it
performs. To bootstrap it once:

```bash
git -C /home/u171331366/domains/nexobarbers.com/nexo pull --ff-only
```

After that the script is present and pulls subsequent versions itself. It
copies itself to `/tmp` and re-executes before doing anything, because bash
reads a script incrementally and a `git pull` that rewrites the running file
would otherwise resume at a byte offset that now means something else.

## The one thing to understand first

**This account hosts eighteen websites.** `ls ~/domains/` on 8 Nov 2026 showed:

```
brown-cormorant-523933.hostingersite.com   loarien.com          plutopayus.com
emanpal.com                                loarienhometrends.com qudssweet.com
eramgames.com                              mediumseagreen-…      sewaar.net
houseofcornedbeef.com                      nexobarbers.com       test-stages.com
jakesdeli.com                              noraacare.com         thehouseofcornedbeef.com
ketara.shop                                ozkban.com            ultimate.furniture
```

Only `nexobarbers.com` is ours. A command run from the wrong directory, or a
`--all-databases` dump, reaches someone else's live site.

**Rules the script follows, and you should too when working by hand:**

- Absolute paths everywhere. Never rely on `cd` persisting between commands.
- `composer`, `artisan` and cache commands run **only** inside
  `/home/u171331366/domains/nexobarbers.com/nexo`.
- Back up **nexo's database only** — read `DB_DATABASE` from nexo's `.env`.
  Never `--all-databases`.
- Don't touch another site's folder, `.env`, database, cron, `.htaccess` or PHP
  settings. Don't run `composer self-update`, change the account PHP version,
  or edit `~/.bashrc` / `~/.profile` / global composer.
- Don't restart services, kill processes, or change ownership/permissions
  outside the nexo app and `.../public_html/uploads`.
- The box runs hot (load 11–15 with other sites on it). Heavy commands run
  `nice`d and logged; never two at once.
- `~/.ssh/authorized_keys` is **account-wide** — a key added there reaches all
  eighteen sites, not just this one.

---

## The document root is not `public/`

Apache serves

```
/home/u171331366/domains/nexobarbers.com/public_html
```

which is a **sibling** of the app, not `nexo/public`. Laravel's `public/`
directory is never reached by a request. `public_html/index.php` is a
customised bootstrap pointing at `../nexo/bootstrap`.

The consequence is easy to miss and cost a morning of bookings on
**9 Nov 2026**: a static file committed to the repo is pulled into
`nexo/public/` and then **never served**. The barber-profiles release added
`public/frontend_assets/js/booking.js`, every rendered page carried its
`<script>` tag, and the file 404'd. The homepage returned a clean 200, the
modal markup was present and correct, and all five Book Now controls on the
site did nothing at all. Nothing in the logs; nothing in the smoke checks.

`deploy.sh` now mirrors the tracked contents of `public/` into the document
root on every deploy, and then asserts that every asset the homepage
*references* returns 200 — because a 200 on `/` says nothing about whether the
JavaScript that page depends on exists.

Five paths are never copied: `index.php` (the customised bootstrap —
overwriting it with the stock `public/index.php` took the site down on
6 Oct 2026), `.htaccess`, `robots.txt`, `favicon.ico`, and `uploads/`
(user data, not in the repo). There is no `--delete`.

If you add a static asset by hand, put it in **both** places, or just run the
deploy script.

### Static files are cached for seven days, by this host, with no revalidation

```
$ curl -sSD - -o /dev/null https://nexobarbers.com/portal_assets/css/portal.css
cache-control: public, max-age=604800
```

Nothing in the app sets that; hCDN does. It means **a deployed change to any
file under `public/` can be invisible for a week** to anyone who has already
loaded the old one — and that includes the portal's service worker, whose own
`fetch()` calls are answered from the same browser cache.

That is not theoretical. On 9 Nov 2026 the manager release shipped a new
`portal.css` and a worker version bump, and phones went on using the old
stylesheet anyway: the new worker installed and refilled its cache from the
HTTP cache.

The portal's own assets are therefore fingerprinted — `App\Support\Portal::asset()`
appends eight characters of the file's md5, so a changed file is a changed URL
and a miss in every cache at once. **Use it, not `asset()`, for anything under
`portal_assets/`.** A test asserts the rendered URLs match the files on disk.

Nothing else on the site does this yet. If you change a file under
`public/frontend_assets/` or `public/admin_assets/` and the live site does not
pick it up, this header is why, and a `?v=` on the reference is the fix.

The service worker belongs to the same story: it precaches only the icons,
fetches with `cache: 'reload'` / `'no-cache'` so it cannot inherit a stale
copy, and still caches no `/me` HTML or JSON, ever. See the comment at the top
of `public/portal_assets/sw.js`.

### The portal updates itself

An installed PWA has no address bar, no reload button and nothing to pull down
on. Whatever shell it registered on the day it was installed is the shell it
keeps — so the portal has to notice a new release on its own, and then ask.

How that works, end to end:

1. `deploy/write-build-version.sh` writes the pulled commit to
   `storage/app/build-version` (git-ignored, written per server).
2. `App\Support\BuildVersion` reads it, falling back to an md5 of everything
   under `public/portal_assets/` if the file is missing.
3. `/me/sw.js` is **served by a route**, not as a static file, and that route
   substitutes the commit for `__BUILD_VERSION__` in the source. Every deploy
   is therefore a different worker script, which is the only thing a browser
   looks at when deciding whether there is an update. It is sent
   `Cache-Control: no-cache, no-store, must-revalidate`, because this is the
   one file that must never be answered from the seven-day cache above.
4. `portal.js` registers with `updateViaCache: 'none'` and calls
   `registration.update()` on load and whenever the app returns to the
   foreground, at most once a minute.
5. The new worker installs and **waits** — `sw.js` deliberately does not call
   `skipWaiting()` on install. A bar appears at the bottom of the screen
   offering **Update**; tapping it posts `SKIP_WAITING`, and the page reloads
   when `controllerchange` fires.

**The portal never reloads itself.** Only the barber's tap does. The last time
a screen reloaded on its own, a pull-to-refresh gesture emptied the password
form every time an iPhone user reached for the keyboard, and it cost a barber
an afternoon.

To check it on the server:

```
curl -sSD - -o /dev/null https://nexobarbers.com/me/sw.js | grep -i cache-control
curl -sS https://nexobarbers.com/me/sw.js | grep "const VERSION"
cat /home/u171331366/domains/nexobarbers.com/nexo/storage/app/build-version
```

The version in the worker and the one in the file must match, and neither may
be `__BUILD_VERSION__`.

The manifest is served by a route for a related reason: the icon URLs inside
it need the same fingerprints the stylesheet gets, or a phone keeps last
week's app icon for a week. The JSON on disk is still the source of truth —
the route only rewrites the `src` of each icon.

### This host has no `/dev/fd`

So `while read ... done < <(some command)` — process substitution — **fails**,
under bash 4.4.20, in a plain script:

```
/tmp/script.sh: line 6: /dev/fd/62: No such file or directory
```

It fails by executing the loop body zero times, which is indistinguishable
from "there was nothing to do". The asset sync reads its file list from a
`mktemp` file for exactly this reason, and aborts loudly if that list is
empty rather than reporting success. Herestrings (`<<<`) do work.

---

## Before you deploy

**Back up nexo's database.** Not optional, and not in the script because it
should outlive a failed deploy:

```bash
cd /home/u171331366/domains/nexobarbers.com/nexo
val(){ grep -m1 "^$1=" .env | cut -d= -f2- | sed -e 's/^["'"'"']//' -e 's/["'"'"']$//'; }
mkdir -p ~/backups
MYSQL_PWD="$(val DB_PASSWORD)" mysqldump --single-transaction --quick \
  -u"$(val DB_USERNAME)" "$(val DB_DATABASE)" \
  > ~/backups/nexo-$(date -u +%Y%m%d-%H%M%S).sql
```

Note the `val()` helper: values in this `.env` are **double-quoted**, and a
naive `cut -d=` keeps the quotes and the password fails to authenticate.

If the dump is slow (it takes a while at load 14), run it under `nohup` and
poll — an SSH drop mid-dump leaves a 0-byte file that looks like a backup.
**Check the file size.** A real dump of this database is ~5.5 MB / 68 tables.

---

## What the script does, and why

| Step | Why |
|---|---|
| `php artisan down --secret=…` | See "the gap" below. The secret URL lets you view the live site while everyone else gets a 503. |
| `git pull --ff-only` | Refuses to merge. If it fails, someone committed on the server — stop and look. |
| `deploy/write-build-version.sh` | Writes the pulled commit to `storage/app/build-version`. The portal's service worker is named after it — see "The portal updates itself". Warns and continues if it fails. |
| `composer install` **only if `composer.lock` changed** | Most releases change no dependencies, and this box cannot resolve them anyway (see below). |
| `migrate --force` | Runs while down, deliberately. |
| `config:clear` / `route:clear` / `view:clear` | Stale compiled views and cached config survive a pull and will serve yesterday's code. |
| `config:cache` **only if it was cached before** | Matching the prior state. Caching config when it wasn't, or leaving it uncached when it was, is a behaviour change smuggled in by a deploy. |
| `php artisan up` | Also runs from an `EXIT`/`INT`/`TERM` trap, so a crashed deploy cannot leave the site in maintenance mode. |
| Smoke checks | Eleven URLs, including the portal's `sw.js`, its manifest and the `/app` short link; non-zero exit if any is wrong. |

### The gap between pull and migrate

**This is why the site goes down on purpose.**

On 8 Nov 2026 the barber-profiles deploy pulled code and ran migrations as two
separate steps with the site live. In between, every single page returned 500.
The cause: `AppServiceProvider` registers a `View::composer('*')` that calls
`Employee::publicBarbers()`, which filters on `employees.public_profile` — a
column the migration had not yet added. A global view composer makes **every
page** depend on the newest migration having run.

Two defences now:

1. The script puts the site in maintenance mode across pull + migrate.
2. That composer is cached and defended — see below — so a missing column
   degrades to an empty barber list and a logged warning, not a 500.

Keep both. The second is what saves you when someone deploys by hand.

### composer and `ext-sodium`

`composer install` on this box **fails**:

```
lcobucci/jwt 5.6.0 requires ext-sodium * -> it is missing from your system
```

PHP 8.3 CLI here has no sodium extension. `lcobucci/jwt` comes in via
`socialiteproviders/apple`, which this project does not actually use — no
social-login route is active.

It failed at dependency resolution, so it wrote nothing and `vendor/` is fine.
This is why the script skips composer unless `composer.lock` genuinely moved.

If you ever do need it:

- **Do not** reach for `--ignore-platform-req=ext-sodium` reflexively. Combined
  with `--no-dev` it will strip the dev packages currently installed, which is
  a real change with no upside on a release that didn't alter dependencies.
- The durable fixes are to enable `ext-sodium` for PHP 8.3 in hPanel, or to
  drop `socialiteproviders/apple` and `laravel/passport` from `composer.json`
  since neither has a live route.

---

## After deploying

The script's smoke checks cover nexo. **Also confirm no other site moved** —
capture this before and after and compare:

```bash
for d in $(ls ~/domains/ | grep -v '^public_html$'); do
  printf '%-50s %s\n' "$d" "$(curl -sS -o /dev/null -w '%{http_code}' -m 20 -L https://$d/)"
done
```

As of 8 Nov 2026, four already return `000` **before** any deploy:
`ketara.shop`, `noraacare.com`, `sewaar.net`, `ultimate.furniture`. That is the
baseline, not damage you caused.

---

## Never, on production

- `php artisan db:seed` — it overwrites live CMS content.
- `php artisan db:seed --class=BarberDemoSeeder` — invents three fake barbers
  and publishes them to the website.
- `migrate:fresh`, `migrate:refresh`, `migrate:reset` — they drop tables.
- `php artisan nexo-pos:reset-data` — wipes the POS ledger and payroll.

---

## If it goes wrong

**Site in maintenance mode after a failed run** — the trap should have handled
it; if not:

```bash
php /home/u171331366/domains/nexobarbers.com/nexo/artisan up
```

**Blank 500 with nothing in `storage/logs/laravel.log`** — the failure is
before Laravel boots far enough to log. Usually the front controller. Compare
it with the tracked copy and restore if it differs:

```bash
diff /home/u171331366/domains/nexobarbers.com/public_html/index.php \
     /home/u171331366/domains/nexobarbers.com/nexo/deploy/public_html-index.php
cp /home/u171331366/domains/nexobarbers.com/nexo/deploy/public_html-index.php \
   /home/u171331366/domains/nexobarbers.com/public_html/index.php
```

**500 on every page right after a deploy** — almost certainly code that is
ahead of its migration. Run `php artisan migrate --force`.

**Roll the database back:**

```bash
cd /home/u171331366/domains/nexobarbers.com/nexo
val(){ grep -m1 "^$1=" .env | cut -d= -f2- | sed -e 's/^["'"'"']//' -e 's/["'"'"']$//'; }
MYSQL_PWD="$(val DB_PASSWORD)" mysql -u"$(val DB_USERNAME)" "$(val DB_DATABASE)" \
  < ~/backups/nexo-<timestamp>.sql
```

**Roll the code back:** `git -C <app> reset --hard <old-sha>`, then re-run the
cache steps. Check whether the migrations you ran have a working `down()` before
relying on `migrate:rollback`.

---

## Facts worth not rediscovering

- **Document root is a sibling of the app.** `public_html/` is served;
  the app is in `nexo/`. `public_html/index.php` is therefore **outside the
  repository** and references `../nexo/vendor/autoload.php`. Overwriting it with
  `nexo/public/index.php` takes the whole site down silently — that happened on
  6 Oct 2026. The canonical copy is `deploy/public_html-index.php`.
- **Uploads already work.** `nexo/public/uploads` is a symlink **to**
  `public_html/uploads`, so `public_path('uploads/...')` lands in the served
  directory. No `storage:link`, no sync step.
- **MariaDB 11.8.9, `READ-COMMITTED` globally** — not MySQL, not REPEATABLE
  READ. InnoDB takes no gap locks on a ranged `SELECT … FOR UPDATE` here, which
  is why booking capacity is serialised by a named lock
  (`BookingSlotService::withSlotLock`) and not by row locks alone.
- **No `PDO::ATTR_PERSISTENT`** — each request gets its own connection, so a
  named lock cannot leak between requests.
- `shell_exec` is disabled, so interactive `php artisan tinker` is unavailable.
  Use `tinker --execute="…"` or a dedicated artisan command.
- SSH: `ssh sadeim` (alias in `~/.ssh/config`, `id_ed25519`). A plain
  `ssh u171331366@…` offers `id_rsa` because of the `Host *` block and will be
  refused — that is a local config quirk, not a server problem.
