@extends('layouts.admin')

@section('title', 'Artwork list — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Artworks')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>Artwork catalogue</h2>
            <p>{{ $artworks->total() }} {{ \Illuminate\Support\Str::plural('artwork', $artworks->total()) }} in the catalogue. Preview images are public, master files are private.</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
            <a class="btn btn-ink btn-sm" href="{{ route('admin.artworks.create') }}"><i class="bi bi-plus-lg"></i> Add artwork</a>
        </div>
    </div>

    <div class="admin-card">
        <form class="admin-filters" method="GET" action="{{ route('admin.artworks.index') }}">
            <div class="admin-filters__search">
                <i class="bi bi-search"></i>
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search title, artist or description">
            </div>

            <select name="category" data-admin-auto-submit aria-label="Filter by category">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <select name="status" data-admin-auto-submit aria-label="Filter by status">
                <option value="">Any status</option>
                <option value="active" @selected(($filters['status'] ?? null) === 'active')>Active</option>
                <option value="inactive" @selected(($filters['status'] ?? null) === 'inactive')>Inactive</option>
                <option value="featured" @selected(($filters['status'] ?? null) === 'featured')>Featured</option>
            </select>

            <select name="sort" data-admin-auto-submit aria-label="Sort artworks">
                <option value="newest" @selected($sort === 'newest')>Newest first</option>
                <option value="oldest" @selected($sort === 'oldest')>Oldest first</option>
                <option value="price_low" @selected($sort === 'price_low')>Price: low to high</option>
                <option value="price_high" @selected($sort === 'price_high')>Price: high to low</option>
                <option value="title" @selected($sort === 'title')>Title A–Z</option>
            </select>

            <button class="btn btn-ghost btn-sm" type="submit">Search</button>
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.artworks.index') }}">Reset</a>
        </form>

        @if ($artworks->isEmpty())
            <div class="admin-empty">
                <i class="bi bi-images"></i>
                No artwork matches these filters.
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Artwork</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Sold</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($artworks as $artwork)
                            <tr>
                                <td>
                                    <div class="admin-table__product">
                                        <div class="admin-table__thumb">
                                            @if ($artwork->previewUrl())
                                                <img src="{{ $artwork->previewUrl() }}" alt="">
                                            @else
                                                {{ mb_substr($artwork->title, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <span class="admin-table__title">{{ $artwork->title }}</span>
                                            <span class="admin-table__sub">{{ $artwork->artist_name }} &middot; {{ $artwork->file_format }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $artwork->category->name }}</td>
                                <td class="admin-table__num">{{ money($artwork->price) }}</td>
                                <td class="admin-table__num">{{ $artwork->order_items_count }}</td>
                                <td>
                                    @if ($artwork->is_active)
                                        <span class="pill pill--success">Active</span>
                                    @else
                                        <span class="pill pill--muted">Inactive</span>
                                    @endif
                                    @if ($artwork->is_featured)
                                        <span class="pill pill--sky"><i class="bi bi-star"></i> Featured</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="icon-action" href="{{ route('admin.artworks.show', $artwork) }}" aria-label="View {{ $artwork->title }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a class="icon-action" href="{{ route('admin.artworks.edit', $artwork) }}" aria-label="Edit {{ $artwork->title }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.artworks.destroy', $artwork) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action icon-action--danger" type="submit"
                                                    data-confirm="Delete &quot;{{ $artwork->title }}&quot;? This cannot be undone."
                                                    aria-label="Delete {{ $artwork->title }}">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="pagination-wrap">
        {{ $artworks->links() }}
    </div>
@endsection
