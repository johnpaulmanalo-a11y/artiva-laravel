@extends('layouts.admin')

@section('title', 'Add category — Artiva Admin')
@section('eyebrow', 'Catalogue')
@section('heading', 'Add category')

@section('content')
    <div class="admin-page-head">
        <div>
            <h2>New category</h2>
            <p>Group artwork so collectors can browse the gallery by theme or style.</p>
        </div>
        <a class="btn btn-outline-ink btn-sm" href="{{ route('admin.categories.index') }}"><i class="bi bi-arrow-left"></i> Back to categories</a>
    </div>

    <div class="admin-card">
        <form class="admin-form" method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" data-loading>
            @csrf
            @include('admin.categories.partials.form')
        </form>
    </div>
@endsection
