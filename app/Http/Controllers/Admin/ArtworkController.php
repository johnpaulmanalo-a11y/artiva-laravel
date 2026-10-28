<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'in:active,inactive,featured'],
            'sort' => ['nullable', 'in:newest,oldest,price_low,price_high,title'],
        ]);

        $sort = $filters['sort'] ?? 'newest';

        $artworks = Artwork::query()
            ->with('category')
            ->withCount('orderItems')
            ->search($filters['q'] ?? null)
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category_id', $category))
            ->when(($filters['status'] ?? null) === 'active', fn ($query) => $query->where('is_active', true))
            ->when(($filters['status'] ?? null) === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when(($filters['status'] ?? null) === 'featured', fn ($query) => $query->where('is_featured', true))
            ->when($sort === 'price_low', fn ($query) => $query->orderBy('price'))
            ->when($sort === 'price_high', fn ($query) => $query->orderByDesc('price'))
            ->when($sort === 'title', fn ($query) => $query->orderBy('title'))
            ->when($sort === 'oldest', fn ($query) => $query->oldest())
            ->when($sort === 'newest', fn ($query) => $query->latest())
            ->paginate(10)
            ->withQueryString();

        return view('admin.artworks.index', [
            'artworks' => $artworks,
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $filters,
            'sort' => $sort,
        ]);
    }

    public function create(): View
    {
        return view('admin.artworks.create', [
            'artwork' => new Artwork(['is_active' => true, 'file_format' => 'PNG']),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['slug'] = Artwork::uniqueSlug($data['title']);
        $data['preview_path'] = $request->hasFile('preview')
            ? $request->file('preview')->store('previews', 'public')
            : null;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('artworks', 'local');
            $data['file_size_kb'] = (int) ceil($request->file('file')->getSize() / 1024);
        }

        $artwork = Artwork::create($data);

        return redirect()
            ->route('admin.artworks.show', $artwork)
            ->with('success', '"'.$artwork->title.'" was added to the catalogue.');
    }

    public function show(Artwork $artwork): View
    {
        $artwork->load('category')
            ->loadCount(['orderItems as sold_count' => fn ($query) => $query->whereHas('order', fn ($order) => $order->paid())]);

        return view('admin.artworks.show', [
            'artwork' => $artwork,
            'revenue' => (float) $artwork->orderItems()
                ->whereHas('order', fn ($query) => $query->paid())
                ->sum('subtotal'),
            'recentOrders' => $artwork->orderItems()
                ->with('order.user')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    public function edit(Artwork $artwork): View
    {
        return view('admin.artworks.edit', [
            'artwork' => $artwork,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Artwork $artwork): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('preview')) {
            if ($artwork->preview_path) {
                Storage::disk('public')->delete($artwork->preview_path);
            }

            $data['preview_path'] = $request->file('preview')->store('previews', 'public');
        }

        if ($request->hasFile('file')) {
            if ($artwork->file_path) {
                Storage::disk('local')->delete($artwork->file_path);
            }

            $data['file_path'] = $request->file('file')->store('artworks', 'local');
            $data['file_size_kb'] = (int) ceil($request->file('file')->getSize() / 1024);
        }

        $artwork->update($data);

        return redirect()
            ->route('admin.artworks.show', $artwork)
            ->with('success', '"'.$artwork->title.'" was updated.');
    }

    public function destroy(Artwork $artwork): RedirectResponse
    {
        if ($artwork->orderItems()->exists()) {
            return back()->with('error', 'This artwork has sales history, so it cannot be deleted. Deactivate it instead.');
        }

        if ($artwork->preview_path) {
            Storage::disk('public')->delete($artwork->preview_path);
        }

        if ($artwork->file_path) {
            Storage::disk('local')->delete($artwork->file_path);
        }

        $title = $artwork->title;
        $artwork->delete();

        return redirect()
            ->route('admin.artworks.index')
            ->with('success', '"'.$title.'" was deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'artist_name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:4000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'file_format' => ['required', 'string', 'max:10'],
            'resolution' => ['nullable', 'string', 'max:40'],
            'preview' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,zip,psd,pdf,svg,tiff', 'max:20480'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
