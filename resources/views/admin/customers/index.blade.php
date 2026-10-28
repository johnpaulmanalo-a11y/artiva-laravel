@extends('layouts.admin')

@section('title', 'Customers — Artiva Admin')
@section('eyebrow', 'People')
@section('heading', 'Customers')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>Customer management</h2>
            <p>Registered collectors, their order counts and lifetime spend. Open a customer to review their purchases.</p>
        </div>
    </div>

    <div class="metric-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-people"></i> Customers</span>
            <strong class="metric-card__value">{{ number_format($summary['customers']) }}</strong>
            <span class="metric-card__hint">Registered accounts</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-bag-check"></i> Buyers</span>
            <strong class="metric-card__value">{{ number_format($summary['buyers']) }}</strong>
            <span class="metric-card__hint">At least one paid order</span>
        </div>
        <div class="metric-card">
            <span class="metric-card__label"><i class="bi bi-cash-stack"></i> Lifetime revenue</span>
            <strong class="metric-card__value">{{ money($summary['lifetime']) }}</strong>
            <span class="metric-card__hint">All paid orders</span>
        </div>
    </div>

    <div class="admin-card">
        <form class="admin-filters" method="GET" action="{{ route('admin.customers.index') }}">
            <div class="admin-filters__search">
                <i class="bi bi-search"></i>
                <input type="search" name="q" value="{{ $term }}" placeholder="Search name or email">
            </div>
            <button class="btn btn-ghost btn-sm" type="submit">Search</button>
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.customers.index') }}">Reset</a>
        </form>

        @if ($customers->isEmpty())
            <div class="admin-empty">
                <i class="bi bi-people"></i>
                No customers match this search.
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Orders</th>
                            <th>Spend</th>
                            <th>Last order</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <div class="admin-table__product">
                                        <span class="avatar avatar--sky">{{ $customer->initials() }}</span>
                                        <div>
                                            <span class="admin-table__title">{{ $customer->name }}</span>
                                            <span class="admin-table__sub">{{ $customer->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $customer->phone ?: '—' }}</td>
                                <td class="admin-table__num">{{ $customer->orders_count }}</td>
                                <td class="admin-table__num">{{ money($customer->total_spent ?? 0) }}</td>
                                <td>
                                    {{ $customer->orders_max_created_at ? \Carbon\Carbon::parse($customer->orders_max_created_at)->format('j M Y') : '—' }}
                                </td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="icon-action" href="{{ route('admin.customers.show', $customer) }}" aria-label="View {{ $customer->name }}">
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
        {{ $customers->links() }}
    </div>
@endsection
