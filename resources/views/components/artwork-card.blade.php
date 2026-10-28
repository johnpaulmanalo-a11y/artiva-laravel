@props([
    'artwork',
    'owned' => false,
    'wishlisted' => false,
    'showWishlist' => true,
    'delay' => 0,
])

@php
    $preview = $artwork->previewUrl();
    $initial = mb_strtoupper(mb_substr($artwork->title, 0, 1));
@endphp

<article class="art-card" data-reveal @if ($delay) style="--reveal-delay: {{ $delay }}ms" @endif>
    <div class="art-card__media">
        <a href="{{ route('artworks.show', $artwork) }}" class="art-card__media-link" aria-label="View {{ $artwork->title }}">
            @if ($preview)
                <img src="{{ $preview }}" alt="{{ $artwork->title }} by {{ $artwork->artist_name }}" loading="lazy" width="1000" height="1200">
            @else
                <span class="art-card__placeholder" aria-hidden="true">
                    <span class="art-card__placeholder-mark">{{ $initial }}</span>
                    <span class="art-card__placeholder-text">Preview coming soon</span>
                </span>
            @endif
        </a>

        <span class="art-card__badge">{{ $artwork->category->name }}</span>

        @if ($owned)
            <span class="art-card__owned"><i class="bi bi-check-circle-fill"></i> Owned</span>
        @endif

        @if ($showWishlist)
            @auth
                <form method="POST" action="{{ route('wishlist.store', $artwork) }}" class="art-card__wish">
                    @csrf
                    <button type="submit" class="art-card__wish-btn {{ $wishlisted ? 'is-active' : '' }}"
                            aria-label="{{ $wishlisted ? 'Remove from wishlist' : 'Save to wishlist' }}"
                            aria-pressed="{{ $wishlisted ? 'true' : 'false' }}">
                        <i class="bi {{ $wishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                    </button>
                </form>
            @else
                <div class="art-card__wish">
                    <a href="{{ route('login') }}" class="art-card__wish-btn" aria-label="Sign in to save artwork">
                        <i class="bi bi-heart"></i>
                    </a>
                </div>
            @endauth
        @endif
    </div>

    <div class="art-card__body">
        <div class="art-card__meta">
            <span class="art-card__artist">{{ $artwork->artist_name }}</span>
            <span class="art-card__price">{{ number_format((float) $artwork->price, 2) }}</span>
        </div>

        <h3 class="art-card__title">
            <a href="{{ route('artworks.show', $artwork) }}">{{ $artwork->title }}</a>
        </h3>

        <div class="art-card__foot">
            <span class="art-card__format">{{ $artwork->file_format }} &middot; {{ $artwork->resolution ?: 'High resolution' }}</span>

            @if ($owned)
                <a href="{{ route('library.index') }}" class="btn btn-ghost btn-sm">Download</a>
            @else
                @auth
                    <form method="POST" action="{{ route('cart.store', $artwork) }}">
                        @csrf
                        <button type="submit" class="btn btn-ink btn-sm" data-add-to-cart>
                            <i class="bi bi-bag-plus"></i> Add
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ink btn-sm">
                        <i class="bi bi-bag-plus"></i> Add
                    </a>
                @endauth
            @endif
        </div>
    </div>
</article>
