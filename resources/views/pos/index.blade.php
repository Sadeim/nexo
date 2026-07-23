<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NEXO POS</title>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('pos_assets/css/pos.css') }}" rel="stylesheet">
</head>
<body class="pos-body">

    <div class="pos-topbar">
        <div class="pos-brand">NEXO POS</div>
        <div style="display:flex; align-items:center; gap:16px;">
            <span class="pos-cashier">Cashier: <strong>{{ $cashier->name ?? $cashier->email }}</strong></span>
            <form method="POST" action="{{ route('pos.logout') }}">
                @csrf
                <button type="submit" class="pos-btn pos-btn-ghost" style="border-color:rgba(248,237,210,.4); color:var(--color-ivory);">Logout</button>
            </form>
        </div>
    </div>

    <div class="pos-main">
        {{-- Services --}}
        <div class="pos-panel">
            <h2>Services <span class="pos-count-pill">{{ $services->whereNotNull('price')->count() }} sellable</span></h2>

            @if ($services->isEmpty())
                <div class="pos-empty">No services found. Add services from the admin dashboard first.</div>
            @else
                <div class="pos-services">
                    @foreach ($services as $service)
                        @php $sellable = $service->price !== null; @endphp
                        <button type="button"
                                class="pos-service-btn {{ $sellable ? '' : 'is-disabled' }}"
                                @if ($sellable)
                                    data-id="{{ $service->id }}"
                                    data-name="{{ $service->name }}"
                                    data-price="{{ $service->price }}"
                                    onclick="POS.addService(this)"
                                @else
                                    disabled aria-disabled="true"
                                @endif>
                            <span class="name">{{ $service->name }}</span>
                            @if ($sellable)
                                <span class="price">${{ number_format((float) $service->price, 2) }}</span>
                                <span class="add-hint">+</span>
                            @else
                                <span class="pos-badge-noprice">No price — set one first</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Cart --}}
        <div class="pos-panel">
            <h2>Cart <span id="pos-cart-count" class="pos-count-pill">0 items</span></h2>

            <ul id="pos-cart" class="pos-cart-items"></ul>
            <div id="pos-cart-empty" class="pos-cart-empty">Cart is empty. Tap a service to add it.</div>

            <div class="pos-totals">
                <div class="pos-total-row"><span>Subtotal</span><span id="pos-subtotal">$0.00</span></div>
                <div class="pos-total-row grand"><span>Total</span><span id="pos-total">$0.00</span></div>
            </div>

            <div class="pos-pay-actions">
                <button id="pos-pay-cash" type="button" class="pos-btn pos-btn-cash" onclick="POS.pay('cash')" disabled>
                    Cash
                </button>
                <button id="pos-pay-card" type="button" class="pos-btn pos-btn-card" onclick="POS.payCard()" disabled>
                    Card
                </button>
            </div>
            <button type="button" class="pos-btn pos-btn-ghost pos-clear" style="width:100%;"
                    onclick="POS.clear()">Clear cart</button>
            <p class="pos-hint">Custom prices apply to this sale only and never change the stored service price.</p>
        </div>
    </div>

    {{-- Success modal (edit #2) --}}
    <div id="pos-success" class="pos-overlay" onclick="POS.closeSuccess(event)">
        <div class="pos-modal" onclick="event.stopPropagation()">
            <div class="pos-success-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            </div>
            <h3>Sale completed</h3>
            <p class="muted">The transaction was saved successfully.</p>
            <div class="pos-receipt">
                <div class="row"><span>Invoice</span><span id="pos-succ-invoice">#0</span></div>
                <div class="row"><span>Total</span><span class="big" id="pos-succ-total">$0.00</span></div>
            </div>
            <button type="button" class="pos-btn pos-btn-evergreen" style="width:100%;" onclick="POS.closeSuccess()">Done</button>
        </div>
    </div>

    {{-- Custom price modal (edit #5) --}}
    <div id="pos-custom" class="pos-overlay" onclick="POS.closeCustom(event)">
        <div class="pos-modal custom" onclick="event.stopPropagation()">
            <h3>Custom price</h3>
            <p class="muted" id="pos-custom-service">—</p>

            <label class="field-label" for="pos-custom-input">Price for this line only</label>
            <div class="pos-price-wrap">
                <span class="dollar">$</span>
                <input type="text" id="pos-custom-input" inputmode="decimal" autocomplete="off"
                       onkeydown="if(event.key==='Enter'){POS.applyCustom();}">
            </div>
            <p class="orig-note">Original stored price: <strong id="pos-custom-original">$0.00</strong> (never changed)</p>
            <p class="err-note" id="pos-custom-error"></p>

            <div class="pos-modal-actions">
                <button type="button" class="pos-btn pos-btn-ghost" onclick="POS.resetCustom()">Reset to original</button>
                <button type="button" class="pos-btn pos-btn-evergreen" onclick="POS.applyCustom()">Apply</button>
            </div>
        </div>
    </div>

    {{-- Card (PlutoPay Terminal) status modal --}}
    <div id="pos-card-wait" class="pos-overlay" onclick="event.stopPropagation()">
        <div class="pos-modal" onclick="event.stopPropagation()">
            <div class="pos-card-spinner" id="pos-card-spinner"></div>
            <h3 id="pos-card-wait-title">Waiting for card…</h3>
            <p class="muted" id="pos-card-wait-msg">Present the card on the reader.</p>
            <div class="pos-receipt">
                <div class="row"><span>Total</span><span class="big" id="pos-card-total">$0.00</span></div>
            </div>
            <button type="button" id="pos-card-close" class="pos-btn pos-btn-ghost" style="width:100%; display:none;"
                    onclick="POS.closeCardWait()">Close</button>
        </div>
    </div>

    <script>
    const POS = (function () {
        const storeUrl = @json(route('pos.transactions.store'));
        const cardStartUrl = @json(route('pos.card.start'));
        const cardStatusTpl = @json(route('pos.card.status', ['transaction' => '__ID__']));
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const MAX_PRICE = 1000000;
        const CARD_TIMEOUT_MS = 90000;   // 90s wait for the card read

        // cart line: { id, name, original(string), custom(string|null), qty }
        let cart = [];
        let busy = false;
        let editingId = null;   // line id being edited in the custom modal

        /* ---- money helpers (integer cents; no float math) ---- */
        function toCents(value) {
            if (value === '' || value === null || value === undefined) return null;
            const s = String(value).trim();
            if (!/^\d+(\.\d{1,2})?$/.test(s)) return null;
            const [d, f = ''] = s.split('.');
            return parseInt(d, 10) * 100 + parseInt((f + '00').slice(0, 2), 10);
        }
        function fmt(cents) {
            const sign = cents < 0 ? '-' : '';
            const a = Math.abs(cents);
            return sign + '$' + Math.floor(a / 100) + '.' + String(a % 100).padStart(2, '0');
        }
        function effective(line) { return line.custom !== null ? line.custom : line.original; }
        function isCustom(line) {
            return line.custom !== null && toCents(line.custom) !== toCents(line.original);
        }

        /* ---- cart ops ---- */
        function addService(btn) {
            const id = parseInt(btn.dataset.id, 10);
            const price = btn.dataset.price;
            if (toCents(price) === 0) {
                if (!confirm(btn.dataset.name + ' is priced $0.00. You must set a price before payment. Add anyway?')) return;
            }
            const existing = cart.find(l => l.id === id);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ id, name: btn.dataset.name, original: price, custom: null, qty: 1 });
            }
            render();
        }
        function changeQty(id, delta) {
            const line = cart.find(l => l.id === id);
            if (!line) return;
            line.qty = Math.max(1, line.qty + delta);
            render();
        }
        function setQty(id, value) {
            const line = cart.find(l => l.id === id);
            if (!line) return;
            const q = parseInt(value, 10);
            line.qty = (Number.isInteger(q) && q >= 1) ? q : 1;
            render();
        }
        function remove(id) { cart = cart.filter(l => l.id !== id); render(); }
        function clear() { cart = []; render(); }

        function lineValid(line) {
            const c = toCents(effective(line));
            // custom lines may be 0 (comp); default (no custom) must be > 0
            const min = line.custom !== null ? 0 : 1;
            return c !== null && c >= min && Number.isInteger(line.qty) && line.qty >= 1;
        }

        /* ---- custom price modal ---- */
        function openCustom(id) {
            const line = cart.find(l => l.id === id);
            if (!line) return;
            editingId = id;
            document.getElementById('pos-custom-service').textContent = line.name;
            document.getElementById('pos-custom-original').textContent = fmt(toCents(line.original));
            document.getElementById('pos-custom-error').textContent = '';
            const input = document.getElementById('pos-custom-input');
            input.value = effective(line);
            document.getElementById('pos-custom').classList.add('open');
            setTimeout(() => { input.focus(); input.select(); }, 30);
        }
        function closeCustom(e) {
            if (e && e.target !== e.currentTarget) return;
            document.getElementById('pos-custom').classList.remove('open');
            editingId = null;
        }
        function applyCustom() {
            const line = cart.find(l => l.id === editingId);
            if (!line) return closeCustom();
            const raw = document.getElementById('pos-custom-input').value.trim();
            const err = document.getElementById('pos-custom-error');

            const cents = toCents(raw);
            if (cents === null) { err.textContent = 'Enter a valid number (max 2 decimals).'; return; }
            if (raw.indexOf('-') !== -1) { err.textContent = 'Price cannot be negative.'; return; }
            if (cents > MAX_PRICE * 100) { err.textContent = 'Price is too large.'; return; }
            if (cents === 0) {
                if (!confirm('Set this line to $0.00 (free)? This will be recorded as a comp.')) return;
            }
            line.custom = raw;
            closeCustom();
            render();
        }
        function resetCustom() {
            const line = cart.find(l => l.id === editingId);
            if (line) line.custom = null;
            closeCustom();
            render();
        }

        /* ---- render ---- */
        function render() {
            const list = document.getElementById('pos-cart');
            const empty = document.getElementById('pos-cart-empty');
            list.innerHTML = '';

            let subtotal = 0;
            let allValid = cart.length > 0;
            let itemCount = 0;

            cart.forEach(line => {
                itemCount += line.qty;
                const eff = toCents(effective(line));
                const valid = lineValid(line);
                if (!valid) allValid = false;
                const lineTotal = (eff !== null) ? eff * line.qty : 0;
                subtotal += lineTotal;

                const custom = isCustom(line);
                const priceLine = custom
                    ? `<span class="orig-price">${fmt(toCents(line.original))}</span>
                       <span class="eff-price">${fmt(eff)}</span>
                       <span class="pos-custom-flag">custom</span>`
                    : `<span class="eff-price">${eff !== null ? fmt(eff) : '—'}</span>`;

                const li = document.createElement('li');
                li.className = 'pos-cart-row';
                li.innerHTML = `
                    <div>
                        <div class="title">${escapeHtml(line.name)}</div>
                        <div class="price-line">${priceLine}</div>
                        <div class="controls">
                            <span class="pos-qty-group">
                                <button class="pos-qty-btn" onclick="POS.changeQty(${line.id}, -1)" aria-label="Decrease">&minus;</button>
                                <input class="pos-qty" type="number" min="1" step="1" value="${line.qty}"
                                       oninput="POS.setQty(${line.id}, this.value)" aria-label="Quantity">
                                <button class="pos-qty-btn" onclick="POS.changeQty(${line.id}, 1)" aria-label="Increase">+</button>
                            </span>
                            <button class="pos-custom-btn ${custom ? 'is-active' : ''}" onclick="POS.openCustom(${line.id})">
                                &#9998; ${custom ? 'Custom' : 'Custom price'}
                            </button>
                            ${valid ? '' : '<span class="pos-badge-noprice">Set a valid price</span>'}
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px;">
                        <button class="pos-remove" title="Remove" onclick="POS.remove(${line.id})">&times;</button>
                        <span class="pos-line-total">${valid ? fmt(lineTotal) : '—'}</span>
                    </div>`;
                list.appendChild(li);
            });

            empty.style.display = cart.length ? 'none' : 'block';
            document.getElementById('pos-subtotal').textContent = fmt(subtotal);
            document.getElementById('pos-total').textContent = fmt(subtotal);
            document.getElementById('pos-cart-count').textContent = itemCount + (itemCount === 1 ? ' item' : ' items');
            document.getElementById('pos-pay-cash').disabled = !(allValid && cart.length && !busy);
            document.getElementById('pos-pay-card').disabled = !(allValid && cart.length && !busy);
        }

        /* ---- payment ---- */
        async function pay(method) {
            if (busy) return;
            if (!cart.length) { alert('Cart is empty.'); return; }
            if (!cart.every(lineValid)) { alert('Fix invalid prices before paying.'); return; }

            busy = true; render();

            const payload = {
                payment_method: method,
                items: cart.map(l => {
                    const item = { service_id: l.id, quantity: l.qty };
                    // Send custom_price ONLY when the cashier set one explicitly.
                    if (l.custom !== null) item.custom_price = l.custom;
                    return item;
                }),
            };

            try {
                const res = await fetch(storeUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    showSuccess(data.transaction_id, data.total);
                    cart = [];
                } else if (res.status === 401) {
                    alert('Session expired. Redirecting to login…');
                    window.location.reload();
                } else {
                    alert(data.message || 'Could not complete the sale.');
                }
            } catch (e) {
                alert('Network error. Please try again.');
            } finally {
                busy = false; render();
            }
        }

        /* ---- card payment (PlutoPay Terminal) ---- */
        let cardPoll = null;
        let cardDeadline = 0;

        function uuid() {
            if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                const r = Math.random() * 16 | 0;
                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
        }
        function cardStatusUrl(id) { return cardStatusTpl.replace('__ID__', id); }

        function cardItems() {
            return cart.map(l => {
                const item = { service_id: l.id, quantity: l.qty };
                if (l.custom !== null) item.custom_price = l.custom;
                return item;
            });
        }

        async function payCard() {
            if (busy) return;
            if (!cart.length) { alert('Cart is empty.'); return; }
            if (!cart.every(lineValid)) { alert('Fix invalid prices before paying.'); return; }

            busy = true; render();
            showCardWait();

            // One idempotency key per attempt: a double-click => one charge.
            const payload = { idempotency_key: uuid(), items: cardItems() };

            try {
                const res = await fetch(cardStartUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (!res.ok || !data.transaction_id) {
                    cardError(data.message || 'Could not start the card payment.');
                    busy = false; render();
                    return;
                }
                if (data.status === 'completed') { onCardSuccess(data); return; }
                if (data.status === 'failed')    { cardError('Payment failed: ' + (data.failure_reason || 'declined')); busy = false; render(); return; }

                startCardPolling(data.transaction_id);
            } catch (e) {
                cardError('Network error. Please try again.');
                busy = false; render();
            }
        }

        function startCardPolling(id) {
            cardDeadline = Date.now() + CARD_TIMEOUT_MS;
            clearInterval(cardPoll);
            cardPoll = setInterval(async () => {
                if (Date.now() > cardDeadline) {
                    clearInterval(cardPoll);
                    cardPending();               // NOT paid — manual verification
                    busy = false; render();
                    return;
                }
                try {
                    const res = await fetch(cardStatusUrl(id), { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (data.status === 'completed') { clearInterval(cardPoll); onCardSuccess(data); }
                    else if (data.status === 'failed')   { clearInterval(cardPoll); cardError('Payment failed: ' + (data.failure_reason || 'declined')); busy = false; render(); }
                    else if (data.status === 'canceled') { clearInterval(cardPoll); cardError('Payment canceled.'); busy = false; render(); }
                } catch (e) { /* transient — keep polling until the deadline */ }
            }, 2000);
        }

        function onCardSuccess(data) {
            hideCardWait();
            cart = [];
            busy = false; render();
            showSuccess(data.transaction_id, data.total);   // reuse the cash success modal
        }

        /* card modal state helpers */
        function showCardWait() {
            document.getElementById('pos-card-total').textContent = document.getElementById('pos-total').textContent;
            document.getElementById('pos-card-wait-title').textContent = 'Waiting for card…';
            document.getElementById('pos-card-wait-msg').textContent = 'Present the card on the reader.';
            document.getElementById('pos-card-spinner').style.display = '';
            document.getElementById('pos-card-close').style.display = 'none';
            document.getElementById('pos-card-wait').classList.add('open');
        }
        function hideCardWait() { document.getElementById('pos-card-wait').classList.remove('open'); }
        function closeCardWait() { clearInterval(cardPoll); hideCardWait(); }
        function cardError(msg) {
            document.getElementById('pos-card-wait-title').textContent = 'Payment failed';
            document.getElementById('pos-card-wait-msg').textContent = msg;
            document.getElementById('pos-card-spinner').style.display = 'none';
            document.getElementById('pos-card-close').style.display = '';
        }
        function cardPending() {
            document.getElementById('pos-card-wait-title').textContent = 'Still pending';
            document.getElementById('pos-card-wait-msg').textContent =
                'No confirmation received. Do NOT treat this as paid — verify manually before retrying.';
            document.getElementById('pos-card-spinner').style.display = 'none';
            document.getElementById('pos-card-close').style.display = '';
        }

        /* ---- success modal ---- */
        let successTimer = null;
        function showSuccess(invoice, total) {
            document.getElementById('pos-succ-invoice').textContent = '#' + invoice;
            document.getElementById('pos-succ-total').textContent = '$' + total;
            document.getElementById('pos-success').classList.add('open');
            clearTimeout(successTimer);
            successTimer = setTimeout(closeSuccess, 5000);
        }
        function closeSuccess(e) {
            // Ignore clicks that originate inside the modal (overlay click closes).
            if (e && e.target !== e.currentTarget) return;
            clearTimeout(successTimer);
            document.getElementById('pos-success').classList.remove('open');
        }

        function escapeHtml(s) {
            return String(s).replace(/[&<>"']/g, c => (
                { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
            ));
        }

        return { addService, changeQty, setQty, remove, clear, pay, payCard, closeCardWait,
                 openCustom, closeCustom, applyCustom, resetCustom, closeSuccess };
    })();
    </script>
</body>
</html>
