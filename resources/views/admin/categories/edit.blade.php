@extends('layouts.admin')

@section('title', 'Edit '.$category->name.' — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Edit category')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>{{ $category->name }}</h2>
            <p>{{ $category->artworks()->count() }} {{ \Illuminate\Support\Str::plural('artwork', $category->artworks()->count()) }} in this category.</p>
        </div>
        <div class="admin-page-head__actions">
            <a class="btn btn-outline-ink btn-sm" href="{{ route('categories.show', $category) }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View on store</a>
            <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.categories.index') }}"><i class="bi bi-arrow-left"></i> Categories</a>
        </div>
    </div>

    <div class="admin-card">
        <form class="admin-form" method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" data-loading>
            @csrf
            @method('PUT')
            @include('admin.categories.partials.form')
        </form>
    </div>

    <div class="admin-card" style="margin-top: 20px;">
        <div class="admin-card__head">
            <div>
                <h3>Danger zone</h3>
                <p>Categories with artwork cannot be deleted — move the artwork first.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-ink btn-sm" type="submit" data-confirm="Delete the &quot;{{ $category->name }}&quot; category?">
                <i class="bi bi-trash3"></i> Delete category
            </button>
        </form>
    </div>
@endsection
