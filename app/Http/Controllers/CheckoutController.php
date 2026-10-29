<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $cart = Cart::forUser($request->user())->load('items.artwork.category');

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('info', 'Your cart is empty — add an artwork before checking out.');
        }

        $subtotal = $cart->subtotal();
        $tax = round($subtotal * (float) config('artiva.tax_rate'), 2);

        return view('checkout.index', [
            'cart' => $cart,
            'items' => $cart->items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => round($subtotal + $tax, 2),
            'user' => $request->user(),
        ]);
    }

    /**
     * Create an unpaid order. Never mark a payment as paid based on form input.
     * Real card/PayPal processing must be handled by a gateway and verified server-side.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'billing_name' => ['required', 'string', 'max:120'],
            'billing_email' => ['required', 'email', 'max:150'],
            'billing_phone' => ['nullable', 'string', 'max:30'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:gcash,card,paypal'],
            'payment_reference' => ['nullable', 'required_if:payment_method,gcash', 'string', 'max:100'],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Please accept the Artiva licence terms to continue.',
            'payment_reference.required_if' => 'Enter the GCash transaction reference so the payment can be checked.',
        ]);

        $user = $request->user();
        $cart = Cart::forUser($user)->load('items.artwork');

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty — nothing to check out.');
        }

        $subtotal = round((float) $cart->items->sum(
            fn ($item) => (float) $item->artwork->price * $item->quantity
        ), 2);
        $tax = round($subtotal * (float) config('artiva.tax_rate'), 2);
        $total = round($subtotal + $tax, 2);

        $order = DB::transaction(function () use ($user, $cart, $data, $subtotal, $tax, $total) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => $data['payment_method'],
                'billing_name' => $data['billing_name'],
                'billing_email' => $data['billing_email'],
                'billing_phone' => $data['billing_phone'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'paid_at' => null,
            ]);

            foreach ($cart->items as $item) {
                /** @var Artwork $artwork */
                $artwork = $item->artwork;
                $lineTotal = round((float) $artwork->price * $item->quantity, 2);
                $order->items()->create([
                    'artwork_id' => $artwork->id,
                    'title' => $artwork->title,
                    'artist_name' => $artwork->artist_name,
                    'price' => $artwork->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $lineTotal,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => $data['payment_method'],
                'transaction_id' => null,
                'amount' => $total,
                'status' => Order::PAYMENT_PENDING,
                'payload' => [
                    'verification' => 'manual_review_required',
                    'customer_reference' => $data['payment_reference'] ?? null,
                    'note' => $data['payment_method'] === 'gcash'
                        ? 'Customer supplied a GCash reference; verify against actual received funds before approving.'
                        : 'Payment gateway is not connected. Do not approve until a real provider confirms payment.',
                ],
                'paid_at' => null,
            ]);

            // Keep order details while awaiting payment verification.
            $cart->items()->delete();

            return $order;
        });

        return redirect()
            ->route('checkout.success', $order)
            ->with('info', 'Order created. Payment is pending verification; downloads remain locked until payment is confirmed.');
    }

    public function success(Request $request, Order $order): View
    {
        $this->authorizeOrder($request, $order);
        $order->load('items.artwork', 'payments');

        return view('checkout.success', ['order' => $order]);
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || $order->user_id === $user->id), 404);
    }
}
