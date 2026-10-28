@extends('layouts.app')

@section('title', 'Access denied — Artiva')

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="empty-state" style="padding-block: clamp(48px, 9vw, 96px);">
                <div class="empty-state__icon"><i class="bi bi-shield-lock"></i></div>
                <p class="eyebrow" style="margin-bottom: 0;">Error 403</p>
                <h3 style="font-size: clamp(1.8rem, 3.4vw, 2.4rem);">You cannot open this page</h3>
                <p>{{ $exception->getMessage() ?: 'This area is restricted. If you think you should have access, contact the Artiva team.' }}</p>

                <div class="d-flex gap-2 flex-wrap justify-content-center" style="margin-top: 10px;">
                    <a class="btn btn-ink" href="{{ route('home') }}">Back to home</a>
                    <a class="btn btn-outline-ink" href="{{ route('library.index') }}">My library</a>
                </div>
            </div>
        </div>
    </section>
@endsection
