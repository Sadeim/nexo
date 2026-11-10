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

    // ---------- day picker (All Bookings) ----------

    /*
     * Jump to the chosen day as soon as one is picked.
     *
     * The field lives in a plain GET form, so without this it still works —
     * there is a submit button behind <noscript> and Enter submits. This only
     * removes the second gesture.
     */
    var dayJump = document.querySelector('[data-portal-dayjump]');

    if (dayJump && dayJump.form) {
        dayJump.addEventListener('change', function () {
            if (dayJump.value) {
                dayJump.form.submit();
            }
        });
    }

    // ---------- pull to refresh ----------

    // A touch gesture, so a phone affordance only.
    if (!isMobile) {
        return;
    }

    /*
     * ONLY on screens that also show a Refresh button, and never from a
     * touch that begins in a form.
     *
     * This gesture throws away whatever is on the screen. That is harmless on
     * a list of today's bookings and ruinous on a form: it was armed on every
     * mobile screen, including the password form, which never scrolls — so
     * window.scrollY was always 0, every touch armed it, and the downward
     * swipe an iPhone user makes to dismiss the keyboard and reach the Save
     * button reloaded the page and emptied all three fields. A barber spent
     * the afternoon of 9 Nov 2026 unable to get past it, with nothing in the
     * server logs, because the request was never made.
     *
     * So the gesture now has to be asked for twice over: the screen opts in
     * by carrying the Refresh control, and the touch has to start somewhere
     * that is not an input. A reload has to be something the barber wanted.
     */
    if (!refresh) {
        return;
    }

    var startY = null;

    window.addEventListener('touchstart', function (event) {
        var from = event.target;

        // closest() rather than a tagName check: the touch can land on a
        // label, or on padding inside the field's own wrapper.
        var inAForm = from && from.closest
            && from.closest('form, input, textarea, select, button');

        // Only when the page is already scrolled to the top, so it never
        // fights with normal scrolling.
        startY = (!inAForm && window.scrollY === 0)
            ? event.touches[0].clientY
            : null;
    }, { passive: true });

    window.addEventListener('touchmove', function (event) {
        if (startY === null) return;

        if (event.touches[0].clientY - startY > 90) {
            startY = null;
            window.location.reload();
        }
    }, { passive: true });
})();
