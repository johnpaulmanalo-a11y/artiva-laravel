@extends('layouts.app')

@section('title', $category->name.' — Artiva')
@section('meta_description', $category->description ?: 'Browse the '.$category->name.' collection on Artiva.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/artworks.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/artworks.js') }}" defer></script>
@endpush

@section('content')
    <section class="listing">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="breadcrumb__sep">/</span>
                <a href="{{ route('artworks.index') }}">Explore</a>
                <span class="breadcrumb__sep">/</span>
                <span>{{ $category->name }}</span>
            </nav>

            <div class="listing__head listing__head--category">
                <p class="eyebrow">Category</p>
                <h1>{{ $category->name }}</h1>
                <p>{{ $category->description ?: 'Original digital artwork from the '.$category->name.' collection.' }}</p>

                <div class="listing__head-meta">
                    <span class="pill">{{ $artworks->total() }} {{ \Illuminate\Support\Str::plural('artwork', $artworks->total()) }}</span>
                    <a class="link-arrow" href="{{ route('artworks.index') }}">All categories <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <div class="toolbar" style="margin-top: 26px;">
                <form method="GET" action="{{ route('categories.show', $category) }}" style="display: contents;">
                    <div class="toolbar__controls" style="margin-left: auto;">
                        <label class="toolbar__count" for="categorySort">Sort</label>
                        <select class="toolbar__select" id="categorySort" name="sort" data-auto-submit>
                            <option value="newest" @selected($sort === 'newest')>Newest first</option>
                            <option value="price_low" @selected($sort === 'price_low')>Price: low to high</option>
                            <option value="price_high" @selected($sort === 'price_high')>Price: high to low</option>
                            <option value="title" @selected($sort === 'title')>Title A–Z</option>
                        </select>
                    </div>
                </form>
            </div>

            @if ($artworks->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon"><i class="bi bi-images"></i></div>
                    <h3>Nothing here yet</h3>
                    <p>This collection is being prepared. In the meantime, browse the rest of the gallery.</p>
                    <a class="btn btn-ink" href="{{ route('artworks.index') }}">Explore all artwork</a>
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

            @if ($categories->count() > 1)
                <section class="section--tight">
                    <div class="section-head">
                        <div class="section-head__text">
                            <p class="eyebrow">Keep browsing</p>
                            <h2 class="display-2">Other collections</h2>
                        </div>
                    </div>

                    <div class="cat-strip">
                        @foreach ($categories->where('id', '!=', $category->id) as $otherCategory)
                            <a class="cat-tile" href="{{ route('categories.show', $otherCategory) }}">
                                <span class="cat-tile__mark">{{ mb_substr($otherCategory->name, 0, 1) }}</span>
                                <span>
                                    <span class="cat-tile__name">{{ $otherCategory->name }}</span>
                                    <span class="cat-tile__count">{{ $otherCategory->artworks_count }} {{ \Illuminate\Support\Str::plural('piece', $otherCategory->artworks_count) }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
