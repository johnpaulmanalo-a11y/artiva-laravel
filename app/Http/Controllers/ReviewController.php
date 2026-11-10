<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Artwork $artwork): RedirectResponse
    {
        abort_unless($request->user()->hasPurchased($artwork), 403, 'Only customers who purchased this artwork can review it.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'min:5', 'max:1000'],
        ]);

        $request->user()->reviews()->updateOrCreate(
            ['artwork_id' => $artwork->id],
            $data
        );

        return back()->with('success', 'Thank you! Your artwork review has been saved.');
    }
}
