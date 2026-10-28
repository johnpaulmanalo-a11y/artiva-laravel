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
                                <p>This project uses a simulated gateway for demonstration.</p>
                            </div>
                        </div>

                        <div class="payment-options">
                            <label class="payment-option" data-payment-option>
                                <input type="radio" name="payment_method" value="card" @checked(old('payment_method', 'card') === 'card')>
                                <div class="payment-option__row">
                                    <span class="payment-option__radio"></span>
                                    <span class="payment-option__label"><i class="bi bi-credit-card-2-front"></i> Credit or debit card</span>
                                    <span class="payment-option__tag">Sandbox</span>
                                </div>
                                <p class="payment-option__hint">Test any 16-digit number. Nothing is charged.</p>
                            </label>

                            <label class="payment-option" data-payment-option>
                                <input type="radio" name="payment_method" value="paypal" @checked(old('payment_method') === 'paypal')>
                                <div class="payment-option__row">
                                    <span class="payment-option__radio"></span>
                                    <span class="payment-option__label"><i class="bi bi-paypal"></i> PayPal</span>
                                    <span class="payment-option__tag">Sandbox</span>
                                </div>
                                <p class="payment-option__hint">Simulated redirect — approved instantly for the demo.</p>
                            </label>

                            <label class="payment-option" data-payment-option>
                                <input type="radio" name="payment_method" value="simulated" @checked(old('payment_method') === 'simulated')>
                                <div class="payment-option__row">
                                    <span class="payment-option__radio"></span>
                                    <span class="payment-option__label"><i class="bi bi-cpu"></i> Artiva simulated gateway</span>
                                </div>
                                <p class="payment-option__hint">No card details required. Marks the order paid for demonstration.</p>
                            </label>
                        </div>

                        <div class="payment-fields {{ old('payment_method', 'card') === 'card' ? 'is-visible' : '' }}" data-card-fields>
                            <div class="card-preview">
                                <div class="card-preview__top">
                                    <span>Artiva sandbox</span>
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <div class="card-preview__number" data-card-preview-number>•••• •••• •••• ••••</div>
                                <div class="card-preview__bottom">
                                    <span>{{ strtoupper($user->name) }}</span>
                                    <span data-card-preview-expiry>MM/YY</span>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="card_number">Card number</label>
                                    <input class="form-control" type="text" id="card_number" name="card_number" inputmode="numeric" autocomplete="cc-number" placeholder="4242 4242 4242 4242" maxlength="23">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="card_expiry">Expiry</label>
                                    <input class="form-control" type="text" id="card_expiry" name="card_expiry" inputmode="numeric" autocomplete="cc-exp" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="card_cvv">CVV</label>
                                    <input class="form-control" type="text" id="card_cvv" name="card_cvv" inputmode="numeric" autocomplete="cc-csc" placeholder="123" maxlength="4">
                                </div>
                            </div>

                            <div class="payment-sandbox-note">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span><strong>Demonstration mode.</strong> These fields are validated but never sent to a real payment provider. Use any test numbers, for example 4242 4242 4242 4242 with 12/30 and 123.</span>
                            </div>
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
