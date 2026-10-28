@extends('layouts.app')

@section('title', 'Order history — Artiva')
@section('meta_description', 'Review your past Artiva orders, receipts and downloads.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="page-head">
                <div>
                    <p class="eyebrow">Your account</p>
                    <h1>Order history</h1>
                    <p>Every purchase, receipt and download in one place. Open an order to re-download your files.</p>
                </div>
                <a class="link-arrow" href="{{ route('library.index') }}"><i class="bi bi-collection"></i> Go to library</a>
            </div>

            <div class="orders-tabs">
                <a class="orders-tab {{ $status === '' ? 'is-active' : '' }}" href="{{ route('orders.index') }}">All orders</a>
                @foreach ($statuses as $option)
                    <a class="orders-tab {{ $status === $option ? 'is-active' : '' }}"
                       href="{{ route('orders.index', ['status' => $option]) }}">
                        {{ ucfirst($option) }}
                    </a>
                @endforeach
            </div>

            @if ($orders->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon"><i class="bi bi-receipt"></i></div>
                    <h3>{{ $status ? 'No '.$status.' orders' : 'No orders yet' }}</h3>
                    <p>When you buy artwork it will appear here with its receipt and download links.</p>
                    <a class="btn btn-ink" href="{{ route('artworks.index') }}">Explore artwork</a>
                </div>
            @else
                <div class="order-list">
                    @foreach ($orders as $order)
                        <article class="order-card">
                            <div>
                                <a class="order-card__number" href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a>
                                <span class="order-card__date">{{ $order->created_at->format('j M Y, g:i a') }}</span>
                            </div>

                            <div>
                                <span class="order-card__label">Items</span>
                                <span class="order-card__value">{{ $order->items_count }} {{ \Illuminate\Support\Str::plural('artwork', $order->items_count) }}</span>
                            </div>

                            <div>
                                <span class="order-card__label">Total</span>
                                <span class="order-card__value">{{ money($order->total) }}</span>
                            </div>

                            <div class="d-flex align-items-center gap-2 justify-content-end">
                                <span class="pill {{ $order->payment_status === 'paid' ? 'pill--success' : ($order->payment_status === 'refunded' ? 'pill--muted' : 'pill--warning') }}">
                                    {{ $order->statusLabel() }}
                                </span>
                                <a class="btn btn-outline-ink btn-sm" href="{{ route('orders.show', $order) }}">View</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
