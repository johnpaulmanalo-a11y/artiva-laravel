@extends('layouts.admin')

@section('title', 'Dashboard — Artiva Admin')
@section('eyebrow', 'Overview')
@section('heading', 'Dashboard')

@section('content')
    @php
        $maxRevenue = collect($salesByMonth)->max('revenue') ?: 1;
        $totalMonthOrders = collect($salesByMonth)->sum('orders');
        $formatMoney = fn ($amount) => money($amount);
    @endphp

    <div class="admin-page-head">
        <div>
            <h2>Welcome back, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}.</h2>
            <p>Here is how Artiva is performing today. Revenue, catalogue and customer activity at a glance.</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.reports.index') }}"><i class="bi bi-graph-up"></i> Sales report</a>
            <a class="btn btn-ink btn-sm" href="{{ route('admin.artworks.create') }}"><i class="bi bi-plus-lg"></i> Add artwork</a>
        </div>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-cash-stack"></i> Total revenue</span>
            <strong class="metric-card__value">{{ $formatMoney($metrics['revenue']) }}</strong>
            <span class="metric-card__hint">{{ $metrics['paid_orders'] }} paid {{ \Illuminate\Support\Str::plural('order', $metrics['paid_orders']) }}</span>
        </div>

        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-receipt"></i> Orders</span>
            <strong class="metric-card__value">{{ number_format($metrics['orders']) }}</strong>
            <span class="metric-card__hint">{{ $metrics['pending_orders'] }} awaiting completion</span>
        </div>

        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-people"></i> Customers</span>
            <strong class="metric-card__value">{{ number_format($metrics['customers']) }}</strong>
            <span class="metric-card__hint">Registered collectors</span>
        </div>

        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-images"></i> Artworks</span>
            <strong class="metric-card__value">{{ number_format($metrics['artworks']) }}</strong>
            <span class="metric-card__hint">Average order {{ $formatMoney($metrics['average_order']) }}</span>
        </div>
    </div>

    <div class="admin-grid admin-grid--split">
        <div class="admin-card">
            <div class="admin-card__head">
                <div>
                    <h3>Revenue trend</h3>
                    <p>Paid orders over the last six months</p>
                </div>
                <a class="admin-card__link" href="{{ route('admin.reports.index') }}">Full report</a>
            </div>

            @if (count($salesByMonth) > 0)
                <div class="bar-chart" role="img" aria-label="Monthly revenue chart">
                    @foreach ($salesByMonth as $month)
                        <div class="bar-chart__col">
                            <div class="bar-chart__bar"
                                 style="--bar-height: {{ max(4, round(($month['revenue'] / $maxRevenue) * 100)) }}%"
                                 data-value="{{ $formatMoney($month['revenue']) }}"
                                 title="{{ $month['label'] }}: {{ $formatMoney($month['revenue']) }}"></div>
                            <span class="bar-chart__label">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="chart-legend">
                    <span><i></i> Revenue per month</span>
                    <span>{{ $totalMonthOrders }} {{ \Illuminate\Support\Str::plural('order', $totalMonthOrders) }} in the period</span>
                </div>
            @else
                <div class="admin-empty">
                    <i class="bi bi-bar-chart"></i>
                    No paid orders yet — sales will appear here once the first checkout completes.
                </div>
            @endif
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <div>
                    <h3>Best sellers</h3>
                    <p>By paid units</p>
                </div>
                <a class="admin-card__link" href="{{ route('admin.artworks.index') }}">Catalogue</a>
            </div>

            @forelse ($topArtworks as $artwork)
                <div class="progress-row">
                    <div class="progress-row__top">
                        <span class="progress-row__name">{{ $artwork->title }}</span>
                        <span class="progress-row__value">{{ $artwork->sold_count }} sold</span>
                    </div>
                    <div class="progress-track">
                        <span style="width: {{ max(6, round(($artwork->sold_count / max($topArtworks->max('sold_count'), 1)) * 100)) }}%"></span>
                    </div>
                </div>
            @empty
                <div class="admin-empty">
                    <i class="bi bi-graph-up-arrow"></i>
                    No sales recorded yet.
                </div>
            @endforelse
        </div>
    </div>

    <div class="admin-grid admin-grid--split" style="margin-top: 20px;">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head" style="padding: 22px 22px 0;">
                <div>
                    <h3>Recent orders</h3>
                    <p>Latest activity across the store</p>
                </div>
                <a class="admin-card__link" href="{{ route('admin.orders.index') }}">All orders</a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="admin-empty">
                    <i class="bi bi-receipt"></i>
                    No orders have been placed yet.
                </div>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td>
                                        <span class="admin-table__title nowrap">{{ $order->order_number }}</span>
                                        <span class="admin-table__sub">{{ $order->created_at->format('j M Y') }} &middot; {{ $order->items_count }} items</span>
                                    </td>
                                    <td>
                                        {{ $order->user->name }}
                                        <span class="admin-table__sub d-block">{{ $order->user->email }}</span>
                                    </td>
                                    <td class="admin-table__num">{{ $formatMoney($order->total) }}</td>
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
                    <h3>Catalogue by category</h3>
                    <p>Active and inactive pieces</p>
                </div>
                <a class="admin-card__link" href="{{ route('admin.categories.index') }}">Manage</a>
            </div>

            @forelse ($categoryTotals as $category)
                <div class="progress-row">
                    <div class="progress-row__top">
                        <span class="progress-row__name">{{ $category->name }}</span>
                        <span class="progress-row__value">{{ $category->artworks_count }} {{ \Illuminate\Support\Str::plural('piece', $category->artworks_count) }}</span>
                    </div>
                    <div class="progress-track">
                        <span style="width: {{ max(5, round(($category->artworks_count / max($categoryTotals->max('artworks_count'), 1)) * 100)) }}%"></span>
                    </div>
                </div>
            @empty
                <div class="admin-empty">
                    <i class="bi bi-tags"></i>
                    No categories yet.
                </div>
            @endforelse
        </div>
    </div>

    @if ($inactiveArtworks->isNotEmpty())
        <div class="admin-card" style="margin-top: 20px;">
            <div class="admin-card__head">
                <div>
                    <h3>Hidden from the storefront</h3>
                    <p>These artworks are inactive and cannot be purchased</p>
                </div>
                <a class="admin-card__link" href="{{ route('admin.artworks.index', ['status' => 'inactive']) }}">View all</a>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Artwork</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inactiveArtworks as $artwork)
                            <tr>
                                <td>
                                    <div class="admin-table__product">
                                        <div class="admin-table__thumb">
                                            @if ($artwork->previewUrl())
                                                <img src="{{ $artwork->previewUrl() }}" alt="">
                                            @else
                                                {{ mb_substr($artwork->title, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <span class="admin-table__title">{{ $artwork->title }}</span>
                                            <span class="admin-table__sub">{{ $artwork->artist_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $artwork->category->name }}</td>
                                <td class="admin-table__num">{{ $formatMoney($artwork->price) }}</td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="icon-action" href="{{ route('admin.artworks.edit', $artwork) }}" aria-label="Edit artwork">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
