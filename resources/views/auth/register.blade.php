@extends('layouts.guest')

@section('title', 'Create account — Artiva')
@section('heading', 'Create your account')
@section('subheading', 'Save favourites, check out quickly and keep every download in one library.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('register.store') }}" data-loading>
        @csrf

        <div>
            <label class="form-label" for="name">Full name</label>
            <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" maxlength="120" placeholder="Your name">
        </div>

        <div>
            <label class="form-label" for="email">Email address</label>
            <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" maxlength="150" placeholder="you@example.com">
        </div>

        <div>
            <label class="form-label" for="phone">Phone <span class="text-muted-soft" style="text-transform: none; letter-spacing: 0;">(optional)</span></label>
            <input class="form-control" type="text" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel" maxlength="30" placeholder="+1 555 0100">
        </div>

        <div>
            <label class="form-label" for="password">Password</label>
            <div style="position: relative;">
                <input class="form-control" type="password" id="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters" style="padding-right: 46px;">
                <button class="icon-btn" type="button" data-toggle-password="password" aria-label="Show password" style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); width: 34px; height: 34px; color: var(--muted-soft);">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <p class="form-text">Use at least 8 characters with upper and lower case letters and a number.</p>
        </div>

        <div>
            <label class="form-label" for="password_confirmation">Confirm password</label>
            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password">
        </div>

        <button class="btn btn-ink btn-block" type="submit">
            Create account <i class="bi bi-arrow-right"></i>
        </button>

        <div class="auth-hint">
            <i class="bi bi-shield-check"></i>
            <span>Your account keeps your purchases and downloads private. We never share your details.</span>
        </div>
    </form>
@endsection

@section('foot')
    Already collecting?&nbsp;<a href="{{ route('login') }}">Sign in</a>
@endsection
