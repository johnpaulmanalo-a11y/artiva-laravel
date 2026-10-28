<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->toString();
        $status = $request->string('status')->toString();

        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($term, function ($query) use ($term) {
                $query->where(function ($query) use ($term) {
                    $query->where('order_number', 'like', '%'.$term.'%')
                        ->orWhere('billing_name', 'like', '%'.$term.'%')
                        ->orWhere('billing_email', 'like', '%'.$term.'%');
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'term' => $term,
            'status' => $status,
            'statuses' => Order::statusOptions(),
            'summary' => [
                'revenue' => (float) Order::query()->paid()->sum('total'),
                'pending' => Order::query()->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])->count(),
                'total' => Order::query()->count(),
            ],
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('user', 'items.artwork', 'payments');

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statusOptions(),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::statusOptions())],
        ]);

        $status = $data['status'];
        $order->status = $status;

        if ($status === Order::STATUS_REFUNDED) {
            $order->payment_status = Order::PAYMENT_REFUNDED;
            $order->payments()->update(['status' => Order::PAYMENT_REFUNDED]);
        }

        if ($status === Order::STATUS_COMPLETED && $order->isPaid() && $order->paid_at === null) {
            $order->paid_at = now();
        }

        $order->save();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order '.$order->order_number.' is now '.$order->statusLabel().'.');
    }
}
