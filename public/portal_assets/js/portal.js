/*
 * Portal shell behaviour. Deliberately tiny — the portal is server-rendered
 * and read-only in this phase, so there is no state here to get out of sync.
 */
(function () {
    'use strict';

    // ---------- service worker ----------

    // Phones only. /work is a desk browser: it is not installable, has no
    // manifest, and must not quietly register a worker for the other version
    // of the portal just because it loads the same script.
    var isMobile = document.body.classList.contains('v-mobile');

    if (isMobile && 'serviceWorker' in navigator) {
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

    // A touch gesture, so a phone affordance only.
    if (!isMobile) {
        return;
    }

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
