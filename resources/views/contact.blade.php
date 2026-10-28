@extends('layouts.app')

@section('title', 'Contact Artiva — Support, licensing and artist submissions')
@section('meta_description', 'Get in touch with the Artiva team about orders, downloads, licensing or artist submissions.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endpush

@section('content')
    <section class="contact-page">
        <div class="container">
            <div class="page-head">
                <div>
                    <p class="eyebrow">Contact</p>
                    <h1>We would love to hear from you.</h1>
                    <p>Questions about an order, a download that will not open, licensing for a project, or submitting your portfolio — send us a message and a real person will reply.</p>
                </div>
            </div>

            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="bi bi-envelope"></i></div>
                        <div>
                            <h3>Email</h3>
                            <a href="mailto:hello@artiva.test">hello@artiva.test</a>
                            <p class="text-muted-soft" style="font-size: 0.82rem; margin-top: 4px;">Replies within two business days.</p>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="bi bi-life-preserver"></i></div>
                        <div>
                            <h3>Order support</h3>
                            <a href="mailto:support@artiva.test">support@artiva.test</a>
                            <p class="text-muted-soft" style="font-size: 0.82rem; margin-top: 4px;">Include your order number (ART-…) so we can help faster.</p>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="bi bi-briefcase"></i></div>
                        <div>
                            <h3>Artist submissions</h3>
                            <a href="mailto:artists@artiva.test">artists@artiva.test</a>
                            <p class="text-muted-soft" style="font-size: 0.82rem; margin-top: 4px;">Send a portfolio link and a short note about your work.</p>
                        </div>
                    </div>

                    <div class="contact-map">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <strong>Artiva Studio</strong>
                            <span>12 Gallery Lane, Suite 3 · Open Monday to Friday, 9am – 5pm</span>
                        </div>
                    </div>
                </div>

                <div class="contact-form-card">
                    <h2>Send a message</h2>
                    <p>Fill in the form and we will route your message to the right person.</p>

                    <form method="POST" action="{{ route('contact.send') }}" class="stack" data-loading>
                        @csrf

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="name">Your name</label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required maxlength="120" autocomplete="name">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="email">Email address</label>
                                <input class="form-control" type="email" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required maxlength="150" autocomplete="email">
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="subject">Subject</label>
                            <input class="form-control" type="text" id="subject" name="subject" value="{{ old('subject') }}" required maxlength="150" placeholder="Order support, licensing, artist submission…">
                        </div>

                        <div>
                            <label class="form-label" for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required minlength="10" maxlength="2000" placeholder="Tell us what you need help with.">{{ old('message') }}</textarea>
                        </div>

                        <button class="btn btn-ink" type="submit">
                            Send message <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
