@extends('layouts.admin')

@section('title', 'Orders — Artiva Admin')
@section('eyebrow', 'Sales')
@section('heading', 'Order management')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>Orders</h2>
            <p>Review payments, update fulfilment status and investigate individual transactions.</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.reports.index') }}"><i class="bi bi-graph-up"></i> Sales report</a>
        </div>
    </div>

    <div class="metric-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-cash-stack"></i> Revenue</span>
            <strong class="metric-card__value">{{ money($summary['revenue']) }}</strong>
            <span class="metric-card__hint">From paid orders</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-hourglass-split"></i> Open orders</span>
            <strong class="metric-card__value">{{ $summary['pending'] }}</strong>
            <span class="metric-card__hint">Pending or processing</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-receipt"></i> All orders</span>
            <strong class="metric-card__value">{{ $summary['total'] }}</strong>
            <span class="metric-card__hint">Lifetime order count</span>
        </div>
    </div>

    <div class="admin-card">
        <form class="admin-filters" method="GET" action="{{ route('admin.orders.index') }}">
            <div class="admin-filters__search">
                <i class="bi bi-search"></i>
                <input type="search" name="q" value="{{ $term }}" placeholder="Search order number, name or email">
            </div>

            <select name="status" data-admin-auto-submit aria-label="Filter by status">
                <option value="">Any status</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>

            <button class="btn btn-ghost btn-sm" type="submit">Filter</button>
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.orders.index') }}">Reset</a>
        </form>

        @if ($orders->isEmpty())
            <div class="admin-empty">
                <i class="bi bi-receipt"></i>
                No orders match these filters.
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <span class="admin-table__title">{{ $order->order_number }}</span>
                                    <span class="admin-table__sub">{{ $order->created_at->format('j M Y, g:i a') }}</span>
                                </td>
                                <td>
                                    {{ $order->billing_name }}
                                    <span class="admin-table__sub d-block">{{ $order->billing_email }}</span>
                                </td>
                                <td class="admin-table__num">{{ $order->items_count }}</td>
                                <td class="admin-table__num">{{ money($order->total) }}</td>
                                <td>
                                    <span class="pill {{ $order->payment_status === 'paid' ? 'pill--success' : ($order->payment_status === 'refunded' ? 'pill--muted' : 'pill--warning') }}">
                                        {{ $order->paymentStatusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="pill {{ in_array($order->status, ['completed'], true) ? 'pill--success' : ($order->status === 'cancelled' ? 'pill--danger' : 'pill--sky') }}">
                                        {{ $order->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="icon-action" href="{{ route('admin.orders.show', $order) }}" aria-label="View order {{ $order->order_number }}">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="pagination-wrap">
        {{ $orders->links() }}
    </div>
@endsection
