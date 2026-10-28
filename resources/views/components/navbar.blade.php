<header class="site-header" data-site-header>
    <div class="container site-header__inner">
        <a class="brand" href="{{ url('/') }}" aria-label="Artiva home">
    <img
        src="{{ asset('images/logo.png') }}"
        alt="Artiva"
        class="brand__logo"
    >
</a>

        <nav class="site-nav" id="siteNav" aria-label="Main navigation">
            <a class="site-nav__link {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="site-nav__link {{ request()->routeIs('artworks.*') ? 'is-active' : '' }}" href="{{ route('artworks.index') }}">Explore</a>

            <div class="site-nav__dropdown">
                <button class="site-nav__link site-nav__link--button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Categories <i class="bi bi-chevron-down"></i>
                </button>
                <ul class="dropdown-menu site-dropdown">
                    @foreach ($navCategories as $navCategory)
                        <li>
                            <a class="dropdown-item" href="{{ route('categories.show', $navCategory) }}">
                                {{ $navCategory->name }}
                            </a>
                        </li>
                    @endforeach
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('artworks.index') }}">All artwork</a></li>
                </ul>
            </div>

            <a class="site-nav__link {{ request()->routeIs('about') ? 'is-active' : '' }}" href="{{ route('about') }}">About</a>
            <a class="site-nav__link {{ request()->routeIs('contact') ? 'is-active' : '' }}" href="{{ route('contact') }}">Contact</a>

            <div class="site-nav__mobile-actions">
                @auth
                    <a class="site-nav__link" href="{{ route('orders.index') }}">My orders</a>
                    <a class="site-nav__link" href="{{ route('library.index') }}">My library</a>
                    <a class="site-nav__link" href="{{ route('profile.index') }}">Profile</a>
                    @if (auth()->user()->isAdmin())
                        <a class="site-nav__link" href="{{ route('admin.dashboard') }}">Admin dashboard</a>
                    @endif
                @else
                    <a class="site-nav__link" href="{{ route('login') }}">Sign in</a>
                    <a class="site-nav__link" href="{{ route('register') }}">Create account</a>
                @endauth
            </div>
        </nav>

        <div class="site-actions">
            <a class="icon-btn" href="{{ route('artworks.index') }}" aria-label="Search artwork">
                <i class="bi bi-search"></i>
            </a>

            <a class="icon-btn" href="{{ route('wishlist.index') }}" aria-label="Wishlist">
                <i class="bi bi-heart"></i>
                @if ($navWishlistCount > 0)
                    <span class="icon-btn__badge">{{ $navWishlistCount }}</span>
                @endif
            </a>

            <a class="icon-btn" href="{{ route('cart.index') }}" aria-label="Shopping cart">
                <i class="bi bi-bag"></i>
                @if ($navCartCount > 0)
                    <span class="icon-btn__badge">{{ $navCartCount }}</span>
                @endif
            </a>

            @auth
                <div class="site-account dropdown">
                    <button class="site-account__trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar avatar--sm">{{ auth()->user()->initials() }}</span>
                        <span class="d-none d-xl-inline">{{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end site-dropdown">
                        <li><span class="dropdown-header">{{ auth()->user()->email }}</span></li>
                        <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person"></i> My profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-receipt"></i> Order history</a></li>
                        <li><a class="dropdown-item" href="{{ route('library.index') }}"><i class="bi bi-collection"></i> Purchased library</a></li>
                        @if (auth()->user()->isAdmin())
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Admin dashboard</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right"></i> Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a class="btn btn-ink btn-sm site-actions__cta" href="{{ route('login') }}">Sign in</a>
            @endauth

            <button class="icon-btn site-nav-toggle" type="button" data-nav-toggle aria-label="Open menu" aria-controls="siteNav" aria-expanded="false">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
</header>
