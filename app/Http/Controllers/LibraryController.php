<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    /**
     * Every artwork the customer has bought, with download access.
     */
    public function index(Request $request): View
    {
        $ids = $request->user()->purchasedArtworkIds();

        $artworks = Artwork::query()
            ->whereIn('id', $ids)
            ->with('category')
            ->orderBy('title')
            ->get();

        return view('library.index', [
            'artworks' => $artworks,
            'orderCount' => $request->user()->orders()->paid()->count(),
        ]);
    }

    /**
     * Stream the private master file to a verified buyer.
     */
    public function download(Request $request, Artwork $artwork): \Symfony\Component\HttpFoundation\Response
    {
        $user = $request->user();

        abort_unless($user->hasPurchased($artwork), 403, 'You have not purchased this artwork.');

        if (! $artwork->hasDownloadableFile()) {
            return redirect()
                ->route('library.index')
                ->with('error', 'The master file for "'.$artwork->title.'" has not been uploaded yet. Please contact support.');
        }

        $artwork->increment('downloads_count');

        // Files live on the private "local" disk, never inside public/.
        return Storage::disk('local')->download($artwork->file_path, $artwork->downloadFileName());
    }
}
