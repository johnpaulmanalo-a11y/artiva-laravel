@extends('layouts.app')

@section('title', 'My profile — Artiva')
@section('meta_description', 'Manage your Artiva account details, password and collection summary.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="profile-hero">
                <span class="avatar">
                    @if ($user->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        {{ $user->initials() }}
                    @endif
                </span>

                <div class="profile-hero__body">
                    <h1 class="profile-hero__name">{{ $user->name }}</h1>
                    <div class="profile-hero__meta">
                        <span><i class="bi bi-envelope"></i> {{ $user->email }}</span>
                        @if ($user->phone)
                            <span><i class="bi bi-telephone"></i> {{ $user->phone }}</span>
                        @endif
                        <span><i class="bi bi-calendar3"></i> Member since {{ $user->created_at->format('F Y') }}</span>
                    </div>
                </div>

                <div class="profile-hero__actions">
                    <a class="btn btn-outline-light btn-sm" href="{{ route('library.index') }}"><i class="bi bi-collection"></i> My library</a>
                    @if ($user->isAdmin())
                        <a class="btn btn-light-ink btn-sm" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Admin</a>
                    @endif
                </div>
            </div>

            <div class="profile-stats">
                <div class="profile-stat">
                    <strong>{{ $stats['orders'] }}</strong>
                    <span>Total orders</span>
                </div>
                <div class="profile-stat">
                    <strong>{{ $stats['paid_orders'] }}</strong>
                    <span>Paid orders</span>
                </div>
                <div class="profile-stat">
                    <strong>{{ $stats['library'] }}</strong>
                    <span>Artworks owned</span>
                </div>
                <div class="profile-stat">
                    <strong>{{ $stats['wishlist'] }}</strong>
                    <span>Wishlist items</span>
                </div>
            </div>

            <div class="profile-layout">
                <div>
                    <div class="profile-card">
                        <div class="profile-card__head">
                            <h2>Account details</h2>
                            <p>Update your name, email and contact number.</p>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" data-loading>
                            @csrf
                            @method('PATCH')

                            <div class="profile-avatar-row">
                                <span class="avatar">
                                    @if ($user->avatar)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}" alt="{{ $user->name }}">
                                    @else
                                        {{ $user->initials() }}
                                    @endif
                                </span>
                                <div style="flex: 1;">
                                    <label class="form-label" for="avatar">Profile photo</label>
                                    <input class="form-control" type="file" id="avatar" name="avatar" accept="image/png,image/jpeg,image/webp">
                                    <p class="form-text">JPG, PNG or WebP up to 2 MB.</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Full name</label>
                                    <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="120">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">Email address</label>
                                    <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="150">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="phone">Phone</label>
                                    <input class="form-control" type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="30">
                                </div>
                            </div>

                            <div class="form-actions">
                                <button class="btn btn-ink" type="submit">Save changes</button>
                                <span class="form-actions__spacer"></span>
                                <span class="text-muted-soft" style="font-size: 0.78rem;">Last updated {{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                        </form>
                    </div>

                    <div class="profile-card">
                        <div class="profile-card__head">
                            <h2>Change password</h2>
                            <p>Use at least 8 characters with upper and lower case letters and a number.</p>
                        </div>

                        <form method="POST" action="{{ route('profile.password') }}" data-loading>
                            @csrf
                            @method('PATCH')

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="current_password">Current password</label>
                                    <input class="form-control" type="password" id="current_password" name="current_password" required autocomplete="current-password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="new_password">New password</label>
                                    <input class="form-control" type="password" id="new_password" name="password" required autocomplete="new-password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="password_confirmation">Confirm new password</label>
                                    <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-actions">
                                <button class="btn btn-ink" type="submit">Update password</button>
                            </div>
                        </form>
                    </div>
                </div>

                <aside>
                    <div class="profile-card">
                        <div class="profile-card__head">
                            <h2>Quick links</h2>
                        </div>

                        <div class="profile-quick">
                            <a class="profile-quick__link" href="{{ route('orders.index') }}">
                                <span class="profile-quick__icon"><i class="bi bi-receipt"></i></span>
                                <span>
                                    <span class="profile-quick__label">Order history</span>
                                    <span class="profile-quick__hint">{{ $stats['orders'] }} {{ \Illuminate\Support\Str::plural('order', $stats['orders']) }} on record</span>
                                </span>
                                <i class="bi bi-chevron-right"></i>
                            </a>

                            <a class="profile-quick__link" href="{{ route('library.index') }}">
                                <span class="profile-quick__icon"><i class="bi bi-collection"></i></span>
                                <span>
                                    <span class="profile-quick__label">Purchased library</span>
                                    <span class="profile-quick__hint">{{ $stats['library'] }} {{ \Illuminate\Support\Str::plural('artwork', $stats['library']) }} owned</span>
                                </span>
                                <i class="bi bi-chevron-right"></i>
                            </a>

                            <a class="profile-quick__link" href="{{ route('wishlist.index') }}">
                                <span class="profile-quick__icon"><i class="bi bi-heart"></i></span>
                                <span>
                                    <span class="profile-quick__label">Wishlist</span>
                                    <span class="profile-quick__hint">{{ $stats['wishlist'] }} {{ \Illuminate\Support\Str::plural('piece', $stats['wishlist']) }} saved</span>
                                </span>
                                <i class="bi bi-chevron-right"></i>
                            </a>

                            <a class="profile-quick__link" href="{{ route('cart.index') }}">
                                <span class="profile-quick__icon"><i class="bi bi-bag"></i></span>
                                <span>
                                    <span class="profile-quick__label">Shopping cart</span>
                                    <span class="profile-quick__hint">Review items before checkout</span>
                                </span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    @if ($recentOrders->isNotEmpty())
                        <div class="profile-card">
                            <div class="profile-card__head">
                                <h2>Recent orders</h2>
                            </div>

                            @foreach ($recentOrders as $order)
                                <a class="profile-mini-order" href="{{ route('orders.show', $order) }}">
                                    <span>
                                        <span class="profile-mini-order__num">{{ $order->order_number }}</span><br>
                                        <span class="profile-mini-order__date">{{ $order->created_at->format('j M Y') }}</span>
                                    </span>
                                    <span class="d-flex align-items-center gap-2">
                                        <span class="pill {{ $order->payment_status === 'paid' ? 'pill--success' : 'pill--warning' }}">
                                            {{ money($order->total) }}
                                        </span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($purchased->isNotEmpty())
                        <div class="profile-card">
                            <div class="profile-card__head">
                                <h2>From your collection</h2>
                            </div>

                            <div class="profile-quick">
                                @foreach ($purchased as $artwork)
                                    <a class="profile-quick__link" href="{{ route('artworks.show', $artwork) }}">
                                        <span class="profile-quick__icon" style="overflow: hidden; padding: 0;">
                                            @if ($artwork->previewUrl())
                                                <img src="{{ $artwork->previewUrl() }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <i class="bi bi-image"></i>
                                            @endif
                                        </span>
                                        <span>
                                            <span class="profile-quick__label">{{ $artwork->title }}</span>
                                            <span class="profile-quick__hint">{{ $artwork->artist_name }}</span>
                                        </span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
