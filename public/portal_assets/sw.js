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
 * The cache's version. activate() deletes every cache that is not this one,
 * so changing the name is what evicts the files cached under the old one.
 *
 * It is no longer the thing that keeps the portal current, and it must not be
 * relied on for that again: the stylesheet and the script carry a fingerprint
 * of their own contents in their URL (App\Support\Portal::asset), so a change
 * to either is a different URL and a miss in every cache there is. This name
 * is only for evicting what an older worker left behind.
 */
const CACHE = 'nexo-portal-shell-v3';

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
            .then(() => self.skipWaiting())
    );
});

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
