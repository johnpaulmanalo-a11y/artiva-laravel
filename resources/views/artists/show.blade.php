@extends('layouts.app')

@section('title', $artist->name.' — Artiva Artist')
@section('meta_description', \Illuminate\Support\Str::limit($artist->bio ?: 'Explore artwork and characters created by '.$artist->name.' on Artiva.', 160))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
@endpush

@section('content')
    <section class="artist-profile">
        <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="breadcrumb__sep">/</span>
                <a href="{{ route('artists.index') }}">Artists</a>
                <span class="breadcrumb__sep">/</span>
                <span>{{ $artist->name }}</span>
            </nav>

            <header class="artist-profile__hero">
                <div class="artist-profile__photo">
                    @if ($artist->photoUrl())
                        <img src="{{ $artist->photoUrl() }}" alt="{{ $artist->name }}">
                    @else
                        <span>{{ mb_strtoupper(mb_substr($artist->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="artist-profile__intro">
                    <p class="eyebrow">Artiva artist</p>
                    <h1>{{ $artist->name }}</h1>
                    @if ($artist->specialization)
                        <p class="artist-profile__specialization">{{ $artist->specialization }}</p>
                    @endif
                    @if ($artist->bio)
                        <div class="artist-profile__bio">{!! nl2br(e($artist->bio)) !!}</div>
                    @else
                        <p class="artist-profile__bio">Explore the characters and artwork created by {{ $artist->name }}.</p>
                    @endif
                    <div class="artist-profile__links">
                        @foreach ([
                            'portfolio_url' => ['Portfolio', 'bi-link-45deg'],
                            'instagram_url' => ['Instagram', 'bi-instagram'],
                            'facebook_url' => ['Facebook', 'bi-facebook'],
                            'artstation_url' => ['ArtStation', 'bi-palette'],
                        ] as $field => [$label, $icon])
                            @if ($artist->$field)
                                <a href="{{ $artist->$field }}" target="_blank" rel="noopener noreferrer">
                                    <i class="bi {{ $icon }}" aria-hidden="true"></i> {{ $label }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </header>

            <section class="artist-portfolio" aria-labelledby="artist-portfolio-title">
                <div class="section-head">
                    <div class="section-head__text">
                        <p class="eyebrow">Selected work</p>
                        <h2 class="display-2" id="artist-portfolio-title">Artwork by {{ $artist->name }}</h2>
                    </div>
                    <span class="pill">{{ $artworks->total() }} {{ \Illuminate\Support\Str::plural('piece', $artworks->total()) }}</span>
                </div>

                <div class="artist-portfolio__grid">
                    @foreach ($artworks as $artwork)
                        <x-artwork-card :artwork="$artwork" />
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $artworks->links() }}
                </div>
            </section>
        </div>
    </section>
@endsection
