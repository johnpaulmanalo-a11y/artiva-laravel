@extends('layouts.app')

@section('title', 'Artists — Artiva')
@section('meta_description', 'Meet Artiva artists and explore the characters and digital artwork they create.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
@endpush

@section('content')
    <section class="artist-directory">
        <div class="container">
            <div class="artist-directory__intro">
                <p class="eyebrow">The people behind the art</p>
                <h1 class="display-2">Meet our artists</h1>
                <p>Discover the independent artists behind Artiva’s characters and digital artwork.</p>
            </div>

            <form class="artist-directory__search" method="GET" action="{{ route('artists.index') }}">
                <label class="visually-hidden" for="artist-search">Search artists</label>
                <input id="artist-search" type="search" name="q" value="{{ $term }}" placeholder="Search artists">
                <button class="btn btn-ink btn-sm" type="submit">Search</button>
                @if ($term !== '')
                    <a class="btn btn-ghost btn-sm" href="{{ route('artists.index') }}">Clear</a>
                @endif
            </form>

            @if ($artists->isEmpty())
                <div class="artist-directory__empty">
                    <i class="bi bi-person-badge" aria-hidden="true"></i>
                    <h2>No artists found</h2>
                    <p>Try a different search or explore the artwork catalogue.</p>
                    <a class="btn btn-ink" href="{{ route('artworks.index') }}">Explore artwork</a>
                </div>
            @else
                <div class="artist-grid">
                    @foreach ($artists as $artist)
                        <a class="artist-card" href="{{ route('artists.show', $artist) }}">
                            <div class="artist-card__photo">
                                @if ($artist->photoUrl())
                                    <img src="{{ $artist->photoUrl() }}" alt="{{ $artist->name }}" loading="lazy">
                                @else
                                    <span>{{ mb_strtoupper(mb_substr($artist->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="artist-card__body">
                                <h2>{{ $artist->name }}</h2>
                                <p>{{ $artist->specialization ?: 'Artiva artist' }}</p>
                                <span>{{ $artist->artworks_count }} {{ \Illuminate\Support\Str::plural('artwork', $artist->artworks_count) }} <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $artists->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
