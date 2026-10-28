<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<script>document.documentElement.classList.replace('no-js', 'js');</script>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin — Artiva')</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700&family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-shell">
        @include('components.admin-sidebar')

        <div class="admin-main">
            <header class="admin-topbar">
                <button class="admin-burger" type="button" data-admin-nav-toggle aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>

                <div class="admin-topbar__title">
                    <p class="admin-topbar__eyebrow">@yield('eyebrow', 'Artiva admin')</p>
                    <h1>@yield('heading', 'Dashboard')</h1>
                </div>

                <div class="admin-topbar__actions">
                    <a class="admin-topbar__link" href="{{ route('home') }}" target="_blank" rel="noopener">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>View store</span>
                    </a>
                    <div class="admin-user">
                        <span class="avatar avatar--sm">{{ auth()->user()->initials() }}</span>
                        <span class="admin-user__meta">
                            <strong>{{ auth()->user()->name }}</strong>
                            <small>Administrator</small>
                        </span>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                @include('components.alert')
                @yield('content')
            </div>

            <footer class="admin-footer">
                <span>&copy; {{ now()->year }} Artiva. All rights reserved.</span>
                <span>Signed in as {{ auth()->user()->email }}</span>
            </footer>
        </div>
    </div>

    <div class="admin-scrim" data-admin-nav-scrim></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/admin.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
