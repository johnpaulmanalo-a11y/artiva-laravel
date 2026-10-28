<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * The Artiva landing page.
     */
    public function index(): View
    {
        $featured = Artwork::query()
            ->active()
            ->featured()
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        if ($featured->isEmpty()) {
            $featured = Artwork::query()
                ->active()
                ->with('category')
                ->latest()
                ->take(6)
                ->get();
        }

        $latest = Artwork::query()
            ->active()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::query()
            ->active()
            ->withCount(['artworks' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('home', [
            'featured' => $featured,
            'latest' => $latest,
            'categories' => $categories,
            'stats' => $this->storeStats(),
        ]);
    }

    /**
     * The About Artiva page.
     */
    public function about(): View
    {
        return view('about', [
            'stats' => $this->storeStats(),
            'categories' => Category::query()
                ->active()
                ->withCount(['artworks' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Small aggregate numbers shared by the marketing pages.
     *
     * @return array<string, int|float>
     */
    private function storeStats(): array
    {
        return [
            'artworks' => Artwork::query()->active()->count(),
            'artists' => Artwork::query()->active()->distinct()->count('artist_name'),
            'customers' => User::query()->where('role', User::ROLE_CUSTOMER)->count(),
            'orders' => Order::query()->paid()->count(),
        ];
    }
}
