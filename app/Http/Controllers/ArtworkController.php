<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    /**
     * Browse, search, filter and sort the artwork catalogue.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:80'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:newest,oldest,price_low,price_high,title'],
        ]);

        $sort = $filters['sort'] ?? 'newest';

        $artworks = Artwork::query()
            ->active()
            ->with('category')
            ->search($filters['q'] ?? null)
            ->when($filters['category'] ?? null, function ($query, $slug) {
                $query->whereHas('category', fn ($category) => $category->where('slug', $slug));
            })
            ->when($filters['min_price'] ?? null, fn ($query, $min) => $query->where('price', '>=', $min))
            ->when($filters['max_price'] ?? null, fn ($query, $max) => $query->where('price', '<=', $max))
            ->when($sort === 'price_low', fn ($query) => $query->orderBy('price'))
            ->when($sort === 'price_high', fn ($query) => $query->orderByDesc('price'))
            ->when($sort === 'title', fn ($query) => $query->orderBy('title'))
            ->when($sort === 'oldest', fn ($query) => $query->oldest())
            ->when($sort === 'newest', fn ($query) => $query->latest())
            ->paginate(9)
            ->withQueryString();

        return view('artworks.index', [
            'artworks' => $artworks,
            'categories' => Category::query()
                ->active()
                ->withCount(['artworks' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('name')
                ->get(),
            'filters' => $filters,
            'sort' => $sort,
            'resultCount' => $artworks->total(),
        ]);
    }

    /**
     * Artwork details page.
     */
    public function show(Artwork $artwork): View
    {
        abort_unless($artwork->is_active || auth()->user()?->isAdmin(), 404);

        $artwork->load('category');

        $related = Artwork::query()
            ->active()
            ->where('category_id', $artwork->category_id)
            ->whereKeyNot($artwork->getKey())
            ->with('category')
            ->latest()
            ->take(4)
            ->get();

        $inWishlist = false;
        $inCart = false;
        $purchased = false;

        if ($user = auth()->user()) {
            $inWishlist = Wishlist::query()
                ->where('user_id', $user->id)
                ->where('artwork_id', $artwork->id)
                ->exists();

            $inCart = $user->cart()
                ->whereHas('items', fn ($query) => $query->where('artwork_id', $artwork->id))
                ->exists();

            $purchased = $user->hasPurchased($artwork);
        }

        return view('artworks.show', [
            'artwork' => $artwork,
            'related' => $related,
            'inWishlist' => $inWishlist,
            'inCart' => $inCart,
            'purchased' => $purchased,
        ]);
    }
}
