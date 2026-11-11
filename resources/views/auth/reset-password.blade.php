@extends('layouts.guest')

@section('title', 'Choose a new password — Artiva')
@section('heading', 'Choose a new password')
@section('subheading', 'Set a new password for your Artiva account.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label class="form-label" for="email">Email address</label>
            <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="email">
        </div>

        <div>
            <label class="form-label" for="password">New password</label>
            <input class="form-control" type="password" id="password" name="password" required autocomplete="new-password">
        </div>

        <div>
            <label class="form-label" for="password_confirmation">Confirm new password</label>
            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button class="btn btn-ink btn-block" type="submit">
            Reset password <i class="bi bi-arrow-right"></i>
        </button>
    </form>
@endsection

@section('foot')
    <a href="{{ route('login') }}">Return to sign in</a>
@endsection
