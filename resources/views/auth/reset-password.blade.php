@extends('layouts.guest')

@section('title', 'Choose a new password — Artiva')
@section('heading', 'Choose a new password')
@section('subheading', 'Set a new password for your Artiva account. You will be signed in with it next time.')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('password.store') }}" data-loading>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="form-label" for="email">Email address</label>
            <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" maxlength="150" placeholder="you@example.com">
        </div>

        <div>
            <label class="form-label" for="password">New password</label>
            <div style="position: relative;">
                <input class="form-control" type="password" id="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters" style="padding-right: 46px;">
                <button class="icon-btn" type="button" data-toggle-password="password" aria-label="Show password" style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); width: 34px; height: 34px; color: var(--muted-soft);">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <p class="form-text">Use at least 8 characters with upper and lower case letters and a number.</p>
        </div>

        <div>
            <label class="form-label" for="password_confirmation">Confirm new password</label>
            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your new password">
        </div>

        <button class="btn btn-ink btn-block" type="submit">
            Reset password <i class="bi bi-check2"></i>
        </button>
    </form>
@endsection

@section('foot')
    <a href="{{ route('login') }}">Back to sign in</a>
@endsection
