<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
        ]);
        $term = trim($filters['q'] ?? '');

        $artists = Artist::query()
            ->whereHas('artworks', fn ($query) => $query->active())
            ->withCount(['artworks' => fn ($query) => $query->active()])
            ->when($term !== '', fn ($query) => $query->where('name', 'like', '%'.$term.'%'))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('artists.index', [
            'artists' => $artists,
            'term' => $term,
        ]);
    }

    public function show(Artist $artist): View
    {
        $artworks = $artist->artworks()
            ->active()
            ->with('category')
            ->latest()
            ->paginate(9);

        abort_if($artworks->total() === 0, 404);

        return view('artists.show', [
            'artist' => $artist,
            'artworks' => $artworks,
        ]);
    }
}
