@extends('layouts.app')

@section('title', 'Order '.$order->order_number.' — Artiva')
@section('meta_description', 'Order details, billing information and downloads for your Artiva purchase.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="breadcrumb__sep">/</span>
                <a href="{{ route('orders.index') }}">Orders</a>
                <span class="breadcrumb__sep">/</span>
                <span>{{ $order->order_number }}</span>
            </nav>

            <div class="page-head">
                <div>
                    <p class="eyebrow">Order details</p>
                    <h1 class="order-number">{{ $order->order_number }}</h1>
                    <p>
                        Placed {{ $order->created_at->format('j F Y, g:i a') }} &middot;
                        <span class="pill {{ $order->payment_status === 'paid' ? 'pill--success' : 'pill--warning' }}">{{ $order->paymentStatusLabel() }}</span>
                        <span class="pill pill--muted">{{ $order->statusLabel() }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if ($order->user_id !== auth()->id())
                        <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.orders.show', $order) }}">Open in admin</a>
                    @endif
                    <a class="btn btn-outline-ink btn-sm" href="{{ route('orders.index') }}"><i class="bi bi-arrow-left"></i> All orders</a>
                </div>
            </div>

            <div class="order-detail">
                <div>
                    <div class="order-panel">
                        <div class="order-panel__head">
                            <h2>Artwork in this order</h2>
                            <span class="pill pill--muted">{{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('item', $order->items->count()) }}</span>
                        </div>

                        @foreach ($order->items as $item)
                            <div class="order-line">
                                <div class="order-line__thumb">
                                    @if ($item->artwork && $item->artwork->previewUrl())
                                        <img src="{{ $item->artwork->previewUrl() }}" alt="{{ $item->title }}">
                                    @else
                                        {{ mb_substr($item->title, 0, 1) }}
                                    @endif
                                </div>

                                <div class="order-line__body">
                                    @if ($item->artwork)
                                        <a class="order-line__title" href="{{ route('artworks.show', $item->artwork) }}">{{ $item->title }}</a>
                                    @else
                                        <span class="order-line__title">{{ $item->title }}</span>
                                    @endif
                                    <span class="order-line__meta">
                                        {{ $item->artist_name ?: 'Artiva artist' }} &middot; Qty {{ $item->quantity }}
                                    </span>
                                </div>

                                <span class="order-line__price">{{ money($item->subtotal) }}</span>

                                @if ($item->artwork && in_array($item->artwork_id, $purchasedIds, true))
                                    <a class="btn btn-ghost btn-sm" href="{{ route('library.download', $item->artwork) }}">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                @else
                                    <span class="pill pill--muted">Unavailable</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="order-panel">
                        <div class="order-panel__head">
                            <h2>Billing &amp; delivery</h2>
                        </div>

                        <div class="order-meta-grid">
                            <div>
                                <h3>Billed to</h3>
                                <p>
                                    {{ $order->billing_name }}<br>
                                    {{ $order->billing_email }}
                                    @if ($order->billing_phone)
                                        <br>{{ $order->billing_phone }}
                                    @endif
                                    @if ($order->billing_address)
                                        <br>{{ $order->billing_address }}
                                    @endif
                                </p>
                            </div>

                            <div>
                                <h3>Delivery method</h3>
                                <p>
                                    Digital download<br>
                                    Available immediately after payment
                                    @if ($order->paid_at)
                                        <br>Paid {{ $order->paid_at->format('j M Y, g:i a') }}
                                    @endif
                                </p>
                            </div>

                            <div>
                                <h3>Payment method</h3>
                                <p>
                                    {{ $order->payment_method === 'card' ? 'Credit / debit card' : ucfirst($order->payment_method) }}<br>
                                    Simulated gateway (sandbox)
                                </p>
                            </div>

                            <div>
                                <h3>Order notes</h3>
                                <p>{{ $order->notes ?: 'No notes on this order.' }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($order->payments->isNotEmpty())
                        <div class="order-panel">
                            <div class="order-panel__head">
                                <h2>Payment records</h2>
                            </div>

                            @foreach ($order->payments as $payment)
                                <div class="payment-record">
                                    <div>
                                        <div class="payment-record__id">{{ $payment->transaction_id }}</div>
                                        <div class="payment-record__meta">
                                            {{ ucfirst($payment->method) }} &middot;
                                            {{ $payment->paid_at ? $payment->paid_at->format('j M Y, g:i a') : 'Not completed' }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="payment-record__meta">{{ money($payment->amount) }}</span>
                                        <span class="pill {{ $payment->status === 'paid' ? 'pill--success' : 'pill--warning' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside>
                    <div class="summary-card">
                        <h2>Receipt</h2>

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

                        <a class="btn btn-ink btn-block" href="{{ route('library.index') }}" style="margin-top: 18px;">
                            <i class="bi bi-collection"></i> Open library
                        </a>

                        <div class="summary-note">
                            <i class="bi bi-info-circle"></i>
                            <span>Keep this order number for support: <strong>{{ $order->order_number }}</strong></span>
                        </div>
                    </div>

                    <div class="summary-card" style="margin-top: 18px;">
                        <h2>Need help?</h2>
                        <p class="text-muted-soft" style="font-size: 0.86rem; line-height: 1.7;">
                            If a download will not open or you need a different file format, contact the Artiva team
                            with your order number and we will sort it out.
                        </p>
                        <a class="btn btn-outline-ink btn-block" href="{{ route('contact') }}" style="margin-top: 14px;">
                            Contact support
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
