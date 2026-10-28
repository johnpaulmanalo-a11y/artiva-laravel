@extends('layouts.app')

@section('title', 'Explore Artwork — Artiva')
@section('meta_description', 'Browse original digital artwork by category, price and artist. Filter character art, portraits, fantasy, concept art, abstract work and landscapes.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/artworks.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/artworks.js') }}" defer></script>
@endpush

@section('content')
    @php
        $query = request()->query();
        $activeFilters = collect($filters)->except('sort')->filter(fn ($value) => ! is_null($value) && $value !== '');
        $selectedCategory = $filters['category'] ?? null;
    @endphp

    <section class="listing">
        <div class="container">
            <div class="listing__head">
                <p class="eyebrow eyebrow--light">The gallery</p>
                <h1>Explore artwork</h1>
                <p>Every piece is sold directly by its artist with a personal-use licence and instant delivery. Filter the collection to find the work that fits.</p>
            </div>

            <div class="listing__body">
                <aside>
                    <button class="btn btn-outline-ink btn-sm filters-toggle" type="button" data-filters-toggle aria-expanded="false" aria-controls="filtersPanel" style="margin-bottom: 14px;">
                        <i class="bi bi-sliders"></i> Filters
                    </button>

                    <form class="filters" id="filtersPanel" method="GET" action="{{ route('artworks.index') }}" data-filters-panel>
                        @if (($filters['q'] ?? null))
                            <input type="hidden" name="q" value="{{ $filters['q'] }}">
                        @endif
                        @if (($filters['sort'] ?? null))
                            <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                        @endif

                        <div class="filters__head">
                            <h2>Filter</h2>
                            <a class="filters__reset" href="{{ route('artworks.index') }}">Reset</a>
                        </div>

                        <div class="filters__group">
                            <span class="filters__legend">Category</span>

                            <label class="filters__option">
                                <input type="radio" name="category" value="" @checked(! $selectedCategory)>
                                All categories
                                <span class="filters__option-count">{{ $categories->sum('artworks_count') }}</span>
                            </label>

                            @foreach ($categories as $category)
                                <label class="filters__option">
                                    <input type="radio" name="category" value="{{ $category->slug }}" data-category-option @checked($selectedCategory === $category->slug)>
                                    {{ $category->name }}
                                    <span class="filters__option-count">{{ $category->artworks_count }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="filters__group">
                            <span class="filters__legend">Price range</span>
                            <div class="filters__price">
                                <input class="form-control form-control-sm" type="number" id="min_price" name="min_price" min="0" step="1" placeholder="Min" value="{{ $filters['min_price'] ?? '' }}" aria-label="Minimum price">
                                <span>&ndash;</span>
                                <input class="form-control form-control-sm" type="number" id="max_price" name="max_price" min="0" step="1" placeholder="Max" value="{{ $filters['max_price'] ?? '' }}" aria-label="Maximum price">
                            </div>
                        </div>

                        <button class="btn btn-ink btn-sm btn-block filters__apply" type="submit">
                            Apply filters
                        </button>
                    </form>
                </aside>

                <div>
                    @if ($activeFilters->isNotEmpty())
                        <div class="filters__chips">
                            @foreach ($activeFilters as $key => $value)
                                <span class="filter-chip">
                                    @if ($key === 'q')
                                        Search: {{ $value }}
                                    @elseif ($key === 'category')
                                        {{ $categories->firstWhere('slug', $value)?->name ?? $value }}
                                    @elseif ($key === 'min_price')
                                        From {{ money($value) }}
                                    @elseif ($key === 'max_price')
                                        Up to {{ money($value) }}
                                    @endif

                                    <a href="{{ route('artworks.index', collect($query)->except($key, 'page')->all()) }}" aria-label="Remove filter">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="toolbar">
                        <form method="GET" action="{{ route('artworks.index') }}" style="display: contents;">
                            @if ($selectedCategory)
                                <input type="hidden" name="category" value="{{ $selectedCategory }}">
                            @endif
                            @if (($filters['min_price'] ?? null) !== null)
                                <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
                            @endif
                            @if (($filters['max_price'] ?? null) !== null)
                                <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
                            @endif

                            <div class="toolbar__search">
                                <i class="bi bi-search"></i>
                                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search title, artist or keyword" aria-label="Search artwork">
                            </div>

                            <div class="toolbar__controls">
                                <span class="toolbar__count"><strong>{{ $resultCount }}</strong> {{ \Illuminate\Support\Str::plural('piece', $resultCount) }} found</span>

                                <select class="toolbar__select" name="sort" data-auto-submit aria-label="Sort artwork">
                                    <option value="newest" @selected($sort === 'newest')>Newest first</option>
                                    <option value="oldest" @selected($sort === 'oldest')>Oldest first</option>
                                    <option value="price_low" @selected($sort === 'price_low')>Price: low to high</option>
                                    <option value="price_high" @selected($sort === 'price_high')>Price: high to low</option>
                                    <option value="title" @selected($sort === 'title')>Title A–Z</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    @if ($artworks->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state__icon"><i class="bi bi-search"></i></div>
                            <h3>No artwork matches those filters</h3>
                            <p>Try widening the price range or clearing a category. New work is added regularly.</p>
                            <a class="btn btn-ink" href="{{ route('artworks.index') }}">Clear all filters</a>
                        </div>
                    @else
                        <div class="listing__grid" data-stagger>
                            @foreach ($artworks as $artwork)
                                <x-artwork-card :artwork="$artwork" />
                            @endforeach
                        </div>

                        <div class="pagination-wrap">
                            {{ $artworks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
