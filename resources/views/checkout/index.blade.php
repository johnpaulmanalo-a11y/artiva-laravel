@extends('layouts.app')

@section('title', 'Checkout — Artiva')
@section('meta_description', 'Complete your Artiva purchase securely.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/checkout.js') }}" defer></script>
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="page-head">
                <div>
                    <p class="eyebrow">Secure checkout</p>
                    <h1>Complete your order</h1>
                    <p>Your artwork unlocks in your library immediately after payment is approved.</p>
                </div>
                <a class="link-arrow" href="{{ route('cart.index') }}"><i class="bi bi-arrow-left"></i> Back to cart</a>
            </div>

            <div class="checkout-steps">
                <span class="checkout-steps__item is-done">
                    <span class="checkout-steps__num"><i class="bi bi-check-lg"></i></span> Cart
                </span>
                <span class="checkout-steps__sep"></span>
                <span class="checkout-steps__item is-active">
                    <span class="checkout-steps__num">2</span> Details &amp; payment
                </span>
                <span class="checkout-steps__sep"></span>
                <span class="checkout-steps__item">
                    <span class="checkout-steps__num">3</span> Confirmation
                </span>
            </div>

            <form class="checkout-layout" method="POST" action="{{ route('checkout.store') }}" data-checkout-form>
                @csrf

                <div class="checkout-form">
                    <div class="form-card">
                        <div class="form-card__head">
                            <span class="form-card__num">1</span>
                            <div>
                                <h2>Billing details</h2>
                                <p>Used on your receipt and licence record.</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="billing_name">Full name</label>
                                <input class="form-control" type="text" id="billing_name" name="billing_name" value="{{ old('billing_name', $user->name) }}" required maxlength="120" autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="billing_email">Email address</label>
                                <input class="form-control" type="email" id="billing_email" name="billing_email" value="{{ old('billing_email', $user->email) }}" required maxlength="150" autocomplete="email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="billing_phone">Phone <span class="text-muted-soft" style="text-transform: none; letter-spacing: 0;">(optional)</span></label>
                                <input class="form-control" type="text" id="billing_phone" name="billing_phone" value="{{ old('billing_phone', $user->phone) }}" maxlength="30" autocomplete="tel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="billing_address">Address <span class="text-muted-soft" style="text-transform: none; letter-spacing: 0;">(optional)</span></label>
                                <input class="form-control" type="text" id="billing_address" name="billing_address" value="{{ old('billing_address') }}" maxlength="255" autocomplete="street-address" placeholder="Not required for digital delivery">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="notes">Order notes <span class="text-muted-soft" style="text-transform: none; letter-spacing: 0;">(optional)</span></label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="500" placeholder="Anything we should know about this order?">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="form-card__head">
                            <span class="form-card__num">2</span>
                            <div>
                                <h2>Payment method</h2>
                                <p>Payments stay pending until you verify that the money was actually received.</p>
                            </div>
                        </div>

                        <div class="payment-options">
                            <label class="payment-option" data-payment-option>
                                <input type="radio" name="payment_method" value="gcash" @checked(old('payment_method', 'gcash') === 'gcash') required>
                                <div class="payment-option__row">
                                    <span class="payment-option__radio"></span>
                                    <span class="payment-option__label"><i class="bi bi-qr-code"></i> GCash QR</span>
                                    <span class="payment-option__tag">Manual verification</span>
                                </div>
                                <p class="payment-option__hint">Scan the QR code, pay the exact total, then enter your transaction reference. Your order stays pending until the payment is checked.</p>
                            </label>

                            <label class="payment-option" data-payment-option>
                                <input type="radio" name="payment_method" value="card" @checked(old('payment_method') === 'card')>
                                <div class="payment-option__row">
                                    <span class="payment-option__radio"></span>
                                    <span class="payment-option__label"><i class="bi bi-credit-card-2-front"></i> Credit or debit card</span>
                                    <span class="payment-option__tag">Not connected</span>
                                </div>
                                <p class="payment-option__hint">A real card gateway must be connected before card payments can be accepted. Do not enter card details on this page.</p>
                            </label>

                            <label class="payment-option" data-payment-option>
                                <input type="radio" name="payment_method" value="paypal" @checked(old('payment_method') === 'paypal')>
                                <div class="payment-option__row">
                                    <span class="payment-option__radio"></span>
                                    <span class="payment-option__label"><i class="bi bi-paypal"></i> PayPal</span>
                                    <span class="payment-option__tag">Not connected</span>
                                </div>
                                <p class="payment-option__hint">A real PayPal integration must be connected before PayPal payments can be accepted.</p>
                            </label>
                        </div>

                        <div class="payment-fields is-visible" data-gcash-fields>
                            <div style="text-align:center; padding: 18px 0;">
                                <h3>Pay with GCash</h3>
                                <p>Scan this QR code with your GCash app and pay exactly <strong>{{ money($total) }}</strong>.</p>
                                <img src="{{ asset('images/gcash-qr.jpg') }}" alt="GCash payment QR code" style="width:min(280px, 100%); height:auto; background:#fff; padding:10px; border-radius:12px;">
                                <p class="text-muted-soft" style="font-size:.85rem; margin-top:12px;">Keep your GCash receipt. Submitting a reference does not automatically prove payment.</p>
                            </div>
                            <div>
                                <label class="form-label" for="payment_reference">GCash transaction reference</label>
                                <input class="form-control" type="text" id="payment_reference" name="payment_reference" value="{{ old('payment_reference') }}" maxlength="100" placeholder="Enter the reference shown in GCash">
                            </div>
                        </div>

                        <div class="payment-sandbox-note">
                            <i class="bi bi-shield-exclamation"></i>
                            <span><strong>Payment verification required.</strong> Orders are not marked paid and downloads stay locked until you independently verify the payment in your GCash records or through a connected payment provider. Card and PayPal options are placeholders until real gateways are configured.</span>
                        </div>
                    </div>

                    <div class="form-card">
                        <label class="terms-check">
                            <input class="form-check-input" type="checkbox" name="terms" value="1" @checked(old('terms')) required>
                            <span>I agree to the Artiva personal-use licence: I may use this artwork personally, but I will not resell, redistribute or claim authorship. The artist retains copyright.</span>
                        </label>

                        <button class="btn btn-ink btn-lg btn-block" type="submit" style="margin-top: 18px;">
                            <i class="bi bi-lock"></i> Pay {{ money($total) }} and download
                        </button>

                        <p class="text-muted-soft" style="font-size: 0.78rem; text-align: center; margin-top: 12px;">
                            By paying you confirm your billing details are correct.
                        </p>
                    </div>
                </div>

                <aside class="summary-card">
                    <h2>Order summary</h2>

                    <div class="summary-items">
                        @foreach ($items as $item)
                            <div class="summary-item">
                                <div class="summary-item__thumb">
                                    @if ($item->artwork->previewUrl())
                                        <img src="{{ $item->artwork->previewUrl() }}" alt="">
                                    @else
                                        {{ mb_substr($item->artwork->title, 0, 1) }}
                                    @endif
                                </div>
                                <div class="summary-item__body">
                                    <span class="summary-item__title">{{ $item->artwork->title }}</span>
                                    <span class="summary-item__meta">{{ $item->artwork->artist_name }} &middot; {{ $item->artwork->file_format }}</span>
                                </div>
                                <span class="summary-item__price">{{ money($item->lineTotal()) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>{{ money($subtotal) }}</span>
                    </div>

                    <div class="summary-row">
                        <span>Tax</span>
                        <span>{{ money($tax) }}</span>
                    </div>

                    <div class="summary-row">
                        <span>Delivery</span>
                        <span>Instant download</span>
                    </div>

                    <div class="summary-row summary-row--total">
                        <span>Total</span>
                        <span>{{ money($total) }}</span>
                    </div>

                    <div class="summary-note">
                        <i class="bi bi-shield-check"></i>
                        <span>Downloads are locked to your account. Master files are stored privately and served only after a verified purchase.</span>
                    </div>

                    <a class="summary-card__secondary" href="{{ route('cart.index') }}">
                        Edit cart
                    </a>
                </aside>
            </form>
        </div>
    </section>
@endsection
