@extends('layouts.app')

@section('title', 'Shopping cart — Artiva')
@section('meta_description', 'Review the artwork in your Artiva cart before checkout.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/cart.js') }}" defer></script>
@endpush

@section('content')
    <section class="page-shell">
        <div class="container">
            <div class="page-head">
                <div>
                    <p class="eyebrow">Your selection</p>
                    <h1>Shopping cart</h1>
                    <p>{{ $cart->itemCount() }} {{ \Illuminate\Support\Str::plural('artwork', $cart->itemCount()) }} ready for checkout. Each purchase includes a personal-use licence and instant download.</p>
                </div>
                <a class="link-arrow" href="{{ route('artworks.index') }}"><i class="bi bi-arrow-left"></i> Continue browsing</a>
            </div>

            @if ($items->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon"><i class="bi bi-bag"></i></div>
                    <h3>Your cart is empty</h3>
                    <p>Nothing here yet. Explore the gallery and add the pieces you want to take home.</p>
                    <a class="btn btn-ink" href="{{ route('artworks.index') }}">Explore artwork</a>
                </div>
            @else
                <div class="cart-layout">
                    <div>
                        <div class="cart-list">
                            <div class="cart-list__head">
                                <span>Artwork</span>
                                <span>Price</span>
                                <span class="text-end">Remove</span>
                            </div>

                            @foreach ($items as $item)
                                @php
                                    $artwork = $item->artwork;
                                @endphp

                                <div class="cart-item">
                                    <div class="cart-item__main">
                                        <div class="cart-item__thumb">
                                            @if ($artwork->previewUrl())
                                                <img src="{{ $artwork->previewUrl() }}" alt="{{ $artwork->title }}">
                                            @else
                                                <span>{{ mb_substr($artwork->title, 0, 1) }}</span>
                                            @endif
                                        </div>

                                        <div>
                                            <h2 class="cart-item__title">
                                                <a href="{{ route('artworks.show', $artwork) }}">{{ $artwork->title }}</a>
                                            </h2>
                                            <div class="cart-item__meta">
                                                <span>{{ $artwork->artist_name }}</span>
                                                <span>&middot;</span>
                                                <span>{{ $artwork->file_format }}</span>
                                            </div>
                                            <span class="cart-item__licence">
                                                <i class="bi bi-patch-check"></i> Personal-use licence
                                            </span>
                                        </div>
                                    </div>

                                    <div class="cart-item__price">
                                        {{ money($item->lineTotal()) }}
                                    </div>

                                    <form method="POST" action="{{ route('cart.destroy', $artwork) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="cart-item__remove" type="submit" data-cart-remove data-artwork-title="{{ $artwork->title }}" aria-label="Remove {{ $artwork->title }} from cart">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <div class="cart-actions">
                            <form method="POST" action="{{ route('cart.clear') }}">
                                @csrf
                                @method('DELETE')
                                <button class="cart-clear" type="submit" data-cart-clear>
                                    <i class="bi bi-trash3"></i> Clear cart
                                </button>
                            </form>

                            <a class="link-arrow" href="{{ route('artworks.index') }}">
                                <i class="bi bi-arrow-left"></i> Add more artwork
                            </a>
                        </div>
                    </div>

                    <aside class="summary-card">
                        <h2>Order summary</h2>

                        <div class="summary-items">
                            @foreach ($items as $item)
                                <div class="summary-item">
                                    <div class="summary-item__thumb">
                                        @if ($item->artwork->previewUrl())
                                            <img src="{{ $item->artwork->previewUrl() }}" alt="">
                                        @else
                                            {{ mb_substr($item->artwork->title, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="summary-item__body">
                                        <span class="summary-item__title">{{ $item->artwork->title }}</span>
                                        <span class="summary-item__meta">{{ $item->artwork->artist_name }}</span>
                                    </div>
                                    <span class="summary-item__price">{{ money($item->lineTotal()) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>{{ money($subtotal) }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Delivery</span>
                            <span>Instant download</span>
                        </div>

                        <div class="summary-row summary-row--total">
                            <span>Total</span>
                            <span>{{ money($subtotal) }}</span>
                        </div>

                        <a class="btn btn-ink btn-block" href="{{ route('checkout.index') }}">
                            Proceed to checkout <i class="bi bi-arrow-right"></i>
                        </a>

                        <span class="summary-card__secondary">
                            <i class="bi bi-lock"></i> Secure checkout
                        </span>

                        <div class="summary-note">
                            <i class="bi bi-info-circle"></i>
                            <span>Files are delivered digitally. Nothing is shipped, and no physical item is included.</span>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection
