<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<script>document.documentElement.classList.replace('no-js', 'js');</script>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sign in — Artiva')</title>
    <meta name="description" content="@yield('meta_description', 'Access your Artiva account to buy artwork, manage your wishlist and download your collection.')">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700&family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @stack('styles')
</head>
<body class="guest-body">
    <div class="guest-shell">
        <aside class="guest-art" aria-hidden="true">
            <div class="guest-art__inner">
                <a class="brand brand--light" href="{{ route('home') }}">
                    <span class="brand__mark">A</span>
                    <span class="brand__word">Artiva</span>
                </a>

                <div class="guest-art__copy">
                    <p class="eyebrow eyebrow--light">Art that speaks to you</p>
                    <h1 class="guest-art__headline">Collect work that feels like it was made for you.</h1>
                    <p class="guest-art__text">Original digital artwork from independent artists, delivered instantly to your library.</p>
                </div>

                <ul class="guest-art__points">
                    <li><i class="bi bi-patch-check"></i> Personal-use licence with every purchase</li>
                    <li><i class="bi bi-cloud-arrow-down"></i> Instant, secure downloads</li>
                    <li><i class="bi bi-heart"></i> Save favourites to your wishlist</li>
                </ul>

                <p class="guest-art__quote">“<span>Every piece in Artiva is chosen for the way it makes a room — or a screen — feel.</span>”</p>
            </div>
        </aside>

        <main class="guest-panel">
            <div class="guest-panel__top">
                <a class="guest-back" href="{{ route('home') }}">
                    <i class="bi bi-arrow-left"></i> Back to gallery
                </a>
            </div>

            <div class="guest-card">
                <div class="guest-card__head">
                    <a class="brand brand--compact d-lg-none" href="{{ route('home') }}">
                        <span class="brand__mark">A</span>
                        <span class="brand__word">Artiva</span>
                    </a>
                    <h2 class="guest-card__title">@yield('heading', 'Welcome back')</h2>
                    <p class="guest-card__subtitle">@yield('subheading', 'Sign in to keep collecting.')</p>
                </div>

                @include('components.alert', ['compact' => true])

                @yield('content')

                <div class="guest-card__foot">
                    @yield('foot')
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
