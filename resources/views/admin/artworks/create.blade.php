@extends('layouts.admin')

@section('title', 'Add artwork — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Add artwork')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>New artwork</h2>
            <p>Add a piece to the catalogue. You can upload the preview now and the master file later.</p>
        </div>
        <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.artworks.index') }}"><i class="bi bi-arrow-left"></i> Back to catalogue</a>
    </div>

    <div class="admin-card">
        <form class="admin-form" method="POST" action="{{ route('admin.artworks.store') }}" enctype="multipart/form-data" data-loading>
            @csrf
            @include('admin.artworks.partials.form')
        </form>
    </div>
@endsection
