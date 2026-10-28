@extends('layouts.guest')

@section('title', 'Sign in — Artiva')
@section('heading', 'Welcome back')
@section('subheading', 'Sign in to reach your cart, wishlist and purchased artwork.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('login.store') }}" data-loading>
        @csrf

        <div>
            <label class="form-label" for="email">Email address</label>
            <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@example.com">
        </div>

        <div>
            <div class="d-flex justify-content-between align-items-center gap-2">
                <label class="form-label" for="password">Password</label>
                <a class="auth-form__link" href="{{ route('password.request') }}">Forgot password?</a>
            </div>
            <div style="position: relative;">
                <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password" placeholder="Your password" style="padding-right: 46px;">
                <button class="icon-btn" type="button" data-toggle-password="password" aria-label="Show password" style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); width: 34px; height: 34px; color: var(--muted-soft);">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="auth-form__row">
            <label class="form-check d-flex align-items-center gap-2 mb-0">
                <input class="form-check-input mt-0" type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span class="form-check-label">Keep me signed in</span>
            </label>
        </div>

        <button class="btn btn-ink btn-block" type="submit">
            Sign in <i class="bi bi-arrow-right"></i>
        </button>

        <div class="auth-demo">
            <strong>Demo accounts</strong>
            Customer: <code>customer@artiva.test</code> / <code>password</code><br>
            Admin: <code>admin@artiva.test</code> / <code>password</code>
        </div>
    </form>
@endsection

@section('foot')
    New to Artiva?&nbsp;<a href="{{ route('register') }}">Create an account</a>
@endsection
