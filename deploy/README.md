# Deployment files that live outside this repository

Hostinger serves nexobarbers.com from `~/domains/nexobarbers.com/public_html`,
while the application itself sits beside it in `~/domains/nexobarbers.com/nexo`.
The front controller therefore lives outside this repository and nothing here
tracks it — which is how it was lost without anyone noticing.

## public_html-index.php

The canonical copy of `~/domains/nexobarbers.com/public_html/index.php`.

Its paths carry an extra `/nexo` segment because it sits one tree over from
the application. **Never restore it by copying `public/index.php` over it.**
That file is correct only inside `public/`; from the document root its
relative paths resolve one level too high, Composer's autoloader is not
found, and every request returns an empty HTTP 500 — before Laravel boots far
enough to log anything, so `storage/logs/laravel.log` stays silent and the
outage looks like it has no cause.

That happened on 6 October 2026 and took the whole site down: homepage, admin
and the POS API. Diagnosing it meant running the front controller by hand
with `display_errors=1`, because nothing else reported the failure.

To restore:

    cp deploy/public_html-index.php ~/domains/nexobarbers.com/public_html/index.php

Then confirm with `curl -sSo /dev/null -w '%{http_code}' https://nexobarbers.com/`.
