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
    if (!modal || !form) return;

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

    // ---------- submit ----------

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearFieldErrors();

        const submit = form.querySelector('button[type="submit"]');
        const label = submit.textContent;
        submit.disabled = true;
        submit.textContent = 'BOOKING…';

        axios.post(config.storeUrl, {
            name: document.getElementById('booking-name').value,
            email: document.getElementById('booking-email').value,
            date: dateInput.value,
            time: timeSelect.value,
            service_id: serviceSelect.value,
            // Empty string means "any available barber"; the server wants null.
            barber_id: barberSelect.value || null
        })
            .then(function (response) {
                notify(response.data.message || 'Booking confirmed. Check your email.', 'success');
                form.reset();
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
