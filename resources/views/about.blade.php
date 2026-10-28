@extends('layouts.app')

@section('title', 'About Artiva — A marketplace for original digital art')
@section('meta_description', 'Artiva connects independent digital artists with collectors. Learn how the gallery is curated, how artists are paid and how delivery works.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endpush

@section('content')
    <section class="about-hero container">
        <div>
            <p class="eyebrow">Our story</p>
            <h1 class="display-1">A gallery for work that <em style="font-style: italic; color: var(--sky);">stays with you</em>.</h1>
            <p class="lede" style="margin-top: 18px; max-width: 520px;">
                Artiva began with a simple frustration: beautiful digital artwork was being scattered across
                social feeds, saved to camera rolls and lost. We wanted a proper gallery — one with curation,
                clear licensing and a fair deal for the artist.
            </p>
            <p class="lede" style="margin-top: 14px; max-width: 520px;">
                Today Artiva is a small, carefully maintained marketplace for character art, portraits,
                fantasy, concept art, abstract work and landscapes. Every piece is sold directly by its
                creator under a personal-use licence.
            </p>

            <div style="margin-top: 28px; display: flex; gap: 12px; flex-wrap: wrap;">
                <a class="btn btn-ink" href="{{ route('artworks.index') }}">Explore the gallery</a>
                <a class="btn btn-outline-ink" href="{{ route('contact') }}">Talk to us</a>
            </div>
        </div>

        <div class="about-hero__visual">
            @foreach ($categories->take(2) as $index => $category)
                <figure>
                    @php
                        $cover = $category->artworks()->where('is_active', true)->latest()->first();
                    @endphp
                    @if ($cover && $cover->previewUrl())
                        <img src="{{ $cover->previewUrl() }}" alt="Artwork from the {{ $category->name }} collection">
                    @endif
                </figure>
            @endforeach

            <figcaption>Curated in small batches</figcaption>
        </div>
    </section>

    <section class="section--tight">
        <div class="container">
            <div class="about-stats">
                <div class="about-stat">
                    <strong>{{ number_format($stats['artworks']) }}</strong>
                    <span>Artworks listed</span>
                </div>
                <div class="about-stat">
                    <strong>{{ number_format($stats['artists']) }}</strong>
                    <span>Independent artists</span>
                </div>
                <div class="about-stat">
                    <strong>{{ number_format($stats['customers']) }}</strong>
                    <span>Registered collectors</span>
                </div>
                <div class="about-stat">
                    <strong>{{ number_format($stats['orders']) }}</strong>
                    <span>Artwork deliveries</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container about-story">
            <div>
                <p class="eyebrow">What we believe</p>
                <h2 class="display-2">Good art deserves a good home — and a fair licence.</h2>
            </div>

            <div class="about-story__body">
                <p>
                    Digital artwork is not a lesser medium. It is drawn, painted, composed and refined with the
                    same care as anything on canvas — and it deserves a place where it can be discovered without
                    being flattened into a feed.
                </p>
                <p>
                    That is why Artiva keeps its catalogue small and deliberate. Each artwork is reviewed, priced
                    by its artist, and sold with a licence written in plain language. You buy the right to use the
                    piece personally; the artist keeps ownership and can keep selling the work.
                </p>
                <p>
                    When you check out, the master file is moved to your private library. Downloads are verified
                    against your purchase history, which means the file is never exposed to anyone who has not paid
                    for it.
                </p>

                <blockquote>
                    “We would rather show you fifteen pieces we believe in than fifteen thousand we do not.”
                </blockquote>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <div class="section-head__text">
                    <p class="eyebrow">How it works</p>
                    <h2 class="display-2">Three steps from discovery to download.</h2>
                </div>
            </div>

            <div class="about-values">
                <article class="about-value">
                    <div class="about-value__icon"><i class="bi bi-search"></i></div>
                    <h3>1. Find your piece</h3>
                    <p>Browse by category, search by artist or filter by price. Save anything that catches your eye to your wishlist.</p>
                </article>
                <article class="about-value">
                    <div class="about-value__icon"><i class="bi bi-bag-check"></i></div>
                    <h3>2. Check out securely</h3>
                    <p>Pay with the simulated Artiva sandbox gateway used for this project, or connect a live gateway for production.</p>
                </article>
                <article class="about-value">
                    <div class="about-value__icon"><i class="bi bi-download"></i></div>
                    <h3>3. Download and enjoy</h3>
                    <p>The high-resolution master file appears in your library immediately, along with the licence details and your receipt.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="cta-band">
                <div>
                    <p class="eyebrow eyebrow--light">Artists</p>
                    <h2>Made something worth collecting?</h2>
                    <p>Artiva is curated, not open-posting. Send us a note with a link to your portfolio and we will take a look.</p>
                </div>
                <div class="cta-band__actions">
                    <a class="btn btn-light-ink btn-lg" href="{{ route('contact') }}">Submit your portfolio</a>
                </div>
            </div>
        </div>
    </section>
@endsection
