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

    /*
     * Keeping an installed portal current.
     *
     * An installed PWA has no address bar and no reload button. Whatever
     * shell it registered on the day it was installed is the shell it keeps,
     * and the barber has no way to ask for a newer one — there is nothing to
     * pull down on and nothing to type. So the page has to notice for itself.
     *
     * The handshake, end to end:
     *
     *   1. sw.js carries the deployed commit (stamped in by the route), so
     *      every release is a different file and therefore a new worker.
     *   2. update() asks the server for it — on load, and again whenever the
     *      portal comes back to the foreground, which on a phone is far more
     *      often than a page load.
     *   3. The new worker installs and then WAITS, because sw.js no longer
     *      calls skipWaiting() on install. Nothing has changed underfoot yet.
     *   4. A bar appears at the bottom offering Update.
     *   5. Tapping it posts SKIP_WAITING; the worker activates, claims the
     *      page, and controllerchange fires.
     *   6. Only then does the page reload.
     *
     * Steps 3 to 6 exist entirely so that the portal never reloads itself
     * underneath someone. The last time it did — a pull-to-refresh armed on
     * every screen — a barber lost an afternoon to a password form that
     * emptied itself every time he reached for the keyboard. A reload throws
     * away whatever is on the screen, so it happens when it is asked for.
     */
    var registration = null;
    var toast = null;
    var lastChecked = 0;
    var wanted = false;
    var reloaded = false;

    if (isMobile && 'serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            /*
             * updateViaCache: 'none' keeps the browser's HTTP cache out of
             * the update check. Without it this host's seven-day
             * Cache-Control answers the check with the worker the phone
             * already has, and the portal concludes it is up to date for a
             * week. The route sends no-store from its end as well.
             */
            navigator.serviceWorker.register('/me/sw.js', { scope: '/me', updateViaCache: 'none' })
                .then(function (reg) {
                    retireTheOldScope();
                    watchFor(reg);
                })
                .catch(function () { /* no worker is survivable; the portal still works */ });
        });

        navigator.serviceWorker.addEventListener('controllerchange', function () {
            /*
             * Only when the barber asked for it.
             *
             * The controller also changes when the very first worker claims
             * the page, and when another tab takes the update. Reloading on
             * either would be the screen emptying itself for a reason nobody
             * in front of it can see — which is the whole thing this design
             * is avoiding.
             */
            if (!wanted || reloaded) {
                return;
            }

            reloaded = true;
            window.location.reload();
        });
    }

    /*
     * Get rid of the worker that used to be registered at '/me/'.
     *
     * A scope is a plain string prefix, and '/me/' does not match '/me' — so
     * the one screen a barber actually sits on, which is also the address the
     * installed app starts at, was never controlled by the worker at all. No
     * update was ever offered there and the shell cache did nothing for it.
     * The scope is now '/me'.
     *
     * Registering a different scope does not replace the old registration, it
     * adds a second one, and a browser picks the LONGEST matching scope — so
     * left alone, '/me/' would go on controlling every sub-screen while the
     * new worker controlled exactly one page. The old one has to be told to
     * go, and it goes quietly: unregister() takes effect once nothing is
     * controlled by it, so nothing on screen changes now.
     *
     * This is migration code. It can be deleted once every phone that had
     * the portal installed before 11 Nov 2026 has opened it once.
     */
    function retireTheOldScope() {
        if (!navigator.serviceWorker.getRegistrations) {
            return;
        }

        navigator.serviceWorker.getRegistrations()
            .then(function (all) {
                all.forEach(function (one) {
                    if (one.scope === window.location.origin + '/me/') {
                        one.unregister();
                    }
                });
            })
            .catch(function () { /* nothing to retire, or not allowed to ask */ });
    }

    function watchFor(reg) {
        if (!reg) {
            return;
        }

        registration = reg;

        // One left waiting from a previous visit — the barber closed the app
        // before tapping Update, and it has been sitting there since.
        if (reg.waiting && navigator.serviceWorker.controller) {
            offerUpdate(reg.waiting);
        }

        reg.addEventListener('updatefound', function () {
            var incoming = reg.installing;

            if (!incoming) {
                return;
            }

            incoming.addEventListener('statechange', function () {
                /*
                 * 'installed' while something is already controlling the page
                 * means this one is waiting behind it. Installed with NO
                 * controller is the first worker this phone has ever had:
                 * it activates on its own and there is nothing to offer.
                 */
                if (incoming.state === 'installed' && navigator.serviceWorker.controller) {
                    offerUpdate(incoming);
                }
            });
        });

        checkForUpdate();

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                checkForUpdate();
            }
        });
    }

    function checkForUpdate() {
        if (!registration) {
            return;
        }

        /*
         * At most once a minute. An installed app is foregrounded and
         * backgrounded all day — between customers, every time the phone
         * locks — and without a floor each of those is a request.
         */
        var now = Date.now();

        if (now - lastChecked < 60000) {
            return;
        }

        lastChecked = now;

        registration.update().catch(function () { /* offline, or no change */ });
    }

    function offerUpdate(worker) {
        if (toast || !worker) {
            return;
        }

        toast = document.createElement('div');
        toast.className = 'p-update';
        // Announced rather than alerted: it is an offer, not an interruption.
        toast.setAttribute('role', 'status');

        var label = document.createElement('span');
        label.textContent = 'New version available';

        var button = document.createElement('button');
        button.type = 'button';
        button.textContent = 'Update';

        button.addEventListener('click', function () {
            button.disabled = true;
            button.textContent = 'Updating…';

            // The tap, recorded: controllerchange reloads for this and
            // nothing else.
            wanted = true;

            worker.postMessage({ type: 'SKIP_WAITING' });
        });

        toast.appendChild(label);
        toast.appendChild(button);
        document.body.appendChild(toast);
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
