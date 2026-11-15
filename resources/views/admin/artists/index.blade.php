@extends('layouts.admin')

@section('title', 'Artists — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Artists')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>Artist management</h2>
            <p>Create artist profiles and connect them to catalogue artwork. Reassign an artist’s artwork before deleting their profile.</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.artworks.index') }}"><i class="bi bi-images"></i> Artworks</a>
            <a class="btn btn-ink btn-sm" href="{{ route('admin.artists.create') }}"><i class="bi bi-plus-lg"></i> Add artist</a>
        </div>
    </div>

    <div class="admin-card">
        <form class="admin-filters" method="GET" action="{{ route('admin.artists.index') }}">
            <div class="admin-filters__search">
                <i class="bi bi-search"></i>
                <input type="search" name="q" value="{{ $term }}" placeholder="Search artist or specialization" aria-label="Search artists">
            </div>
            <button class="btn btn-ghost btn-sm" type="submit">Search</button>
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.artists.index') }}">Reset</a>
        </form>

        @if ($artists->isEmpty())
            <div class="admin-empty">
                <i class="bi bi-person-badge"></i>
                No artists match this search.
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Artist</th>
                            <th>Specialization</th>
                            <th>Artworks</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($artists as $artist)
                            <tr>
                                <td>
                                    <div class="admin-table__product">
                                        <div class="artist-admin-thumb">
                                            @if ($artist->photoUrl())
                                                <img src="{{ $artist->photoUrl() }}" alt="">
                                            @else
                                                {{ mb_strtoupper(mb_substr($artist->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <span class="admin-table__title">{{ $artist->name }}</span>
                                            <span class="admin-table__sub">{{ $artist->slug }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $artist->specialization ?: '—' }}</td>
                                <td class="admin-table__num">{{ $artist->artworks_count }}</td>
                                <td>
                                    <div class="admin-table__actions">
                                        @if ($artist->artworks_count > 0)
                                            <a class="icon-action" href="{{ route('artists.show', $artist) }}" target="_blank" rel="noopener noreferrer" aria-label="View {{ $artist->name }} profile">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @endif
                                        <a class="icon-action" href="{{ route('admin.artists.edit', $artist) }}" aria-label="Edit {{ $artist->name }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.artists.destroy', $artist) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action icon-action--danger" type="submit"
                                                    data-confirm="Delete the &quot;{{ $artist->name }}&quot; profile?"
                                                    aria-label="Delete {{ $artist->name }}">
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
        {{ $artists->links() }}
    </div>
@endsection
