<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * The signed-in customer's order history.
     */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->withCount('items')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'status' => $status,
            'statuses' => Order::statusOptions(),
        ]);
    }

    /**
     * A single order, only visible to its owner (or an admin).
     */
    public function show(Request $request, Order $order): View
    {
        $user = $request->user();

        abort_unless($user->isAdmin() || $order->user_id === $user->id, 404);

        $order->load('items.artwork', 'payments');

        return view('orders.show', [
            'order' => $order,
            'purchasedIds' => $user->purchasedArtworkIds(),
        ]);
    }
}
