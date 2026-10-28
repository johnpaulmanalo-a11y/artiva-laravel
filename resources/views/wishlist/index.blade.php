@extends('layouts.app')

@section('title', 'Wishlist — Artiva')
@section('meta_description', 'Artwork you have saved for later at Artiva.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wishlist.css') }}">
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="wishlist-head">
                <div>
                    <p class="eyebrow">Saved for later</p>
                    <h1>Your wishlist</h1>
                    <p>Keep the pieces you are thinking about. Add them to your cart whenever you are ready.</p>
                </div>
                <a class="link-arrow" href="{{ route('artworks.index') }}">Explore artwork <i class="bi bi-arrow-right"></i></a>
            </div>

            @if ($items->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon"><i class="bi bi-heart"></i></div>
                    <h3>Nothing saved yet</h3>
                    <p>Tap the heart on any artwork to keep it here while you decide.</p>
                    <a class="btn btn-ink" href="{{ route('artworks.index') }}">Find something you love</a>
                </div>
            @else
                <div class="meta-bar">
                    <span><strong>{{ $items->total() }}</strong> {{ \Illuminate\Support\Str::plural('artwork', $items->total()) }} saved</span>
                    <span><i class="bi bi-heart-fill" style="color: var(--danger);"></i> Remove with the heart button on each card</span>
                </div>

                <div class="wishlist-grid" data-stagger>
                    @foreach ($artworks as $artwork)
                        <x-artwork-card :artwork="$artwork" :wishlisted="true" />
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
