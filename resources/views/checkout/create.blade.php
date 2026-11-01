@extends('layouts.portal', ['title' => 'Checkout | Beauty & Blooms'])

@section('content')
    <div class="portal-row" style="margin-bottom:22px">
        <div><p class="kicker">Secure checkout</p><h1 class="portal-title" style="margin-bottom:0">Delivery details.</h1></div>
        <a class="portal-link" href="{{ route('home') }}">Continue shopping</a>
    </div>
    <div class="portal-grid">
        <section class="portal-card">
            <form id="checkoutForm" class="portal-form" method="post" action="{{ route('checkout.store') }}">
                @csrf
                <input type="hidden" name="items_json" id="itemsJson">
                <input type="hidden" name="payment_method" value="demo_card">
                <div class="portal-grid">
                    <div class="portal-field"><label for="customer_name">Full name</label><input id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required maxlength="120" autocomplete="name"></div>
                    <div class="portal-field"><label for="customer_email">Email</label><input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', auth()->user()->email) }}" required maxlength="255" autocomplete="email"></div>
                    <div class="portal-field full"><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone') }}" required maxlength="30" autocomplete="tel"></div>
                    <div class="portal-field full"><label for="address_line">Street address</label><input id="address_line" name="address_line" value="{{ old('address_line') }}" required maxlength="255" autocomplete="street-address"></div>
                    <div class="portal-field"><label for="city">City</label><input id="city" name="city" value="{{ old('city') }}" required maxlength="100" autocomplete="address-level2"></div>
                    <div class="portal-field"><label for="state">State / Region</label><input id="state" name="state" value="{{ old('state') }}" maxlength="100" autocomplete="address-level1"></div>
                    <div class="portal-field"><label for="postal_code">Postal code</label><input id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required maxlength="20" autocomplete="postal-code"></div>
                    <div class="portal-field"><label for="country">Country</label><input id="country" name="country" value="{{ old('country') }}" required maxlength="100" autocomplete="country-name"></div>
                    <div class="portal-field full"><label for="notes">Delivery notes (optional)</label><textarea id="notes" name="notes" maxlength="1000">{{ old('notes') }}</textarea></div>
                </div>
                <section id="demoCardFields" class="portal-card-fields" aria-labelledby="demoCardTitle">
                    <h2 id="demoCardTitle">Card payment is required</h2>
                    <div class="card-preview" aria-label="Live card preview">
                        <div class="card-inner">
                            <div class="card-top">
                                <div class="card-chip" aria-hidden="true"></div>
                                <span class="card-brand">Beauty <em>&amp;</em> Blooms</span>
                            </div>
                            <div class="card-num" id="cardDisplay">•••• •••• •••• ••••</div>
                            <div class="card-bottom">
                                <div>
                                    <div class="card-label">Card holder</div>
                                    <div class="card-value" id="nameDisplay">YOUR NAME</div>
                                </div>
                                <div class="card-expiry">
                                    <div class="card-label">Expires</div>
                                    <div class="card-value" id="expDisplay">MM/YY</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="portal-muted">Demo only: enter <code>4242 4242 4242 4242</code>, any valid future expiry date, and any 3-digit CVC. The fields update the preview and are checked by the server for the demo; they are never saved.</p>
                    <div class="portal-grid">
                        <div class="portal-field full"><label for="demo_card_name">Name on card</label><input id="demo_card_name" name="demo_cardholder" type="text" placeholder="Sophia Bloom" autocomplete="off" maxlength="100" required></div>
                        <div class="portal-field full"><label for="demo_card_number">Card number</label><input id="demo_card_number" name="demo_card_number" type="text" inputmode="numeric" autocomplete="off" maxlength="19" placeholder="4242 4242 4242 4242" required></div>
                        <div class="portal-field"><label for="demo_card_expiry">Expiry date</label><input id="demo_card_expiry" name="demo_card_expiry" type="text" inputmode="numeric" autocomplete="off" maxlength="5" placeholder="MM/YY" required></div>
                        <div class="portal-field"><label for="demo_card_cvc">CVC</label><input id="demo_card_cvc" name="demo_card_cvc" type="text" inputmode="numeric" autocomplete="off" maxlength="3" placeholder="123" required></div>
                    </div>
                    <p id="demoCardError" class="portal-errors" role="alert" hidden></p>
                </section>
                <p class="portal-muted">Orders over $80 ship free; otherwise shipping is $8.</p>
                <button id="placeOrder" class="portal-button" type="submit">Place order</button>
            </form>
        </section>
        <aside class="portal-card">
            <h2 class="h2" style="font-size:30px">Your bag</h2>
            <div id="checkoutItems" class="portal-form" style="margin-top:18px"></div>
            <div class="portal-row" style="margin-top:22px"><span>Subtotal</span><strong id="checkoutSubtotal">$0.00</strong></div>
            <div class="portal-row" style="margin-top:10px"><span>Delivery</span><strong id="checkoutShipping">$0.00</strong></div>
            <div class="portal-row" style="margin-top:16px;padding-top:16px;border-top:1px solid var(--line)"><strong>Total</strong><strong id="checkoutTotal">$0.00</strong></div>
            <p id="emptyBag" class="portal-muted" hidden>Your bag is empty. Add a product before checkout.</p>
        </aside>
    </div>
    <script>
        (() => {
            const products = @js($products->map(fn ($product) => ['id' => $product->id, 'name' => $product->name, 'price' => (float) $product->price])->values());
            const priceMap = new Map(products.map((product) => [product.id, product]));
            const form = document.getElementById('checkoutForm');
            const button = document.getElementById('placeOrder');
            const cardName = document.getElementById('demo_card_name');
            const cardNumber = document.getElementById('demo_card_number');
            const cardExpiry = document.getElementById('demo_card_expiry');
            const cardCvc = document.getElementById('demo_card_cvc');
            const cardError = document.getElementById('demoCardError');
            let cart = [];
            try {
                const saved = JSON.parse(localStorage.getItem('bb-cart') || '[]');
                cart = Array.isArray(saved) ? saved.filter((item) => priceMap.has(Number(item.id)) && Number.isInteger(item.qty) && item.qty > 0) : [];
            } catch (error) {
                console.error('Unable to read the saved shopping bag.', error);
            }
            const rows = cart.map((item) => ({ id: Number(item.id), opt: String(item.opt || 'One size'), qty: Math.min(item.qty, 10) }));
            document.getElementById('itemsJson').value = JSON.stringify(rows);
            const subtotal = rows.reduce((total, item) => total + priceMap.get(item.id).price * item.qty, 0);
            const shipping = subtotal >= 80 ? 0 : 8;
            document.getElementById('checkoutItems').innerHTML = rows.map((item) => {
                const product = priceMap.get(item.id);
                const lineTotal = product.price * item.qty;
                const safeName = product.name.replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
                const safeOption = item.opt.replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
                return `<div class="portal-row"><span>${safeName} · ${safeOption} × ${item.qty}</span><strong>$${lineTotal.toFixed(2)}</strong></div>`;
            }).join('') || '<p class="portal-muted">No products in your bag.</p>';
            document.getElementById('checkoutSubtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('checkoutShipping').textContent = shipping === 0 ? 'Free' : `$${shipping.toFixed(2)}`;
            document.getElementById('checkoutTotal').textContent = `$${(subtotal + shipping).toFixed(2)}`;
            if (!rows.length) {
                document.getElementById('emptyBag').hidden = false;
                button.disabled = true;
            }
            cardName.addEventListener('input', () => {
                document.getElementById('nameDisplay').textContent = cardName.value.trim().toUpperCase() || 'YOUR NAME';
            });
            cardNumber.addEventListener('input', () => {
                const digits = cardNumber.value.replace(/\D/g, '').slice(0, 16);
                const formatted = digits.match(/.{1,4}/g)?.join(' ') || '';
                cardNumber.value = formatted;
                document.getElementById('cardDisplay').textContent = formatted || '•••• •••• •••• ••••';
            });
            cardExpiry.addEventListener('input', () => {
                const digits = cardExpiry.value.replace(/\D/g, '').slice(0, 4);
                const formatted = digits.length > 2 ? `${digits.slice(0, 2)}/${digits.slice(2)}` : digits;
                cardExpiry.value = formatted;
                document.getElementById('expDisplay').textContent = formatted || 'MM/YY';
            });
            cardCvc.addEventListener('input', () => {
                cardCvc.value = cardCvc.value.replace(/\D/g, '').slice(0, 4);
            });
            form.addEventListener('submit', (event) => {
                if (!rows.length) {
                    event.preventDefault();
                    return;
                }
                const cardDigits = cardNumber.value.replace(/\s/g, '');
                const expiry = cardExpiry.value.trim();
                const cvc = cardCvc.value.trim();
                const [monthValue, yearValue] = expiry.split('/');
                const month = Number(monthValue);
                const year = Number(`20${yearValue}`);
                const now = new Date();
                const currentMonth = now.getFullYear() * 12 + now.getMonth();
                const expiryMonth = year * 12 + month - 1;
                if (cardDigits !== '4242424242424242' || !/^\d{2}\/\d{2}$/.test(expiry) || month < 1 || month > 12 || expiryMonth < currentMonth || !/^\d{3}$/.test(cvc) || !cardName.value.trim()) {
                    event.preventDefault();
                    cardError.hidden = false;
                    cardError.textContent = 'For this demo, enter card 4242 4242 4242 4242, a valid future MM/YY expiry, a 3-digit CVC, and the cardholder name.';
                    cardError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    return;
                }
                cardError.hidden = true;
                button.disabled = true;
                button.textContent = isDemoCard ? 'Simulating demo payment…' : 'Placing order…';
            });
        })();
    </script>
@endsection
