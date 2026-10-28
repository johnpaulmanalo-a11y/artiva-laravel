<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = Cart::forUser($request->user())->load('items.artwork.category');

        return view('auth.cart.index', [
            'cart' => $cart,
            'items' => $cart->items,
            'subtotal' => $cart->subtotal(),
        ]);
    }

    /**
     * Add a single artwork licence to the cart.
     */
    public function store(Request $request, Artwork $artwork): RedirectResponse
    {
        abort_unless($artwork->is_active, 404);

        $user = $request->user();
        $cart = Cart::forUser($user);

        if ($user->hasPurchased($artwork)) {
            return back()->with('info', 'You already own "'.$artwork->title.'". Open your library to download it.');
        }

        $item = $cart->items()->firstOrCreate(
            ['artwork_id' => $artwork->id],
            ['quantity' => 1],
        );

        if (! $item->wasRecentlyCreated) {
            return back()->with('info', '"'.$artwork->title.'" is already in your cart.');
        }

        return back()->with('success', '"'.$artwork->title.'" was added to your cart.');
    }

    public function destroy(Request $request, Artwork $artwork): RedirectResponse
    {
        Cart::forUser($request->user())
            ->items()
            ->where('artwork_id', $artwork->id)
            ->delete();

        return back()->with('success', '"'.$artwork->title.'" was removed from your cart.');
    }

    public function clear(Request $request): RedirectResponse
    {
        Cart::forUser($request->user())->items()->delete();

        return back()->with('success', 'Your cart is now empty.');
    }
}
