<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $items = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->with('artwork.category')
            ->latest()
            ->paginate(9);

        return view('wishlist.index', [
            'items' => $items,
            'artworks' => $items->pluck('artwork')->filter(),
        ]);
    }

    /**
     * Toggle an artwork in the signed-in customer's wishlist.
     */
    public function store(Request $request, Artwork $artwork): RedirectResponse
    {
        $existing = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->where('artwork_id', $artwork->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', '"'.$artwork->title.'" was removed from your wishlist.');
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'artwork_id' => $artwork->id,
        ]);

        return back()->with('success', '"'.$artwork->title.'" was saved to your wishlist.');
    }

    public function destroy(Request $request, Artwork $artwork): RedirectResponse
    {
        Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->where('artwork_id', $artwork->id)
            ->delete();

        return back()->with('success', '"'.$artwork->title.'" was removed from your wishlist.');
    }
}
