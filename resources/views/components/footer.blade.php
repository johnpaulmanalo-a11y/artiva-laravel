<footer class="site-footer">
    <div class="container">
        <div class="site-footer__grid">
            <div class="site-footer__brand">
                <a class="brand brand--light" href="{{ route('home') }}">
                    <span class="brand__mark">A</span>
                    <span class="brand__word">Artiva</span>
                </a>
                <p class="site-footer__tagline">Art that speaks to you. A curated marketplace for original digital artwork, built for collectors and independent artists.</p>
                <p class="site-footer__note">
                    <i class="bi bi-shield-check"></i> Secure, instant digital delivery
                </p>
            </div>

            <div class="site-footer__col">
                <h3>Discover</h3>
                <ul>
                    <li><a href="{{ route('artworks.index') }}">Explore artwork</a></li>
                    @foreach ($navCategories->take(4) as $footerCategory)
                        <li><a href="{{ route('categories.show', $footerCategory) }}">{{ $footerCategory->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="site-footer__col">
                <h3>Artiva</h3>
                <ul>
                    <li><a href="{{ route('about') }}">About us</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('login') }}">Sign in</a></li>
                    <li><a href="{{ route('register') }}">Create account</a></li>
                </ul>
            </div>

            <div class="site-footer__col">
                <h3>Your account</h3>
                <ul>
                    <li><a href="{{ route('orders.index') }}">Order history</a></li>
                    <li><a href="{{ route('library.index') }}">Purchased library</a></li>
                    <li><a href="{{ route('wishlist.index') }}">Wishlist</a></li>
                    <li><a href="{{ route('profile.index') }}">Profile settings</a></li>
                </ul>
            </div>
        </div>

        <div class="site-footer__bottom">
            <span>&copy; {{ now()->year }} Artiva. A digital artwork marketplace.</span>
            <span class="site-footer__licence">All artwork remains the property of its artist and is sold under a personal-use licence.</span>
        </div>
    </div>
</footer>
