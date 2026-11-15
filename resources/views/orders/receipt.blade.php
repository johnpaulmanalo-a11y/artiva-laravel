@extends('layouts.app')

@section('title', 'Receipt '.$receipt->receipt_number.' — Artiva')
@section('meta_description', 'Official Artiva receipt for your verified purchase.')

@section('content')
    <section class="page-shell">
        <div class="container" style="max-width: 760px;">
            <div class="page-head">
                <div>
                    <p class="eyebrow">Artiva purchase receipt</p>
                    <h1>Thank you for your purchase</h1>
                    <p class="text-muted-soft">{{ $receipt->receipt_number }}</p>
                </div>
                <a class="btn btn-outline-ink btn-sm" href="{{ route('orders.show', $receipt->order) }}">Back to order</a>
            </div>

            <section class="summary-card">
                <div class="order-meta-grid">
                    <div>
                        <h3>Customer</h3>
                        <p>{{ $receipt->order->user->name }}</p>
                    </div>
                    <div>
                        <h3>Email</h3>
                        <p>{{ $receipt->order->user->email }}</p>
                    </div>
                    <div>
                        <h3>Order</h3>
                        <p>{{ $receipt->order->order_number }}</p>
                    </div>
                    <div>
                        <h3>Transaction reference</h3>
                        <p>{{ $receipt->payment->provider_reference }}</p>
                    </div>
                    <div>
                        <h3>Payment method</h3>
                        <p>{{ $receipt->payment->method === 'card' ? 'Card' : ucfirst($receipt->payment->method) }}</p>
                    </div>
                    <div>
                        <h3>Verified</h3>
                        <p>{{ $receipt->payment->verified_at->format('j M Y, g:i a') }}</p>
                    </div>
                </div>

                <div class="admin-table-wrap" style="margin-top: 20px;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Artwork / item</th>
                                <th>Qty</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($receipt->order->items as $item)
                                <tr>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">{{ money($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>{{ money($receipt->order->subtotal) }}</span>
                </div>
                <div class="summary-row">
                    <span>Tax</span>
                    <span>{{ money($receipt->order->tax) }}</span>
                </div>
                <div class="summary-row summary-row--total">
                    <span>Total paid</span>
                    <span>{{ money($receipt->order->total) }}</span>
                </div>
                <div class="summary-row">
                    <span>Payment status</span>
                    <span class="pill pill--success">PAID / VERIFIED</span>
                </div>
                <p class="text-muted-soft" style="margin-top: 20px;">Thank you for supporting Artiva and independent artists.</p>
            </section>
        </div>
    </section>
@endsection
