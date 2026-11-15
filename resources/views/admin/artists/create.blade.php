@extends('layouts.admin')

@section('title', 'Add artist — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Add artist')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>New artist profile</h2>
            <p>Add the artist’s public information. You can assign their artwork from the artwork form.</p>
        </div>
        <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.artists.index') }}"><i class="bi bi-arrow-left"></i> Back to artists</a>
    </div>

    <div class="admin-card">
        <form class="admin-form" method="POST" action="{{ route('admin.artists.store') }}" enctype="multipart/form-data" data-loading>
            @csrf
            @include('admin.artists.partials.form')
        </form>
    </div>
@endsection
