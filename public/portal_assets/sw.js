/*
 * Portal service worker — shell only.
 *
 * THE RULE: this worker never caches a /me response. Not the HTML, not JSON,
 * not a redirect. Every page behind /me is one barber's own schedule, and a
 * cached copy is a copy that can be served to whoever holds the phone next —
 * or to the same phone after the barber has been deactivated. The portal is
 * small and the network is the only source of truth for it.
 *
 * What is cached is the shell: stylesheet, script, icons, manifest. Those are
 * identical for everyone and contain nobody's data.
 *
 * Served from /me/sw.js so its scope covers /me (a worker at
 * /portal_assets/sw.js could only control /portal_assets/), which is also what
 * Chrome needs before it will treat /me as installable.
 */

/*
 * The build this worker belongs to.
 *
 * __BUILD_VERSION__ is a placeholder. The file is served by a Laravel route
 * (App\Http\Controllers\Portal\PortalController::serviceWorker), and that
 * route substitutes the deployed commit before the bytes leave the server —
 * so the static file on disk is a template, and what a phone actually
 * registers is one worker per build.
 *
 * That substitution is what makes auto-update work at all. A browser decides
 * whether to install a new worker by comparing the script byte for byte with
 * the one it has; a worker that never changes is never new, however much of
 * the portal around it did. Carrying the commit means every deploy is a
 * different file, an install, a waiting worker, and a prompt on the phone.
 */
const VERSION = '__BUILD_VERSION__';

/*
 * One cache per build, which is what lets activate() throw the last build's
 * away wholesale.
 *
 * The fingerprinted URLs (App\Support\Asset) already make a changed file a
 * changed URL, so nothing stale is ever SERVED from here. What the per-build
 * name buys is that nothing stale is KEPT: without it a phone accumulates
 * every stylesheet the portal has ever had.
 */
const CACHE = 'nexo-portal-' + VERSION;

/*
 * Precached: the icons, which an installed app wants on hand and which are
 * the only shell files whose URL does not change when they do.
 *
 * The stylesheet, the script and the manifest are deliberately NOT here. They
 * arrive fingerprinted, so precaching them from this fixed list would cache
 * a URL the page never asks for.
 */
const SHELL = [
    '/portal_assets/icons/icon-192.png',
    '/portal_assets/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE)
            // Individually, so one missing file cannot fail the whole install
            // and leave the portal with no worker at all.
            //
            // 'reload' bypasses the browser's own HTTP cache. Without it this
            // fetch is served from the same seven-day cache entry everything
            // else is, and a fresh install faithfully copies a stale file.
            .then((cache) => Promise.all(
                SHELL.map((url) => cache
                    .add(new Request(url, { cache: 'reload' }))
                    .catch(() => null))
            ))
    );
});

/*
 * Deliberately NO skipWaiting() in install.
 *
 * A worker that skips waiting takes over the page the moment it finishes
 * installing, which means the portal can change under a barber mid-screen —
 * and with the reload that follows, mid-form. So the new worker installs,
 * sits in `waiting`, and portal.js offers an Update button. Taking over is
 * the barber's decision, and this is the message that carries it.
 *
 * (A first-ever install still activates immediately: there is no controller
 * to wait behind, so nothing is interrupted.)
 */
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

/*
 * Every cache but this build's goes, and the worker takes the open pages.
 *
 * clients.claim() is what turns the barber's tap into a visible change: it
 * fires controllerchange in the page, which is portal.js's cue to reload
 * once. Without it the new worker would control nothing until the next
 * navigation, and the Update button would appear to do nothing.
 */
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Anything that is not a GET of a shell asset on this origin goes straight
    // to the network and is never written to the cache. That covers every /me
    // page and every form post, which is the point.
    const isShell =
        request.method === 'GET' &&
        url.origin === self.location.origin &&
        url.pathname.startsWith('/portal_assets/');

    if (!isShell) {
        return; // no respondWith: the browser handles it normally
    }

    /*
     * Stale while revalidate: answer from the cache if there is anything
     * there, and ask the network every time regardless.
     *
     * Not cache-first. A hit that is never checked is a file the phone keeps
     * forever, and the portal has no offline mode to pay for that with — no
     * /me HTML is ever cached, so the network is required to see a single
     * screen anyway. The cache buys a fast first paint and a shell that
     * survives a flaky connection; it must not buy last month's stylesheet.
     *
     * 'no-cache' on the revalidation, for the same reason as on install: a
     * plain fetch() here is answered by the browser's seven-day HTTP cache
     * and revalidates nothing. This makes it a conditional request to the
     * origin, which is a 304 and a few bytes when nothing has changed.
     *
     * Both waitUntil() and respondWith() are called straight from the handler,
     * never from inside a then(): called later they throw, and the update
     * would silently stop happening.
     */
    const fresh = fetch(new Request(request.url, { cache: 'no-cache' }))
        .then((response) => {
            // Only store a clean same-origin 200.
            if (response && response.ok && response.type === 'basic') {
                const copy = response.clone();
                caches.open(CACHE).then((cache) => cache.put(request, copy));
            }

            return response;
        })
        .catch(() => null);

    // Keeps the worker alive long enough to finish writing the update even
    // when the cached copy was served instantly.
    event.waitUntil(fresh);

    event.respondWith(
        caches.match(request).then(
            (hit) => hit || fresh.then((response) => response || Response.error())
        )
    );
});
