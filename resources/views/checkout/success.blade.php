@extends('layouts.app')

@section('title', 'Order '.$order->order_number.' confirmed — Artiva')
@section('meta_description', 'Your Artiva order is confirmed. Download your artwork from your library.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="confirm-hero">
                <div class="confirm-hero__icon"><i class="bi bi-check2"></i></div>
                <h1>Thank you — your art is ready.</h1>
                <p>
                    Payment was approved and every artwork in this order is now unlocked in your library.
                    A receipt has been recorded against your account.
                </p>

                <div class="confirm-meta">
                    <div class="confirm-meta__item">
                        <span>Order number</span>
                        <strong>{{ $order->order_number }}</strong>
                    </div>
                    <div class="confirm-meta__item">
                        <span>Placed on</span>
                        <strong>{{ $order->created_at->format('j M Y, g:i a') }}</strong>
                    </div>
                    <div class="confirm-meta__item">
                        <span>Total paid</span>
                        <strong>{{ money($order->total) }}</strong>
                    </div>
                    <div class="confirm-meta__item">
                        <span>Payment</span>
                        <strong>{{ $order->paymentStatusLabel() }}</strong>
                    </div>
                </div>
            </div>

            <div class="confirm-layout">
                <div>
                    <div class="order-panel">
                        <div class="order-panel__head">
                            <h2>Your downloads</h2>
                            <span class="pill pill--success"><i class="bi bi-unlock"></i> Unlocked</span>
                        </div>

                        @foreach ($order->items as $item)
                            <div class="owned-item">
                                <div class="owned-item__thumb">
                                    @if ($item->artwork && $item->artwork->previewUrl())
                                        <img src="{{ $item->artwork->previewUrl() }}" alt="{{ $item->title }}">
                                    @else
                                        {{ mb_substr($item->title, 0, 1) }}
                                    @endif
                                </div>

                                <div class="owned-item__body">
                                    <span class="owned-item__title">{{ $item->title }}</span>
                                    <span class="owned-item__meta">{{ $item->artist_name ?: 'Artiva artist' }} &middot; {{ money($item->price) }}</span>
                                </div>

                                @if ($item->artwork)
                                    <a class="btn btn-ink btn-sm" href="{{ route('library.download', $item->artwork) }}">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="order-panel" style="margin-top: 20px;">
                        <div class="order-panel__head">
                            <h2>What happens next</h2>
                        </div>

                        <div class="stack">
                            <p class="text-muted-soft" style="font-size: 0.9rem;">
                                <i class="bi bi-collection" style="color: var(--sky);"></i>
                                Everything you buy stays in <a href="{{ route('library.index') }}" style="text-decoration: underline;">your library</a>, so you can download it again on any device.
                            </p>
                            <p class="text-muted-soft" style="font-size: 0.9rem;">
                                <i class="bi bi-receipt" style="color: var(--sky);"></i>
                                The full record of this order lives in <a href="{{ route('orders.index') }}" style="text-decoration: underline;">your order history</a>.
                            </p>
                            <p class="text-muted-soft" style="font-size: 0.9rem;">
                                <i class="bi bi-envelope" style="color: var(--sky);"></i>
                                Need help with a file? <a href="{{ route('contact') }}" style="text-decoration: underline;">Contact support</a> with your order number.
                            </p>
                        </div>
                    </div>
                </div>

                <aside>
                    <div class="summary-card">
                        <h2>Order total</h2>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>{{ money($order->subtotal) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax</span>
                            <span>{{ money($order->tax) }}</span>
                        </div>
                        <div class="summary-row summary-row--total">
                            <span>Total</span>
                            <span>{{ money($order->total) }}</span>
                        </div>

                        <div class="summary-row" style="margin-top: 12px;">
                            <span>Status</span>
                            <span><span class="pill pill--success">{{ $order->statusLabel() }}</span></span>
                        </div>

                        <a class="btn btn-ink btn-block" href="{{ route('library.index') }}">
                            <i class="bi bi-collection"></i> Go to my library
                        </a>

                        <a class="summary-card__secondary" href="{{ route('artworks.index') }}">
                            Continue browsing
                        </a>
                    </div>

                    @if ($order->payments->isNotEmpty())
                        <div class="summary-card" style="margin-top: 18px;">
                            <h2>Payment record</h2>

                            @foreach ($order->payments as $payment)
                                <div class="payment-record">
                                    <div>
                                        <div class="payment-record__id">{{ $payment->transaction_id }}</div>
                                        <div class="payment-record__meta">
                                            {{ ucfirst($payment->method) }} &middot; {{ $payment->paid_at?->format('j M Y') ?? 'Pending' }}
                                        </div>
                                    </div>
                                    <span class="pill {{ $payment->status === 'paid' ? 'pill--success' : 'pill--warning' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
