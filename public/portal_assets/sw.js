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

const CACHE = 'nexo-portal-shell-v1';

const SHELL = [
    '/portal_assets/css/portal.css',
    '/portal_assets/js/portal.js',
    '/portal_assets/manifest.webmanifest',
    '/portal_assets/icons/icon-192.png',
    '/portal_assets/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE)
            // Individually, so one missing file cannot fail the whole install
            // and leave the portal with no worker at all.
            .then((cache) => Promise.all(
                SHELL.map((url) => cache.add(url).catch(() => null))
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

    event.respondWith(
        caches.match(request).then((hit) => {
            if (hit) {
                return hit;
            }

            return fetch(request).then((response) => {
                // Only store a clean same-origin 200.
                if (response && response.ok && response.type === 'basic') {
                    const copy = response.clone();
                    caches.open(CACHE).then((cache) => cache.put(request, copy));
                }

                return response;
            });
        })
    );
});
