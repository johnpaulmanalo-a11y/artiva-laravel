@extends('layouts.app')

@section('title', $artwork->title.' by '.$artwork->artist_name.' — Artiva')
@section('meta_description', \Illuminate\Support\Str::limit($artwork->description, 155))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/artwork-details.css') }}">
@endpush

@section('content')
    <section class="artwork-page">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="breadcrumb__sep">/</span>
                <a href="{{ route('artworks.index') }}">Explore</a>
                <span class="breadcrumb__sep">/</span>
                <a href="{{ route('categories.show', $artwork->category) }}">{{ $artwork->category->name }}</a>
                <span class="breadcrumb__sep">/</span>
                <span>{{ $artwork->title }}</span>
            </nav>

            <div class="artwork-detail">
                <div class="artwork-visual">
                    <div class="artwork-visual__frame">
                        @if ($artwork->previewUrl())
                            <img src="{{ $artwork->previewUrl() }}" alt="{{ $artwork->title }} by {{ $artwork->artist_name }}">
                        @else
                            <div class="artwork-visual__placeholder">
                                <span>{{ mb_substr($artwork->title, 0, 1) }}</span>
                                <span>Preview coming soon</span>
                            </div>
                        @endif
                    </div>

                    <p class="artwork-visual__note">
                        <i class="bi bi-eye"></i>
                        Preview is watermarked for display. The purchased file is the full-resolution master.
                    </p>
                </div>

                <div class="artwork-panel">
                    <div class="artwork-panel__kicker">
                        <a class="pill pill--sky" href="{{ route('categories.show', $artwork->category) }}">{{ $artwork->category->name }}</a>
                        @if ($artwork->is_featured)
                            <span class="pill pill--muted"><i class="bi bi-star"></i> Featured</span>
                        @endif
                        @if (! $artwork->is_active)
                            <span class="pill pill--warning">Hidden from store</span>
                        @endif
                    </div>

                    <div>
                        <h1 class="artwork-panel__title">{{ $artwork->title }}</h1>
                        <div class="artwork-panel__artist">
                            <span class="avatar avatar--sm avatar--sky">{{ mb_substr($artwork->artist_name, 0, 1) }}</span>
                            <span>By <strong>{{ $artwork->artist_name }}</strong></span>
                        </div>
                    </div>

                    <div class="artwork-panel__price">
                        <strong>{{ money($artwork->price) }}</strong>
                        <span>One-time purchase &middot; personal licence</span>
                    </div>

                    <p class="artwork-panel__description">{{ $artwork->description }}</p>

                    <dl class="artwork-specs">
                        <div class="artwork-specs__item">
                            <span class="artwork-specs__label">File format</span>
                            <span class="artwork-specs__value">{{ $artwork->file_format }}</span>
                        </div>
                        <div class="artwork-specs__item">
                            <span class="artwork-specs__label">Resolution</span>
                            <span class="artwork-specs__value">{{ $artwork->resolution ?: 'High resolution' }}</span>
                        </div>
                        <div class="artwork-specs__item">
                            <span class="artwork-specs__label">File size</span>
                            <span class="artwork-specs__value">{{ $artwork->fileSizeForHumans() }}</span>
                        </div>
                        <div class="artwork-specs__item">
                            <span class="artwork-specs__label">Downloads</span>
                            <span class="artwork-specs__value">{{ number_format($artwork->downloads_count) }}</span>
                        </div>
                    </dl>

                    @auth
                        @if ($purchased)
                            <div class="artwork-owned">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <strong>You own this artwork.</strong>
                                    <div>Download the master file from your library at any time.</div>
                                </div>
                            </div>

                            <div class="artwork-actions">
                                <a class="btn btn-ink btn-lg" href="{{ route('library.index') }}">
                                    <i class="bi bi-download"></i> Go to your library
                                </a>
                                <a class="btn btn-outline-ink btn-lg" href="{{ route('orders.index') }}">
                                    View orders
                                </a>
                            </div>
                        @else
                            <div class="artwork-actions">
                                @if ($inCart)
                                    <a class="btn btn-ink btn-lg" href="{{ route('cart.index') }}">
                                        <i class="bi bi-bag-check"></i> In your cart — review
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('cart.store', $artwork) }}">
                                        @csrf
                                        <button class="btn btn-ink btn-lg" type="submit">
                                            <i class="bi bi-bag-plus"></i> Add to cart — {{ money($artwork->price) }}
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('wishlist.store', $artwork) }}">
                                    @csrf
                                    <button class="btn btn-outline-ink btn-lg" type="submit">
                                        <i class="bi {{ $inWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                        {{ $inWishlist ? 'Saved to wishlist' : 'Save for later' }}
                                    </button>
                                </form>
                            </div>

                            <a class="summary-card__secondary" href="{{ route('checkout.index') }}" style="text-align: left; margin-top: -6px;">
                                <i class="bi bi-lightning-charge"></i>
                                Already have items in your cart? <span style="text-decoration: underline;">Go to checkout</span>
                            </a>
                        @endif
                    @else
                        <div class="artwork-actions">
                            <a class="btn btn-ink btn-lg" href="{{ route('login') }}">
                                <i class="bi bi-bag-plus"></i> Sign in to buy — {{ money($artwork->price) }}
                            </a>
                            <a class="btn btn-outline-ink btn-lg" href="{{ route('register') }}">
                                Create account
                            </a>
                        </div>

                        <div class="artwork-license">
                            <i class="bi bi-info-circle"></i>
                            <div>
                                <strong>An account is needed to purchase</strong>
                                Your downloads stay tied to your account, so you can re-download the file whenever you need it.
                            </div>
                        </div>
                    @endauth

                    <div class="artwork-license">
                        <i class="bi bi-shield-check"></i>
                        <div>
                            <strong>Personal-use licence</strong>
                            Use this artwork for personal projects, prints for your own space and social posts with credit.
                            Reselling, redistributing or using it in merchandise is not permitted. The artist retains copyright.
                        </div>
                    </div>
                </div>
            </div>

            @if ($related->isNotEmpty())
                <section class="related-artworks">
                    <div class="section-head">
                        <div class="section-head__text">
                            <p class="eyebrow">Keep looking</p>
                            <h2 class="display-2">More from {{ $artwork->category->name }}</h2>
                        </div>
                        <a class="link-arrow" href="{{ route('categories.show', $artwork->category) }}">
                            View category <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <div class="related-grid" data-stagger>
                        @foreach ($related as $relatedArtwork)
                            <x-artwork-card :artwork="$relatedArtwork" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
