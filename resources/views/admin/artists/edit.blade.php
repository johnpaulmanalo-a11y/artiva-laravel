@extends('layouts.admin')

@section('title', 'Edit '.$artist->name.' — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Edit artist')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>{{ $artist->name }}</h2>
            <p>Update profile details and the artist portrait.</p>
        </div>
        <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.artists.index') }}"><i class="bi bi-arrow-left"></i> Back to artists</a>
    </div>

    <div class="admin-card">
        <form class="admin-form" method="POST" action="{{ route('admin.artists.update', $artist) }}" enctype="multipart/form-data" data-loading>
            @csrf
            @method('PUT')
            @include('admin.artists.partials.form')
        </form>
    </div>
@endsection
