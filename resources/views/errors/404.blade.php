@extends('layouts.app')

@section('title', 'Page not found — Artiva')

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="empty-state" style="padding-block: clamp(48px, 9vw, 96px);">
                <div class="empty-state__icon"><i class="bi bi-compass"></i></div>
                <p class="eyebrow" style="margin-bottom: 0;">Error 404</p>
                <h3 style="font-size: clamp(1.8rem, 3.4vw, 2.4rem);">This page is not in the gallery</h3>
                <p>The link may be out of date, or the artwork may have been removed from the catalogue.</p>

                <div class="d-flex gap-2 flex-wrap justify-content-center" style="margin-top: 10px;">
                    <a class="btn btn-ink" href="{{ route('home') }}">Back to home</a>
                    <a class="btn btn-outline-ink" href="{{ route('artworks.index') }}">Explore artwork</a>
                </div>
            </div>
        </div>
    </section>
@endsection
