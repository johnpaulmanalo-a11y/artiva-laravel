<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->toString();

        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->when($term, function ($query) use ($term) {
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%');
                });
            })
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn ($query) => $query->where('payment_status', Order::PAYMENT_PAID)], 'total')
            ->withMax('orders', 'created_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'term' => $term,
            'summary' => [
                'customers' => User::query()->where('role', User::ROLE_CUSTOMER)->count(),
                'buyers' => User::query()
                    ->where('role', User::ROLE_CUSTOMER)
                    ->whereHas('orders', fn ($query) => $query->paid())
                    ->count(),
                'lifetime' => (float) Order::query()->paid()->sum('total'),
            ],
        ]);
    }

    public function show(User $user): View
    {
        abort_unless($user->isCustomer(), 404);

        return view('admin.customers.show', [
            'customer' => $user,
            'orders' => $user->orders()->withCount('items')->latest()->paginate(8),
            'stats' => [
                'orders' => $user->orders()->count(),
                'spent' => (float) $user->orders()->paid()->sum('total'),
                'library' => count($user->purchasedArtworkIds()),
                'wishlist' => $user->wishlist()->count(),
            ],
        ]);
    }
}
