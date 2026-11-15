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

        $order->load('items.artwork', 'payments', 'receipt.payment');

        return view('orders.show', [
            'order' => $order,
            'purchasedIds' => $user->purchasedArtworkIds(),
        ]);
    }

    public function receipt(Request $request, Order $order): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $order->user_id === $user->id, 404);

        $receipt = $order->receipt()->with('payment', 'order.user', 'order.items')->firstOrFail();
        abort_unless(
            $order->payment_status === Order::PAYMENT_PAID
                && $receipt->payment->status === Order::PAYMENT_PAID
                && $receipt->payment->verified_by !== null
                && $receipt->payment->verified_at !== null,
            404,
        );

        return view('orders.receipt', ['receipt' => $receipt]);
    }
}
