@extends('layouts.admin')

@section('title', 'Sales reports — Artiva Admin')
@section('eyebrow', 'Sales')
@section('heading', 'Sales reports')

@section('content')
    @php
        $maxRevenue = collect($monthly)->max('revenue') ?: 1;
        $statusTotal = $statusBreakdown->sum() ?: 1;
    @endphp

    <div class="admin-page-head">
        <div>
            <h2>Performance report</h2>
            <p>Sales between {{ $from->format('j M Y') }} and {{ $to->format('j M Y') }}. Only paid orders are counted.</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a>
        </div>
    </div>

    <div class="admin-card" style="margin-bottom: 20px;">
        <form class="admin-filters" method="GET" action="{{ route('admin.reports.index') }}" style="margin-bottom: 0;">
            <label class="mono-label" for="from" style="margin: 0;">From</label>
            <input type="date" id="from" name="from" value="{{ $from->format('Y-m-d') }}">

            <label class="mono-label" for="to" style="margin: 0;">To</label>
            <input type="date" id="to" name="to" value="{{ $to->format('Y-m-d') }}">

            <button class="btn btn-ink btn-sm" type="submit">Apply range</button>
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.reports.index') }}">Reset</a>
        </form>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-cash-stack"></i> Revenue</span>
            <strong class="metric-card__value">{{ money($totals['revenue']) }}</strong>
            <span class="metric-card__hint">{{ $totals['orders'] }} paid {{ \Illuminate\Support\Str::plural('order', $totals['orders']) }}</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-graph-up-arrow"></i> Average order</span>
            <strong class="metric-card__value">{{ money($totals['average']) }}</strong>
            <span class="metric-card__hint">Revenue ÷ paid orders</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-box-seam"></i> Units sold</span>
            <strong class="metric-card__value">{{ number_format($totals['units']) }}</strong>
            <span class="metric-card__hint">Artwork licences</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-people"></i> Unique buyers</span>
            <strong class="metric-card__value">{{ number_format($totals['buyers']) }}</strong>
            <span class="metric-card__hint">{{ number_format($totals['catalogue']) }} artworks in catalogue</span>
        </div>
    </div>

    <div class="admin-grid admin-grid--split">
        <div class="admin-card">
            <div class="admin-card__head">
                <div>
                    <h3>Monthly revenue</h3>
                    <p>Paid orders inside the selected range</p>
                </div>
            </div>

            @if (count($monthly) > 0)
                <div class="bar-chart" role="img" aria-label="Monthly revenue chart">
                    @foreach ($monthly as $month)
                        <div class="bar-chart__col">
                            <div class="bar-chart__bar"
                                 style="--bar-height: {{ max(3, round(($month['revenue'] / $maxRevenue) * 100)) }}%"
                                 data-value="{{ money($month['revenue']) }}"
                                 title="{{ $month['label'] }}: {{ money($month['revenue']) }}"></div>
                            <span class="bar-chart__label">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="chart-legend">
                    <span><i></i> Revenue</span>
                    <span>Peak month {{ money($peak) }}</span>
                </div>
            @else
                <div class="admin-empty">
                    <i class="bi bi-bar-chart"></i>
                    No paid orders in this date range.
                </div>
            @endif
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <div>
                    <h3>Order status mix</h3>
                    <p>All orders, not just paid</p>
                </div>
            </div>

            @forelse ($statusBreakdown as $status => $count)
                <div class="progress-row">
                    <div class="progress-row__top">
                        <span class="progress-row__name">{{ ucfirst($status) }}</span>
                        <span class="progress-row__value">{{ $count }} ({{ round(($count / $statusTotal) * 100) }}%)</span>
                    </div>
                    <div class="progress-track">
                        <span style="width: {{ max(4, round(($count / $statusTotal) * 100)) }}%"></span>
                    </div>
                </div>
            @empty
                <div class="admin-empty">
                    <i class="bi bi-pie-chart"></i>
                    No orders recorded yet.
                </div>
            @endforelse
        </div>
    </div>

    <div class="admin-grid admin-grid--split" style="margin-top: 20px;">
        <div class="admin-card admin-card--flush">
            <div class="admin-card__head" style="padding: 22px 22px 0;">
                <div>
                    <h3>Top selling artwork</h3>
                    <p>Ranked by units sold in this range</p>
                </div>
            </div>

            @if ($topArtworks->isEmpty())
                <div class="admin-empty">
                    <i class="bi bi-images"></i>
                    No artwork sold in this range.
                </div>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Artwork</th>
                                <th>Units</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topArtworks as $row)
                                <tr>
                                    <td>
                                        <span class="admin-table__title">{{ $row->title }}</span>
                                        <span class="admin-table__sub">Artwork #{{ $row->artwork_id ?? '—' }}</span>
                                    </td>
                                    <td class="admin-table__num">{{ $row->units }}</td>
                                    <td class="admin-table__num text-end">{{ money($row->revenue) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="admin-card admin-card--flush">
            <div class="admin-card__head" style="padding: 22px 22px 0;">
                <div>
                    <h3>Revenue by category</h3>
                    <p>Where the money comes from</p>
                </div>
            </div>

            @if ($categoryRevenue->isEmpty())
                <div class="admin-empty">
                    <i class="bi bi-tags"></i>
                    No category sales in this range.
                </div>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Units</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoryRevenue as $row)
                                <tr>
                                    <td><span class="admin-table__title">{{ $row->category_name }}</span></td>
                                    <td class="admin-table__num">{{ $row->units }}</td>
                                    <td class="admin-table__num text-end">{{ money($row->revenue) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="admin-card admin-card--flush" style="margin-top: 20px;">
        <div class="admin-card__head" style="padding: 22px 22px 0;">
            <div>
                <h3>Top customers</h3>
                <p>Lifetime paid order count, spend inside this range</p>
            </div>
            <a class="admin-card__link" href="{{ route('admin.customers.index') }}">All customers</a>
        </div>

        @if ($topCustomers->isEmpty())
            <div class="admin-empty">
                <i class="bi bi-people"></i>
                No customers yet.
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Paid orders</th>
                            <th class="text-end">Spend in range</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topCustomers as $customer)
                            <tr>
                                <td>
                                    <div class="admin-table__product">
                                        <span class="avatar avatar--sky avatar--sm">{{ $customer->initials() }}</span>
                                        <div>
                                            <span class="admin-table__title">{{ $customer->name }}</span>
                                            <span class="admin-table__sub">{{ $customer->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="admin-table__num">{{ $customer->paid_orders }}</td>
                                <td class="admin-table__num text-end">{{ money($customer->spent ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
