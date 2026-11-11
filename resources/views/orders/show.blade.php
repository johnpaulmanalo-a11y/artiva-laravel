@extends('layouts.app')

@section('title', 'Order '.$order->order_number.' — Artiva')
@section('meta_description', 'Review the details of your Artiva order and purchased artwork.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="page-head">
                <div>
                    <p class="eyebrow">Your account</p>
                    <h1>Order details</h1>
                    <p class="order-number">{{ $order->order_number }}</p>
                </div>
                <a class="btn btn-outline-ink btn-sm" href="{{ route('orders.index') }}">
                    <i class="bi bi-arrow-left"></i> Order history
                </a>
            </div>

            <div class="order-detail">
                <div>
                    <section class="summary-card">
                        <h2>Items in this order</h2>

                        @forelse ($order->items as $item)
                            <div class="order-line">
                                <div class="order-line__thumb">
                                    @if ($item->artwork?->previewUrl())
                                        <img src="{{ $item->artwork->previewUrl() }}" alt="{{ $item->title }}">
                                    @else
                                        {{ mb_substr($item->title, 0, 1) }}
                                    @endif
                                </div>

                                <div class="order-line__body">
                                    <span class="order-line__title">{{ $item->title }}</span>
                                    <span class="order-line__meta">
                                        {{ $item->artist_name ?: 'Artiva artist' }} &middot;
                                        {{ $item->quantity }} {{ \Illuminate\Support\Str::plural('copy', $item->quantity) }}
                                    </span>
                                </div>

                                <span class="order-line__price">{{ money($item->subtotal) }}</span>

                                @if ($order->isPaid() && $item->artwork)
                                    <a class="btn btn-ink btn-sm" href="{{ route('library.download', $item->artwork) }}">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted-soft">This order has no item details.</p>
                        @endforelse
                    </section>

                    <section class="summary-card" style="margin-top: 20px;">
                        <h2>Billing details</h2>
                        <div class="order-meta-grid">
                            <div>
                                <h3>Name</h3>
                                <p>{{ $order->billing_name }}</p>
                            </div>
                            <div>
                                <h3>Email</h3>
                                <p>{{ $order->billing_email }}</p>
                            </div>
                            <div>
                                <h3>Phone</h3>
                                <p>{{ $order->billing_phone ?: '—' }}</p>
                            </div>
                            <div>
                                <h3>Address</h3>
                                <p>{{ $order->billing_address ?: '—' }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <aside>
                    <section class="summary-card">
                        <h2>Order summary</h2>
                        <div class="summary-row">
                            <span>Placed</span>
                            <span>{{ $order->created_at->format('j M Y, g:i a') }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Payment method</span>
                            <span>{{ $order->payment_method === 'card' ? 'Card' : ucfirst($order->payment_method) }}</span>
                        </div>
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
                            <span>Order status</span>
                            <span class="pill {{ $order->status === \App\Models\Order::STATUS_COMPLETED ? 'pill--success' : 'pill--sky' }}">
                                {{ $order->statusLabel() }}
                            </span>
                        </div>
                        <div class="summary-row">
                            <span>Payment status</span>
                            <span class="pill {{ $order->isPaid() ? 'pill--success' : 'pill--warning' }}">
                                {{ $order->paymentStatusLabel() }}
                            </span>
                        </div>

                        @if ($order->isPaid())
                            <a class="btn btn-ink btn-block" href="{{ route('library.index') }}">
                                <i class="bi bi-collection"></i> Go to my library
                            </a>
                        @endif
                    </section>

                    @if ($order->payments->isNotEmpty())
                        <section class="summary-card" style="margin-top: 18px;">
                            <h2>Payment record</h2>
                            @foreach ($order->payments as $payment)
                                <div class="payment-record">
                                    <div>
                                        <div class="payment-record__id">{{ $payment->transaction_id }}</div>
                                        <div class="payment-record__meta">
                                            {{ ucfirst($payment->method) }} &middot; {{ $payment->paid_at?->format('j M Y') ?? 'Pending' }}
                                        </div>
                                    </div>
                                    <span class="pill {{ $payment->status === \App\Models\Order::PAYMENT_PAID ? 'pill--success' : 'pill--warning' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </section>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
