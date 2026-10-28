@extends('layouts.admin')

@section('title', 'Categories — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Categories')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>Category management</h2>
            <p>Categories organise the gallery. A category can only be deleted once it has no artwork.</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.artworks.index') }}"><i class="bi bi-images"></i> Artworks</a>
            <a class="btn btn-ink btn-sm" href="{{ route('admin.categories.create') }}"><i class="bi bi-plus-lg"></i> Add category</a>
        </div>
    </div>

    <div class="admin-card">
        <form class="admin-filters" method="GET" action="{{ route('admin.categories.index') }}">
            <div class="admin-filters__search">
                <i class="bi bi-search"></i>
                <input type="search" name="q" value="{{ $term }}" placeholder="Search categories">
            </div>
            <button class="btn btn-ghost btn-sm" type="submit">Search</button>
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.categories.index') }}">Reset</a>
        </form>

        @if ($categories->isEmpty())
            <div class="admin-empty">
                <i class="bi bi-tags"></i>
                No categories found.
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Slug</th>
                            <th>Artworks</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <span class="admin-table__title">{{ $category->name }}</span>
                                    <span class="admin-table__sub">{{ \Illuminate\Support\Str::limit($category->description, 60) ?: 'No description' }}</span>
                                </td>
                                <td><span class="mono-label">{{ $category->slug }}</span></td>
                                <td class="admin-table__num">{{ $category->artworks_count }}</td>
                                <td>
                                    @if ($category->is_active)
                                        <span class="pill pill--success">Active</span>
                                    @else
                                        <span class="pill pill--muted">Hidden</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-table__actions">
                                        <a class="icon-action" href="{{ route('categories.show', $category) }}" target="_blank" rel="noopener" aria-label="View category on store">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        <a class="icon-action" href="{{ route('admin.categories.edit', $category) }}" aria-label="Edit {{ $category->name }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action icon-action--danger" type="submit"
                                                    data-confirm="Delete the &quot;{{ $category->name }}&quot; category?"
                                                    aria-label="Delete {{ $category->name }}">
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
        {{ $categories->links() }}
    </div>
@endsection
