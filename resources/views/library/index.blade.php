@extends('layouts.app')

@section('title', 'My artwork library — Artiva')
@section('meta_description', 'Download every artwork you have purchased from Artiva.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/library.css') }}">
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="library-head">
                <div>
                    <p class="eyebrow">Your collection</p>
                    <h1>Purchased artwork library</h1>
                    <p>Every file you own, ready to download again whenever you need it. Licences are personal-use only.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-outline-ink btn-sm" href="{{ route('orders.index') }}"><i class="bi bi-receipt"></i> Order history</a>
                    <a class="btn btn-ink btn-sm" href="{{ route('artworks.index') }}"><i class="bi bi-plus-lg"></i> Add artwork</a>
                </div>
            </div>

            <div class="library-note">
                <i class="bi bi-shield-lock"></i>
                <span>
                    Downloads are verified against your purchase history. Master files live in private storage and
                    links only work while you are signed in to the account that bought them.
                </span>
            </div>

            @if ($artworks->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon"><i class="bi bi-collection"></i></div>
                    <h3>Your library is empty</h3>
                    <p>Once you buy an artwork it appears here with its download links, forever.</p>
                    <a class="btn btn-ink" href="{{ route('artworks.index') }}">Find your first piece</a>
                </div>
            @else
                <div class="library-grid" data-stagger>
                    @foreach ($artworks as $artwork)
                        <article class="library-card">
                            <div class="library-card__media">
                                <span class="library-card__badge">{{ $artwork->category->name }}</span>
                                @if ($artwork->previewUrl())
                                    <img src="{{ $artwork->previewUrl() }}" alt="{{ $artwork->title }}">
                                @else
                                    <span>{{ mb_substr($artwork->title, 0, 1) }}</span>
                                @endif
                            </div>

                            <div class="library-card__body">
                                <h2 class="library-card__title">{{ $artwork->title }}</h2>
                                <span class="library-card__meta">{{ $artwork->artist_name }} &middot; {{ $artwork->file_format }} &middot; {{ $artwork->fileSizeForHumans() }}</span>

                                <span class="library-card__file">
                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                    {{ $artwork->hasDownloadableFile() ? 'Master file ready' : 'Master file pending' }}
                                </span>

                                <div class="library-card__actions">
                                    <a class="btn btn-ink btn-sm" href="{{ route('library.download', $artwork) }}">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                    <a class="btn btn-ghost btn-sm" href="{{ route('artworks.show', $artwork) }}">
                                        Details
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="meta-bar" style="margin-top: 26px;">
                    <span><strong>{{ $artworks->count() }}</strong> {{ \Illuminate\Support\Str::plural('artwork', $artworks->count()) }} owned</span>
                    <span>Across <strong>{{ $orderCount }}</strong> paid {{ \Illuminate\Support\Str::plural('order', $orderCount) }}</span>
                </div>
            @endif
        </div>
    </section>
@endsection
