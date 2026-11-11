@extends('layouts.guest')

@section('title', 'Reset your password — Artiva')
@section('heading', 'Forgot your password?')
@section('subheading', 'We will email you a link to choose a new one.')

@section('content')
    <p class="mb-4">Enter the email address linked to your Artiva account.</p>

    <form class="auth-form" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <label class="form-label" for="email">Email address</label>
            <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@example.com">
        </div>

        <button class="btn btn-ink btn-block" type="submit">
            Email password reset link <i class="bi bi-arrow-right"></i>
        </button>
    </form>
@endsection

@section('foot')
    Remembered your password?&nbsp;<a href="{{ route('login') }}">Sign in</a>
@endsection
