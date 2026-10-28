@extends('layouts.admin')

@section('title', $artwork->title.' — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Artwork details')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>{{ $artwork->title }}</h2>
            <p>Added {{ $artwork->created_at->format('j F Y') }} &middot; Last updated {{ $artwork->updated_at->diffForHumans() }}</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('artworks.show', $artwork) }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View on store</a>
            <a class="btn btn-ink btn-sm" href="{{ route('admin.artworks.edit', $artwork) }}"><i class="bi bi-pencil"></i> Edit</a>
        </div>
    </div>

    <div class="detail-grid">
        <div class="admin-card">
            <div class="detail-media">
                @if ($artwork->previewUrl())
                    <img src="{{ $artwork->previewUrl() }}" alt="{{ $artwork->title }}">
                @else
                    <span>{{ mb_substr($artwork->title, 0, 1) }}</span>
                @endif
            </div>

            <div class="metric-grid" style="grid-template-columns: 1fr 1fr; margin-top: 16px; margin-bottom: 0;">
                <div class="metric-card" style="padding: 14px;">
                    <span class="metric-card__label"><i class="bi bi-cash"></i> Price</span>
                    <strong class="metric-card__value" style="font-size: 1.3rem;">{{ money($artwork->price) }}</strong>
                </div>
                <div class="metric-card" style="padding: 14px;">
                    <span class="metric-card__label"><i class="bi bi-bag-check"></i> Sold</span>
                    <strong class="metric-card__value" style="font-size: 1.3rem;">{{ $artwork->sold_count }}</strong>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <div>
                    <h3>Details</h3>
                    <p>How this artwork appears in the storefront</p>
                </div>
                <div>
                    @if ($artwork->is_active)
                        <span class="pill pill--success">Active</span>
                    @else
                        <span class="pill pill--muted">Inactive</span>
                    @endif
                    @if ($artwork->is_featured)
                        <span class="pill pill--sky">Featured</span>
                    @endif
                </div>
            </div>

            <dl class="detail-list">
                <div class="detail-list__row">
                    <dt>Artist</dt>
                    <dd>{{ $artwork->artist_name }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Category</dt>
                    <dd>{{ $artwork->category->name }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Slug</dt>
                    <dd>{{ $artwork->slug }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Format</dt>
                    <dd>{{ $artwork->file_format }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Resolution</dt>
                    <dd>{{ $artwork->resolution ?: '—' }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>File size</dt>
                    <dd>{{ $artwork->fileSizeForHumans() }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Master file</dt>
                    <dd>{{ $artwork->hasDownloadableFile() ? basename($artwork->file_path) : 'Not uploaded' }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Downloads</dt>
                    <dd>{{ number_format($artwork->downloads_count) }}</dd>
                </div>
                <div class="detail-list__row">
                    <dt>Lifetime revenue</dt>
                    <dd>{{ money($revenue) }}</dd>
                </div>
            </dl>

            <div style="margin-top: 18px; padding-top: 18px; border-top: 1px solid var(--line);">
                <span class="mono-label">Description</span>
                <p class="text-muted-soft" style="font-size: 0.9rem; line-height: 1.75; margin-top: 8px;">{{ $artwork->description }}</p>
            </div>
        </div>
    </div>

    <div class="admin-card" style="margin-top: 20px;">
        <div class="admin-card__head">
            <div>
                <h3>Order activity</h3>
                <p>Most recent purchases containing this artwork</p>
            </div>
            <a class="admin-card__link" href="{{ route('admin.orders.index') }}">All orders</a>
        </div>

        @if ($recentOrders->isEmpty())
            <div class="admin-empty">
                <i class="bi bi-receipt"></i>
                This artwork has not been purchased yet.
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $item)
                            <tr>
                                <td>
                                    <span class="admin-table__title">{{ $item->order->order_number }}</span>
                                    <span class="admin-table__sub">{{ ucfirst($item->order->status) }}</span>
                                </td>
                                <td>{{ $item->order->user->name }}</td>
                                <td class="admin-table__num">{{ money($item->price) }}</td>
                                <td>{{ $item->order->created_at->format('j M Y') }}</td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="icon-action" href="{{ route('admin.orders.show', $item->order) }}" aria-label="View order">
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
@endsection
