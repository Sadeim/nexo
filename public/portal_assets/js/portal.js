/*
 * Portal shell behaviour. Deliberately tiny — the portal is server-rendered
 * and read-only in this phase, so there is no state here to get out of sync.
 */
(function () {
    'use strict';

    // ---------- service worker ----------

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            // Registered at /me/sw.js so its scope covers the portal.
            navigator.serviceWorker.register('/me/sw.js', { scope: '/me/' })
                .catch(function () { /* no worker is survivable; the portal still works */ });
        });
    }

    // ---------- refresh ----------

    var refresh = document.querySelector('[data-portal-refresh]');

    if (refresh) {
        refresh.addEventListener('click', function () {
            refresh.setAttribute('disabled', 'disabled');
            window.location.reload();
        });
    }

    // ---------- pull to refresh ----------

    // Only when the page is already scrolled to the top, so it never fights
    // with normal scrolling.
    var startY = null;

    window.addEventListener('touchstart', function (event) {
        startY = window.scrollY === 0 ? event.touches[0].clientY : null;
    }, { passive: true });

    window.addEventListener('touchmove', function (event) {
        if (startY === null) return;

        if (event.touches[0].clientY - startY > 90) {
            startY = null;
            window.location.reload();
        }
    }, { passive: true });
})();
