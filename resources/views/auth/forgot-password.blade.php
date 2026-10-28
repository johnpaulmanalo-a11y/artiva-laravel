@extends('layouts.guest')

@section('title', 'Forgot password — Artiva')
@section('heading', 'Reset your password')
@section('subheading', 'Enter the email address on your account and we will send a reset link.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('password.email') }}" data-loading>
        @csrf

        <div>
            <label class="form-label" for="email">Email address</label>
            <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" maxlength="150" placeholder="you@example.com">
        </div>

        <button class="btn btn-ink btn-block" type="submit">
            Email reset link <i class="bi bi-envelope-arrow-up"></i>
        </button>

        <div class="auth-hint">
            <i class="bi bi-info-circle"></i>
            <span>This demo project uses <code>MAIL_MAILER=log</code>, so the reset link is written to <code>storage/logs/laravel.log</code> instead of being emailed.</span>
        </div>
    </form>
@endsection

@section('foot')
    Remembered it? <a href="{{ route('login') }}">Back to sign in</a>
@endsection
