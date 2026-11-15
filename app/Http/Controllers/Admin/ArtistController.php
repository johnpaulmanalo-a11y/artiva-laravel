<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ArtistController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
        ]);
        $term = trim($filters['q'] ?? '');

        $artists = Artist::query()
            ->withCount('artworks')
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%'.$term.'%')
                        ->orWhere('specialization', 'like', '%'.$term.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.artists.index', [
            'artists' => $artists,
            'term' => $term,
        ]);
    }

    public function create(): View
    {
        return view('admin.artists.create', [
            'artist' => new Artist,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Artist::uniqueSlug($data['name']);
        $data['photo_path'] = $request->file('photo')?->store('artists', 'public');

        $artist = Artist::create($data);

        return redirect()
            ->route('admin.artists.index')
            ->with('success', '"'.$artist->name.'" was added to the artist directory.');
    }

    public function edit(Artist $artist): View
    {
        return view('admin.artists.edit', [
            'artist' => $artist,
        ]);
    }

    public function update(Request $request, Artist $artist): RedirectResponse
    {
        $data = $this->validated($request, $artist);
        $oldPhotoPath = $artist->photo_path;

        if ($data['name'] !== $artist->name) {
            $data['slug'] = Artist::uniqueSlug($data['name'], $artist->id);
        }

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('artists', 'public');
        }

        $artist->update($data);

        if (isset($data['photo_path']) && $oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()
            ->route('admin.artists.index')
            ->with('success', '"'.$artist->name.'" was updated.');
    }

    public function destroy(Artist $artist): RedirectResponse
    {
        if ($artist->artworks()->exists()) {
            return back()->with('error', 'Reassign this artist’s artworks before deleting the profile.');
        }

        $name = $artist->name;
        $photoPath = $artist->photo_path;
        $artist->delete();

        if ($photoPath) {
            Storage::disk('public')->delete($photoPath);
        }

        return redirect()
            ->route('admin.artists.index')
            ->with('success', '"'.$name.'" was deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Artist $artist = null): array
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:160',
                Rule::unique('artists', 'name')->ignore($artist?->id),
            ],
            'bio' => ['nullable', 'string', 'max:5000'],
            'specialization' => ['nullable', 'string', 'max:160'],
            'portfolio_url' => ['nullable', 'url:http,https', 'max:2048'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:2048'],
            'facebook_url' => ['nullable', 'url:http,https', 'max:2048'],
            'artstation_url' => ['nullable', 'url:http,https', 'max:2048'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:max_width=5000,max_height=5000'],
        ]);

        unset($data['photo']);

        return $data;
    }
}
