@extends('layouts.app')

@section('title', 'Artiva — Art that Speaks to You')
@section('meta_description', 'Discover and collect original digital artwork from independent artists. Character art, portraits, fantasy, concept art and more — delivered instantly.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/home.js') }}" defer></script>
@endpush

@section('content')
    @php
        $heroMain = $featured->first();
        $heroSub = $featured->skip(1)->first();
    @endphp

    <section class="hero">
        <div class="container hero__inner">
            <div class="hero__copy">
                <p class="eyebrow">Curated digital artwork</p>
                <h1 class="hero__headline">Art that <em>speaks</em> to you.</h1>
                <p class="hero__text">
                    A considered marketplace for original digital work. Collect character art, portraits,
                    fantasy worlds and quiet abstractions from independent artists — delivered to your
                    library the moment you check out.
                </p>

                <div class="hero__actions">
                    <a class="btn btn-ink btn-lg" href="{{ route('artworks.index') }}">
                        Explore the gallery <i class="bi bi-arrow-right"></i>
                    </a>
                    <a class="btn btn-outline-ink btn-lg" href="{{ route('about') }}">
                        Our story
                    </a>
                </div>

                <dl class="hero__stats">
                    <div class="hero__stat">
                        <dt data-count-to="{{ $stats['artworks'] }}">{{ $stats['artworks'] }}</dt>
                        <dd>Artworks</dd>
                    </div>
                    <div class="hero__stat">
                        <dt data-count-to="{{ $stats['artists'] }}">{{ $stats['artists'] }}</dt>
                        <dd>Artists</dd>
                    </div>
                    <div class="hero__stat">
                        <dt data-count-to="{{ $stats['customers'] }}">{{ $stats['customers'] }}</dt>
                        <dd>Collectors</dd>
                    </div>
                </dl>
            </div>

            <div class="hero__visual">
                <span class="hero__swatch"></span>

                @if ($heroMain)
                    <figure class="hero__frame hero__frame--main">
                        @if ($heroMain->previewUrl())
                            <img src="{{ asset('images/kai.jpg') }}" alt="Kai artwork">
                        @endif
                    </figure>
                @endif

                @if ($heroSub)
                    <figure class="hero__frame hero__frame--sub">
                        @if ($heroSub->previewUrl())
                            <img src="{{ asset('images/jp.jpg') }}" alt="artist">
                        @endif
                    </figure>
                @endif

                @if ($heroMain)
                    <div class="hero__tag">
                        <span class="hero__tag-label">Featured piece</span>
                        <a class="hero__tag-title" href="{{ route('artworks.show', $heroMain) }}">{{ $heroMain->title }}</a>
                        <span class="hero__tag-price">${{ number_format((float) $heroMain->price, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="section--tight">
            <div class="container">
                <div class="cat-strip">
                    @foreach ($categories as $category)
                        <a class="cat-tile" href="{{ route('categories.show', $category) }}">
                            <span class="cat-tile__mark">{{ mb_substr($category->name, 0, 1) }}</span>
                            <span>
                                <span class="cat-tile__name">{{ $category->name }}</span>
                                <span class="cat-tile__count">{{ $category->artworks_count }} {{ \Illuminate\Support\Str::plural('piece', $category->artworks_count) }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($featured->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <div class="section-head__text">
                        <p class="eyebrow">Selected by Artiva</p>
                        <h2 class="display-2">Featured this season</h2>
                        <p>Pieces our curators keep coming back to — bold character work, quiet portraits and worlds worth getting lost in.</p>
                    </div>
                    <a class="link-arrow" href="{{ route('artworks.index') }}">View all artwork <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="art-grid" data-stagger>
                    @foreach ($featured as $artwork)
                        <x-artwork-card :artwork="$artwork" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section">
        <div class="container">
            <div class="value-band">
                <div class="value-band__head">
                    <p class="eyebrow eyebrow--light">Why Artiva</p>
                    <h2>A gallery built for collectors and the artists behind the work.</h2>
                    <p>Every purchase supports an independent artist directly, with a licence that is simple to understand.</p>
                </div>

                <div class="value-band__grid">
                    <div class="value-item">
                        <div class="value-item__icon"><i class="bi bi-patch-check"></i></div>
                        <h3>Curated catalogue</h3>
                        <p>Every piece is reviewed before it joins the gallery, so the collection stays intentional.</p>
                    </div>
                    <div class="value-item">
                        <div class="value-item__icon"><i class="bi bi-cloud-arrow-down"></i></div>
                        <h3>Instant delivery</h3>
                        <p>High-resolution files unlock in your library as soon as your order is confirmed.</p>
                    </div>
                    <div class="value-item">
                        <div class="value-item__icon"><i class="bi bi-shield-lock"></i></div>
                        <h3>Secure downloads</h3>
                        <p>Master files live in private storage and are only served to verified purchases.</p>
                    </div>
                    <div class="value-item">
                        <div class="value-item__icon"><i class="bi bi-people"></i></div>
                        <h3>Artist first</h3>
                        <p>Artists set their own prices and keep ownership of the work they publish.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($latest->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <div class="section-head__text">
                        <p class="eyebrow">New in the gallery</p>
                        <h2 class="display-2">Recently added</h2>
                        <p>The newest arrivals from the artists we work with.</p>
                    </div>
                    <a class="link-arrow" href="{{ route('artworks.index', ['sort' => 'newest']) }}">Browse new work <i class="bi bi-arrow-right"></i></a>
                </div>

                <div class="art-grid art-grid--four" data-stagger>
                    @foreach ($latest->take(8) as $artwork)
                        <x-artwork-card :artwork="$artwork" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section">
        <div class="container">
            <div class="cta-band">
                <div>
                    <p class="eyebrow eyebrow--light">Start collecting</p>
                    <h2>Find the piece your space has been waiting for.</h2>
                    <p>Create a free account to save favourites, check out securely and keep every download in one place.</p>
                </div>

                <div class="cta-band__actions">
                    @auth
                        <a class="btn btn-light-ink btn-lg" href="{{ route('artworks.index') }}">Explore artwork</a>
                    @else
                        <a class="btn btn-light-ink btn-lg" href="{{ route('register') }}">Create an account</a>
                        <a class="btn btn-outline-light btn-lg" href="{{ route('artworks.index') }}">Browse first</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>
@endsection
