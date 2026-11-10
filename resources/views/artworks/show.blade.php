@extends('layouts.app')

@section('title', $artwork->title.' — Artiva')
@section('meta_description', \Illuminate\Support\Str::limit($artwork->description ?: 'Discover '.$artwork->title.' by '.$artwork->artist_name.' on Artiva.', 160))

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
                            <div class="artwork-visual__placeholder" role="img" aria-label="Preview coming soon for {{ $artwork->title }}">
                                <span aria-hidden="true">{{ mb_strtoupper(mb_substr($artwork->title, 0, 1)) }}</span>
                                <span>Preview coming soon</span>
                            </div>
                        @endif
                    </div>
                    <p class="artwork-visual__note"><i class="bi bi-shield-check"></i> Secure checkout &amp; instant digital delivery</p>
                </div>

                <div class="artwork-panel">
                    <div class="artwork-panel__kicker">
                        <a class="pill" href="{{ route('categories.show', $artwork->category) }}">{{ $artwork->category->name }}</a>
                        @if ($artwork->is_featured)
                            <span class="pill pill--sky">Featured</span>
                        @endif
                    </div>

                    <div>
                        <h1 class="artwork-panel__title">{{ $artwork->title }}</h1>
                        <p class="artwork-panel__artist">Created by <strong>{{ $artwork->artist_name }}</strong></p>
                    </div>

                    <div class="artwork-panel__price">
                        <strong>{{ money($artwork->price) }}</strong>
                        <span>One-time purchase</span>
                    </div>

                    @if ($artwork->description)
                        <p class="artwork-panel__description">{{ $artwork->description }}</p>
                    @endif

                    <dl class="artwork-specs">
                        <div class="artwork-specs__item">
                            <dt class="artwork-specs__label">File format</dt>
                            <dd class="artwork-specs__value">{{ $artwork->file_format ?: 'Digital file' }}</dd>
                        </div>
                        <div class="artwork-specs__item">
                            <dt class="artwork-specs__label">Resolution</dt>
                            <dd class="artwork-specs__value">{{ $artwork->resolution ?: 'High resolution' }}</dd>
                        </div>
                    </dl>

                    <div class="artwork-actions">
                        @if ($purchased)
                            <div class="artwork-owned">
                                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                <span>You own this artwork. It is ready in your library.</span>
                                <a class="btn btn-ink btn-sm" href="{{ route('library.download', $artwork) }}"><i class="bi bi-download"></i> Download</a>
                            </div>
                        @elseif ($inCart)
                            <a class="btn btn-ink" href="{{ route('cart.index') }}"><i class="bi bi-bag-check"></i> View in cart</a>
                        @else
                            @auth
                                <form method="POST" action="{{ route('cart.store', $artwork) }}">
                                    @csrf
                                    <button class="btn btn-ink" type="submit"><i class="bi bi-bag-plus"></i> Add to cart</button>
                                </form>
                            @else
                                <a class="btn btn-ink" href="{{ route('login') }}"><i class="bi bi-bag-plus"></i> Sign in to purchase</a>
                            @endauth
                        @endif

                        @auth
                            <form method="POST" action="{{ route('wishlist.store', $artwork) }}">
                                @csrf
                                <button class="artwork-wish {{ $inWishlist ? 'is-active' : '' }}" type="submit"
                                        aria-label="{{ $inWishlist ? 'Remove from wishlist' : 'Save to wishlist' }}"
                                        aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
                                    <i class="bi {{ $inWishlist ? 'bi-heart-fill' : 'bi-heart' }}" aria-hidden="true"></i>
                                </button>
                            </form>
                        @else
                            <a class="artwork-wish" href="{{ route('login') }}" aria-label="Sign in to save artwork">
                                <i class="bi bi-heart" aria-hidden="true"></i>
                            </a>
                        @endauth
                    </div>

                    @if (! $purchased)
                        <div class="artwork-license">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            <div>
                                <strong>Personal-use licence</strong>
                                <span>Your purchase includes a personal-use licence and instant access to the digital file. Redistribution and resale are not permitted.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <section class="artwork-reviews" aria-labelledby="reviews-title" style="margin-top: 64px;">
                <div class="section-head">
                    <div class="section-head__text">
                        <p class="eyebrow">Community feedback</p>
                        <h2 class="display-2" id="reviews-title">Reviews &amp; ratings</h2>
                    </div>
                    <div class="review-summary">
                        <strong>{{ $reviewCount ? number_format($averageRating, 1) : '—' }}</strong>
                        <span aria-label="{{ $averageRating }} out of 5 stars">
                            @for ($star = 1; $star <= 5; $star++)
                                <i class="bi {{ $star <= round($averageRating) ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                            @endfor
                        </span>
                        <small>{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</small>
                    </div>
                </div>

                @auth
                    @if ($purchased)
                        <form method="POST" action="{{ route('reviews.store', $artwork) }}" class="review-form">
                            @csrf
                            <h3>{{ $myReview ? 'Update your review' : 'Share your experience' }}</h3>
                            <label for="review-rating">Your rating</label>
                            <select id="review-rating" name="rating" required>
                                @foreach ([5 => '5 - Excellent', 4 => '4 - Very good', 3 => '3 - Good', 2 => '2 - Fair', 1 => '1 - Poor'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('rating', $myReview?->rating ?? 5) == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <label for="review-comment">Comment (optional)</label>
                            <textarea id="review-comment" name="comment" rows="3" maxlength="1000" placeholder="What did you think about this artwork?">{{ old('comment', $myReview?->comment) }}</textarea>
                            @error('rating') <p class="form-error">{{ $message }}</p> @enderror
                            @error('comment') <p class="form-error">{{ $message }}</p> @enderror
                            <button class="btn btn-ink" type="submit">{{ $myReview ? 'Update review' : 'Submit review' }}</button>
                        </form>
                    @else
                        <p class="review-note">Only customers who have purchased this artwork can leave a review.</p>
                    @endif
                @else
                    <p class="review-note"><a href="{{ route('login') }}">Sign in</a> to see whether you can review this artwork.</p>
                @endauth

                <div class="review-list">
                    @forelse ($reviews as $review)
                        <article class="review-item">
                            <div class="review-item__head">
                                <strong>{{ $review->user->name }}</strong>
                                <span>
                                    @for ($star = 1; $star <= 5; $star++)
                                        <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                                    @endfor
                                </span>
                                <small>{{ $review->created_at->format('M d, Y') }}</small>
                            </div>
                            @if ($review->comment)
                                <p>{{ $review->comment }}</p>
                            @endif
                        </article>
                    @empty
                        <p class="review-note">No reviews yet. Be the first verified buyer to share your experience.</p>
                    @endforelse
                </div>
                {{ $reviews->links() }}
            </section>

            @if ($related->isNotEmpty())
                <section class="related-artworks" aria-labelledby="related-artworks-title">
                    <div class="section-head">
                        <div class="section-head__text">
                            <p class="eyebrow">Continue exploring</p>
                            <h2 class="display-2" id="related-artworks-title">More from {{ $artwork->category->name }}</h2>
                        </div>
                        <a class="link-arrow" href="{{ route('categories.show', $artwork->category) }}">View collection <i class="bi bi-arrow-right"></i></a>
                    </div>

                    <div class="related-grid">
                        @foreach ($related as $relatedArtwork)
                            <x-artwork-card :artwork="$relatedArtwork" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
