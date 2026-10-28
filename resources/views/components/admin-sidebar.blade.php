@php
    $adminNav = [
        ['label' => 'Overview', 'items' => [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'match' => 'admin.dashboard'],
        ]],
        ['label' => 'Catalogue', 'items' => [
            ['route' => 'admin.artworks.index', 'label' => 'Artworks', 'icon' => 'bi-images', 'match' => 'admin.artworks.*'],
            ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'bi-tags', 'match' => 'admin.categories.*'],
        ]],
        ['label' => 'Sales', 'items' => [
            ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => 'bi-receipt', 'match' => 'admin.orders.*'],
            ['route' => 'admin.reports.index', 'label' => 'Sales reports', 'icon' => 'bi-graph-up', 'match' => 'admin.reports.*'],
        ]],
        ['label' => 'People', 'items' => [
            ['route' => 'admin.customers.index', 'label' => 'Customers', 'icon' => 'bi-people', 'match' => 'admin.customers.*'],
        ]],
    ];
@endphp

<aside class="admin-sidebar" data-admin-sidebar aria-label="Admin navigation">
    <div class="admin-sidebar__head">
        <a class="brand brand--light" href="{{ route('admin.dashboard') }}">
            <span class="brand__mark">A</span>
            <span class="brand__word">Artiva</span>
        </a>
        <span class="admin-sidebar__tag">Admin</span>
    </div>

    <nav class="admin-nav">
        @foreach ($adminNav as $group)
            <p class="admin-nav__label">{{ $group['label'] }}</p>
            <ul class="admin-nav__list">
                @foreach ($group['items'] as $item)
                    <li>
                        <a class="admin-nav__link {{ request()->routeIs($item['match']) ? 'is-active' : '' }}"
                           href="{{ route($item['route']) }}">
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                            @if ($item['route'] === 'admin.orders.index')
                                <span class="admin-nav__count" data-pending-orders>{{ $navPendingOrders ?? '' }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </nav>

    <div class="admin-sidebar__foot">
        <a class="admin-sidebar__store" href="{{ route('home') }}" target="_blank" rel="noopener">
            <i class="bi bi-shop"></i>
            <span>Open storefront</span>
            <i class="bi bi-box-arrow-up-right ms-auto"></i>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="admin-sidebar__signout" type="submit">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign out</span>
            </button>
        </form>
    </div>
</aside>
