@extends('layouts.admin')

@section('title', 'Edit '.$artwork->title.' — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Edit artwork')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>{{ $artwork->title }}</h2>
            <p>Update pricing, imagery and visibility. Leave file fields empty to keep the current uploads.</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.artworks.show', $artwork) }}"><i class="bi bi-eye"></i> View</a>
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.artworks.index') }}"><i class="bi bi-arrow-left"></i> Catalogue</a>
        </div>
    </div>

    <div class="admin-card">
        <form class="admin-form" method="POST" action="{{ route('admin.artworks.update', $artwork) }}" enctype="multipart/form-data" data-loading>
            @csrf
            @method('PUT')
            @include('admin.artworks.partials.form')
        </form>
    </div>

    <div class="admin-card" style="margin-top: 20px;">
        <div class="admin-card__head">
            <div>
                <h3>Danger zone</h3>
                <p>Deleting is only possible when the artwork has no sales history.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.artworks.destroy', $artwork) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-ink btn-sm" type="submit" data-confirm="Delete &quot;{{ $artwork->title }}&quot; permanently?">
                <i class="bi bi-trash3"></i> Delete artwork
            </button>
        </form>
    </div>
@endsection
