@extends('layouts.admin')

@section('title', 'Order '.$order->order_number.' — Artiva Admin')
@section('eyebrow', 'Sales')
@section('heading', 'Order details')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>{{ $order->order_number }}</h2>
            <p>
                Placed {{ $order->created_at->format('j F Y, g:i a') }} by
                <a href="{{ route('admin.customers.show', $order->user) }}" style="text-decoration: underline;">{{ $order->user->name }}</a>
            </p>
        </div>
        <div class="admin-page-head__actions">
            <span class="pill {{ $order->payment_status === 'paid' ? 'pill--success' : 'pill--warning' }}">{{ $order->paymentStatusLabel() }}</span>
            <span class="pill {{ $order->status === 'completed' ? 'pill--success' : 'pill--sky' }}">{{ $order->statusLabel() }}</span>
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.orders.index') }}"><i class="bi bi-arrow-left"></i> Orders</a>
        </div>
    </div>

    <div class="detail-grid">
        <div class="admin-card">
            <div class="admin-card__head">
                <div>
                    <h3>Customer</h3>
                </div>
            </div>

            <dl class="detail-list">
                <div class="detail-list__row">
                    <dt>Name</dt>
                    <dd>{{ $order->billing_name }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Email</dt>
                    <dd>{{ $order->billing_email }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Phone</dt>
                    <dd>{{ $order->billing_phone ?: '—' }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Address</dt>
                    <dd>{{ $order->billing_address ?: '—' }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Account</dt>
                    <dd><a href="{{ route('admin.customers.show', $order->user) }}">{{ $order->user->email }}</a></dd>
                </div>
                <div class="detail-list__row">
                    <dt>Payment method</dt>
                    <dd>{{ $order->payment_method === 'card' ? 'Card' : ucfirst($order->payment_method) }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Paid at</dt>
                    <dd>{{ $order->paid_at ? $order->paid_at->format('j M Y, g:i a') : 'Not paid' }}</dd>
                </div>
            </dl>

            <div style="margin-top: 18px; padding-top: 18px; border-top: 1px solid var(--line);">
                <span class="mono-label">Order notes</span>
                <p class="text-muted-soft" style="font-size: 0.88rem; margin-top: 6px;">{{ $order->notes ?: 'No notes on this order.' }}</p>
            </div>
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card__head">
                    <div>
                        <h3>Items</h3>
                        <p>{{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('artwork', $order->items->count()) }}</p>
                    </div>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Artwork</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <span class="admin-table__title">{{ $item->title }}</span>
                                        <span class="admin-table__sub">{{ $item->artist_name ?: 'Artiva artist' }}</span>
                                    </td>
                                    <td class="admin-table__num">{{ money($item->price) }}</td>
                                    <td class="admin-table__num">{{ $item->quantity }}</td>
                                    <td class="admin-table__num text-end">{{ money($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <dl class="detail-list" style="margin-top: 16px;">
                    <div class="detail-list__row">
                        <dt>Subtotal</dt>
                        <dd>{{ money($order->subtotal) }}</dd>
                    </div>
                    <div class="detail-list__row">
                        <dt>Tax</dt>
                        <dd>{{ money($order->tax) }}</dd>
                    </div>
                    <div class="detail-list__row">
                        <dt>Total</dt>
                        <dd style="font-size: 1rem;">{{ money($order->total) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="admin-card" style="margin-top: 20px;">
                <div class="admin-card__head">
                    <div>
                        <h3>Update status</h3>
                        <p>Completing an order confirms delivery. Refunding marks the payment refunded.</p>
                    </div>
                </div>

                <form class="admin-form" method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-grid">
                        <div>
                            <label class="form-label" for="status">Order status</label>
                            <select class="form-select" id="status" name="status" required>
                                @foreach ($statuses as $option)
                                    <option value="{{ $option }}" @selected($order->status === $option)>{{ ucfirst($option) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display: flex; align-items: flex-end;">
                            <button class="btn btn-ink" type="submit">Save status</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="admin-card" style="margin-top: 20px;">
                <div class="admin-card__head">
                    <div>
                        <h3>Payment records</h3>
                    </div>
                </div>

                @forelse ($order->payments as $payment)
                    <div class="payment-record">
                        <div>
                            <div class="payment-record__id">{{ $payment->transaction_id }}</div>
                            <div class="payment-record__meta">
                                {{ ucfirst($payment->method) }} &middot;
                                {{ $payment->paid_at ? $payment->paid_at->format('j M Y, g:i a') : 'Pending' }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="payment-record__meta">{{ money($payment->amount) }}</span>
                            <span class="pill {{ $payment->status === 'paid' ? 'pill--success' : 'pill--warning' }}">{{ ucfirst($payment->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty">
                        <i class="bi bi-credit-card"></i>
                        No payment recorded for this order.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
