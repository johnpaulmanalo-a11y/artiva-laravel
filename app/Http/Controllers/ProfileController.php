<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('profile.index', [
            'user' => $user,
            'stats' => [
                'orders' => $user->orders()->count(),
                'paid_orders' => $user->orders()->paid()->count(),
                'library' => count($user->purchasedArtworkIds()),
                'wishlist' => Wishlist::query()->where('user_id', $user->id)->count(),
            ],
            'recentOrders' => $user->orders()->latest()->take(4)->get(),
            'purchased' => Artwork::query()
                ->whereIn('id', array_slice($user->purchasedArtworkIds(), 0, 4))
                ->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Your profile was updated.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => $data['password'],
        ]);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Your password was changed.');
    }
}
