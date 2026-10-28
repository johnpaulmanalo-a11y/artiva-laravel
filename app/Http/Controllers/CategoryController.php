<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * All artwork inside a single category.
     */
    public function show(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        $sort = $request->string('sort')->toString() ?: 'newest';

        $artworks = Artwork::query()
            ->active()
            ->where('category_id', $category->id)
            ->with('category')
            ->when($sort === 'price_low', fn ($query) => $query->orderBy('price'))
            ->when($sort === 'price_high', fn ($query) => $query->orderByDesc('price'))
            ->when($sort === 'title', fn ($query) => $query->orderBy('title'))
            ->when(! in_array($sort, ['price_low', 'price_high', 'title'], true), fn ($query) => $query->latest())
            ->paginate(9)
            ->withQueryString();

        return view('categories.show', [
            'category' => $category,
            'artworks' => $artworks,
            'sort' => $sort,
            'categories' => Category::query()
                ->active()
                ->withCount(['artworks' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('name')
                ->get(),
        ]);
    }
}
