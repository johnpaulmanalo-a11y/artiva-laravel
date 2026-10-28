<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->toString();

        $categories = Category::query()
            ->withCount('artworks')
            ->when($term, fn ($query) => $query->where('name', 'like', '%'.$term.'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
            'term' => $term,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['slug'] = Category::uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = $request->hasFile('image')
            ? $request->file('image')->store('categories', 'public')
            : null;

        $category = Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', '"'.$category->name.'" was created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['slug'] = $category->slug;

        if ($data['name'] !== $category->name) {
            $data['slug'] = Category::uniqueSlug($data['name'], $category->id);
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', '"'.$category->name.'" was updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->artworks()->exists()) {
            return back()->with('error', 'Move or delete the artworks in this category first.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', '"'.$name.'" was deleted.');
    }
}
