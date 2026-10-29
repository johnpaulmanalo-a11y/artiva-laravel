@extends('layouts.admin')

@section('title', $customer->name.' — Artiva Admin')
@section('eyebrow', 'People')
@section('heading', 'Customer profile')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>{{ $customer->name }}</h2>
            <p>{{ $customer->email }} &middot; Joined {{ $customer->created_at->format('j F Y') }}</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.customers.index') }}"><i class="bi bi-arrow-left"></i> Customers</a>
        </div>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-receipt"></i> Orders</span>
            <strong class="metric-card__value">{{ $stats['orders'] }}</strong>
            <span class="metric-card__hint">All time</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-cash-stack"></i> Spend</span>
            <strong class="metric-card__value">{{ money($stats['spent']) }}</strong>
            <span class="metric-card__hint">Paid orders only</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-collection"></i> Library</span>
            <strong class="metric-card__value">{{ $stats['library'] }}</strong>
            <span class="metric-card__hint">Artworks owned</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-heart"></i> Wishlist</span>
            <strong class="metric-card__value">{{ $stats['wishlist'] }}</strong>
            <span class="metric-card__hint">Saved pieces</span>
        </div>
    </div>

    <div class="admin-grid admin-grid--split">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head" style="padding: 22px 22px 0;">
                <div>
                    <h3>Order history</h3>
                    <p>Every order placed by this customer</p>
                </div>
            </div>

            @if ($orders->isEmpty())
                <div class="admin-empty">
                    <i class="bi bi-receipt"></i>
                    This customer has not ordered yet.
                </div>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <span class="admin-table__title">{{ $order->order_number }}</span>
                                        <span class="admin-table__sub">{{ $order->items_count }} items</span>
                                    </td>
                                    <td>{{ $order->created_at->format('j M Y') }}</td>
                                    <td class="admin-table__num">{{ money($order->total) }}</td>
                                    <td>
                                        <span class="pill {{ $order->payment_status === 'paid' ? 'pill--success' : 'pill--warning' }}">
                                            {{ $order->statusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-table__actions">
                                            <a class="icon-action" href="{{ route('admin.orders.show', $order) }}" aria-label="View order">
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

        <div class="admin-card">
            <div class="admin-card__head">
                <div>
                    <h3>Account details</h3>
                </div>
            </div>

            <dl class="detail-list">
                <div class="detail-list__row">
                    <dt>Name</dt>
                    <dd>{{ $customer->name }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Email</dt>
                    <dd>{{ $customer->email }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Phone</dt>
                    <dd>{{ $customer->phone ?: '—' }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Role</dt>
                    <dd>{{ ucfirst($customer->role) }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Registered</dt>
                    <dd>{{ $customer->created_at->format('j M Y') }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Last updated</dt>
                    <dd>{{ $customer->updated_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="pagination-wrap">
        {{ $orders->links() }}
    </div>
@endsection
