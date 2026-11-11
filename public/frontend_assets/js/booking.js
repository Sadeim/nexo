/**
 * Booking modal behaviour.
 *
 * Previously ~420 lines inline in footer.blade.php, which renders on every
 * page. It built its own time grid in the browser from a hard-coded copy of
 * the shop's hours, and treated the available-slots endpoint as if it returned
 * BOOKED slots — matching on slot.hour/slot.minute/slot.ampm, fields that
 * payload never had. isSlotBooked() therefore always returned false and no
 * taken slot was ever greyed out: the customer picked a time, submitted, and
 * was told it was unavailable.
 *
 * Now the server is the only thing that knows which slots exist and which are
 * free. This file renders what it is given.
 */
(function () {
    'use strict';

    const config = window.NexoBooking || {};

    const modal        = document.getElementById('booking-modal');
    const form         = document.getElementById('booking-form');
    const identifyForm = document.getElementById('booking-identify-form');
    if (!modal || !form || !identifyForm) return;

    // ---------- step one: the phone ----------

    const phoneInput   = document.getElementById('booking-phone');
    const consentBox   = document.getElementById('booking-consent');
    const phoneShown   = document.getElementById('booking-phone-shown');
    const phoneChange  = document.getElementById('booking-phone-change');

    // The number step one accepted. Posted with the booking so the server
    // identifies the same customer from both halves of the form.
    let identifiedPhone = '';

    /*
     * The last number used on this device.
     *
     * A returning customer should not have to type it again. Every access is
     * wrapped: localStorage throws outright in a Safari private window, and
     * comes back empty whenever site data has been cleared, so the modal has
     * to work exactly as well without it.
     */
    const REMEMBERED = 'nexo.booking.phone';

    // Stored as the bare ten digits, so a change to how numbers are
    // displayed never has to migrate what is already on somebody's phone.
    function remember(value) {
        try { window.localStorage.setItem(REMEMBERED, nationalDigits(value)); } catch (e) { /* not worth a broken form */ }
    }

    function remembered() {
        try { return window.localStorage.getItem(REMEMBERED) || ''; } catch (e) { return ''; }
    }

    function forget() {
        try { window.localStorage.removeItem(REMEMBERED); } catch (e) { /* as above */ }
    }

    /**
     * The ten national digits, from anything a person might type or paste.
     *
     * The country code is fixed and shown beside the field, so it is never
     * part of the value — but people paste what their contacts app gives
     * them, which is "+1 773-814-9061" as often as not. Eleven digits
     * beginning with 1 is the same number as the last ten; anything longer
     * is truncated rather than refused, because a trailing extension or a
     * stray character should not cost somebody their booking.
     */
    function nationalDigits(value) {
        let digits = String(value || '').replace(/\D+/g, '');

        if (digits.length === 11 && digits.charAt(0) === '1') {
            digits = digits.slice(1);
        }

        return digits.slice(0, 10);
    }

    /** (773) 814-9061, growing as the digits arrive. */
    function pretty(value) {
        const d = nationalDigits(value);

        if (d.length <= 3) return d;
        if (d.length <= 6) return '(' + d.slice(0, 3) + ') ' + d.slice(3);

        return '(' + d.slice(0, 3) + ') ' + d.slice(3, 6) + '-' + d.slice(6);
    }

    /**
     * Reformat as they type, without the caret jumping to the end.
     *
     * The brackets and the dash are inserted around the digits, so the only
     * stable thing to anchor the caret to is how many digits precede it.
     * Count them before, count them back afterwards.
     */
    function reformatPhone() {
        const start = phoneInput.selectionStart;
        const atEnd = start === null || start >= phoneInput.value.length;
        const digitsBefore = String(phoneInput.value.slice(0, start || 0)).replace(/\D+/g, '').length;

        const formatted = pretty(phoneInput.value);

        if (formatted === phoneInput.value) {
            return;
        }

        phoneInput.value = formatted;

        if (atEnd) {
            return;
        }

        let position = 0;
        let seen = 0;

        while (position < formatted.length && seen < digitsBefore) {
            if (/\d/.test(formatted.charAt(position))) seen++;
            position++;
        }

        try {
            phoneInput.setSelectionRange(position, position);
        } catch (e) {
            // Some browsers refuse this on a tel input; the value is still
            // right, and the caret lands at the end.
        }
    }

    phoneInput.addEventListener('input', reformatPhone);

    function showStep(which) {
        identifyForm.classList.toggle('hidden', which !== 'phone');
        form.classList.toggle('hidden', which === 'phone');
    }

    /** Back to step one, keeping what they typed so nothing is retyped. */
    function backToPhone() {
        forget();
        showStep('phone');
        phoneInput.focus();
    }

    phoneChange.addEventListener('click', backToPhone);

    const closeBtn     = document.getElementById('close-modal');
    const barberSelect = document.getElementById('booking-barber');
    const dateInput    = document.getElementById('booking-date');
    const timeSelect   = document.getElementById('booking-time');
    const serviceSelect = document.getElementById('booking-service');
    const timeHint     = document.getElementById('booking-time-hint');

    const lockRow      = document.getElementById('booking-barber-lock');
    const lockName     = document.getElementById('booking-barber-name');
    const lockChange   = document.getElementById('booking-barber-change');

    let slotsRequest = 0;

    // ---------- open / close ----------

    function openModal(barberId, barberName) {
        if (barberId) {
            lockBarber(barberId, barberName);
        }
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Always start at step one. The number is prefilled from last time,
        // but it is still shown and still confirmed — the device may have
        // changed hands, and the consent box must never arrive pre-ticked.
        showStep('phone');
        // Formatted on the way in: what was stored is digits, what a person
        // should see is their phone number.
        phoneInput.value = pretty(remembered());
        consentBox.checked = false;
        refreshSlots();
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Delegated: barber cards and the barber page both render .book-now-btn,
    // and the cards did not exist when this listener was first attached.
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.book-now-btn');
        if (!trigger) return;

        // A card's button is a link to ?barber=slug for people without JS.
        // With JS we keep them on the page and open the modal instead.
        event.preventDefault();
        openModal(trigger.dataset.barberId || null, trigger.dataset.barberName || null);
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    // ---------- barber lock ----------

    function lockBarber(id, name) {
        barberSelect.value = String(id);
        barberSelect.disabled = true;

        if (lockRow && lockName) {
            lockName.textContent = name || selectedBarberLabel();
            lockRow.classList.remove('hidden');
        }
    }

    function unlockBarber() {
        barberSelect.disabled = false;
        if (lockRow) lockRow.classList.add('hidden');
        barberSelect.focus();
    }

    function selectedBarberLabel() {
        const option = barberSelect.options[barberSelect.selectedIndex];
        return option ? option.textContent.trim() : '';
    }

    if (lockChange) {
        lockChange.addEventListener('click', function () {
            unlockBarber();
            refreshSlots();
        });
    }

    // Preselected server-side from ?barber=slug.
    if (config.lockedBarberId) {
        lockBarber(config.lockedBarberId, config.lockedBarberName);
    }

    // ---------- slots ----------

    function setTimeHint(message) {
        if (!timeHint) return;
        timeHint.textContent = message || '';
        timeHint.classList.toggle('hidden', !message);
    }

    function placeholder(text) {
        timeSelect.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = text;
        timeSelect.appendChild(option);
    }

    /**
     * Reload the slot list for the current date and barber.
     *
     * Both inputs change what is available, so both trigger this. A keepsake
     * of the current selection survives the reload when it is still bookable
     * — re-picking a time you already chose because you changed your mind
     * about the barber is the kind of small insult that loses a booking.
     */
    function refreshSlots() {
        const date = dateInput.value;

        if (!date) {
            placeholder('Select a date first');
            setTimeHint('');
            return;
        }

        const previous = timeSelect.value;
        const ticket = ++slotsRequest;

        timeSelect.disabled = true;
        placeholder('Loading times…');
        setTimeHint('');

        const params = { date: date };
        if (barberSelect.value) params.barber_id = barberSelect.value;

        axios.get(config.slotsUrl, { params: params })
            .then(function (response) {
                // A slower earlier request must not overwrite a newer answer.
                if (ticket !== slotsRequest) return;

                const slots = (response.data && response.data.slots) || [];
                renderSlots(slots, previous);
            })
            .catch(function () {
                if (ticket !== slotsRequest) return;
                placeholder('Could not load times');
                setTimeHint('Please check your connection and try again.');
            })
            .finally(function () {
                if (ticket === slotsRequest) timeSelect.disabled = false;
            });
    }

    function renderSlots(slots, previous) {
        timeSelect.innerHTML = '';

        if (!slots.length) {
            placeholder('No times available');
            setTimeHint('The shop is closed on this date, or every slot has gone.');
            return;
        }

        const first = document.createElement('option');
        first.value = '';
        first.textContent = 'Select a time';
        timeSelect.appendChild(first);

        let openCount = 0;
        let keptPrevious = false;

        slots.forEach(function (slot) {
            const option = document.createElement('option');
            option.value = slot.value;
            option.textContent = slot.disabled ? slot.label + ' (booked)' : slot.label;
            option.disabled = !!slot.disabled;

            if (!slot.disabled) {
                openCount++;
                if (slot.value === previous) {
                    option.selected = true;
                    keptPrevious = true;
                }
            }

            timeSelect.appendChild(option);
        });

        if (!keptPrevious) timeSelect.value = '';

        if (openCount === 0) {
            setTimeHint(barberSelect.value
                ? 'This barber is fully booked on this date. Try another day, or "Any available barber".'
                : 'Every slot on this date has gone. Please try another day.');
        } else {
            setTimeHint('');
        }
    }

    dateInput.addEventListener('change', refreshSlots);
    barberSelect.addEventListener('change', refreshSlots);

    // Never offer a date in the past.
    dateInput.min = new Date().toISOString().split('T')[0];

    // ---------- services ----------

    axios.get(config.servicesUrl)
        .then(function (response) {
            const services = (response.data && (response.data.services || response.data)) || [];
            if (!Array.isArray(services)) return;

            services.forEach(function (service) {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name;
                // Kept for the conversion value; absent for a service with
                // no price set, which stays absent rather than becoming 0.
                if (service.price !== null && service.price !== undefined) {
                    option.dataset.price = service.price;
                }
                serviceSelect.appendChild(option);
            });
        })
        .catch(function () { /* The placeholder option remains. */ });

    // ---------- feedback ----------

    function notify(message, type) {
        const box = document.createElement('div');
        box.className = 'fixed top-6 right-6 z-[60] px-5 py-4 rounded shadow-lg font-poppins text-sm max-w-sm ' +
            (type === 'success' ? 'bg-evergreen text-ivory' : 'bg-crimson text-ivory');
        box.setAttribute('role', 'status');
        box.textContent = message;
        document.body.appendChild(box);
        setTimeout(function () { box.remove(); }, 6000);
    }

    function clearFieldErrors() {
        form.querySelectorAll('.ring-2.ring-red-500').forEach(function (el) {
            el.classList.remove('ring-2', 'ring-red-500');
        });
    }

    function markField(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('ring-2', 'ring-red-500');
    }

    // ---------- conversion reporting ----------

    /**
     * Tell Google Ads a booking happened.
     *
     * Everything here is optional and nothing here may break a booking that
     * has already succeeded. gtag is absent whenever an ad blocker, a privacy
     * setting or a network failure stopped the tag loading — which is a large
     * minority of visitors — and the label is empty until the conversion
     * action exists in Google Ads. In every one of those cases this does
     * nothing at all, quietly.
     */
    function reportConversion() {
        try {
            const adsId = modal.dataset.adsId;
            const label = modal.dataset.adsLabel;

            if (!adsId || !label || typeof window.gtag !== 'function') {
                return;
            }

            const payload = { send_to: adsId + '/' + label, currency: 'USD' };

            const chosen = serviceSelect.options[serviceSelect.selectedIndex];
            const price = chosen && chosen.dataset ? parseFloat(chosen.dataset.price) : NaN;

            // Only when the service actually has a price. A conversion
            // reported as worth nothing is worse than one with no value.
            if (!isNaN(price)) {
                payload.value = price;
            }

            window.gtag('event', 'conversion', payload);
        } catch (e) {
            // A booking is confirmed either way; this is bookkeeping.
        }
    }

    // ---------- step one: continue ----------

    identifyForm.addEventListener('submit', function (event) {
        event.preventDefault();
        clearFieldErrors();

        const digits = nationalDigits(phoneInput.value);

        /*
         * Ten, exactly. The field asks for a US national number with the
         * country code already supplied, so anything shorter is half-typed.
         * Checked here as well as on the server, so an obvious typo costs a
         * message rather than a round trip.
         */
        if (digits.length !== 10) {
            markField('booking-phone');
            notify('Enter a 10-digit US mobile number.', 'error');
            return;
        }

        const button = document.getElementById('booking-continue');
        const label = button.textContent;
        button.disabled = true;
        button.textContent = 'ONE MOMENT…';

        axios.post(config.identifyUrl, {
            // Digits only. The server normalises anyway, but there is no
            // reason to make it undo formatting this end put on.
            phone: digits,
            sms_marketing_consent: consentBox.checked ? 1 : 0
        })
            .then(function (response) {
                const data = response.data || {};

                identifiedPhone = digits;
                remember(digits);

                // The read-only line in step two, with the same fixed code
                // the field showed.
                phoneShown.textContent = '+1 ' + pretty(digits);

                // Prefilled only for a number the shop already knows. Fields
                // are not overwritten if the customer has already typed into
                // them — going back and forth must not undo their work.
                // Name only. The endpoint does not return an email and must
                // not start: see the comment on identifyCustomer(). A
                // returning customer retypes it, or leaves it blank.
                if (data.known) {
                    setIfEmpty('booking-first-name', data.first_name);
                    setIfEmpty('booking-last-name', data.last_name);
                }

                showStep('booking');
                refreshSlots();
            })
            .catch(function (error) {
                const data = (error.response && error.response.data) || {};
                const status = error.response && error.response.status;

                if (status === 429) {
                    notify('Too many tries. Give it a minute and start again.', 'error');
                    return;
                }

                markField('booking-phone');
                notify(
                    (data.errors && data.errors.phone && data.errors.phone[0])
                        || data.message
                        || 'Something went wrong. Please try again.',
                    'error'
                );
            })
            .finally(function () {
                button.disabled = false;
                button.textContent = label;
            });
    });

    function setIfEmpty(id, value) {
        const el = document.getElementById(id);
        if (el && !el.value && value) el.value = value;
    }

    // ---------- submit ----------

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearFieldErrors();

        const submit = form.querySelector('button[type="submit"]');
        const label = submit.textContent;
        submit.disabled = true;
        submit.textContent = 'BOOKING…';

        axios.post(config.storeUrl, {
            first_name: document.getElementById('booking-first-name').value,
            last_name: document.getElementById('booking-last-name').value,
            email: document.getElementById('booking-email').value,
            // The number step one accepted, not whatever is in the box now:
            // the two halves of the form must reach the same customer.
            phone: identifiedPhone || nationalDigits(phoneInput.value),
            date: dateInput.value,
            time: timeSelect.value,
            service_id: serviceSelect.value,
            // Empty string means "any available barber"; the server wants null.
            barber_id: barberSelect.value || null
        })
            .then(function (response) {
                // Before the form is reset, while the chosen service is still
                // selected and its price still readable.
                reportConversion();

                notify(response.data.message || 'Booking confirmed.', 'success');
                form.reset();
                identifyForm.reset();
                // The number stays remembered for next time; the consent box
                // does not, and step one is where the modal opens again.
                showStep('phone');
                placeholder('Select a date first');
                setTimeHint('');
                if (config.lockedBarberId) lockBarber(config.lockedBarberId, config.lockedBarberName);
                closeModal();
            })
            .catch(function (error) {
                const status = error.response && error.response.status;
                const data = (error.response && error.response.data) || {};

                if (status === 422) {
                    // Two different refusals with two different remedies: pick
                    // another barber, or pick another time. Saying "not
                    // available" for both leaves the customer guessing.
                    if (data.errors) {
                        Object.keys(data.errors).forEach(function (field) {
                            markField('booking-' + field.replace(/_/g, '-'));
                        });
                        notify(data.errors[Object.keys(data.errors)[0]][0], 'error');
                    } else if (data.message) {
                        const barberBusy = /barber is already booked/i.test(data.message);
                        markField(barberBusy ? 'booking-barber' : 'booking-time');
                        setTimeHint(barberBusy
                            ? 'Someone booked this barber a moment ago. Pick another time, or choose "Any available barber".'
                            : 'That time has just been taken. Please pick another.');
                        notify(data.message, 'error');
                    }

                    // Either way the picture has changed, so reload it.
                    refreshSlots();
                    return;
                }

                notify(data.message || 'Something went wrong. Please try again.', 'error');
            })
            .finally(function () {
                submit.disabled = false;
                submit.textContent = label;
            });
    });
})();
