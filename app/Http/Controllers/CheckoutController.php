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
    /**
     * Review the order before paying.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $cart = Cart::forUser($request->user())->load('items.artwork.category');

        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
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
     * Create the order, record the (simulated) payment and empty the cart.
     */
    public function store(Request $request): RedirectResponse
    {
        // Visitors type card numbers with spaces ("4242 4242 4242 4242"), so the
        // value is normalised to digits before the validation rules run.
        if ($request->filled('card_number')) {
            $request->merge([
                'card_number' => preg_replace('/\D/', '', (string) $request->input('card_number')),
            ]);
        }

        $data = $request->validate([
            'billing_name' => ['required', 'string', 'max:120'],
            'billing_email' => ['required', 'email', 'max:150'],
            'billing_phone' => ['nullable', 'string', 'max:30'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:card,paypal,simulated'],
            'card_number' => ['nullable', 'required_if:payment_method,card', 'digits_between:13,19'],
            'card_expiry' => ['nullable', 'required_if:payment_method,card', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'card_cvv' => ['nullable', 'required_if:payment_method,card', 'digits_between:3,4'],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Please accept the Artiva licence terms to continue.',
        ]);

        $user = $request->user();
        $cart = Cart::forUser($user)->load('items.artwork');

        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
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
                'status' => Order::STATUS_PROCESSING,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => $data['payment_method'],
                'billing_name' => $data['billing_name'],
                'billing_email' => $data['billing_email'],
                'billing_phone' => $data['billing_phone'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'paid_at' => now(),
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
                'transaction_id' => 'SIM-'.Str::upper(Str::random(12)),
                'amount' => $total,
                'status' => Order::PAYMENT_PAID,
                'payload' => [
                    'gateway' => 'Artiva Simulated Gateway',
                    'message' => 'Sandbox transaction approved — no real money moved.',
                    'card_last_four' => isset($data['card_number'])
                        ? substr($data['card_number'], -4)
                        : null,
                ],
                'paid_at' => now(),
            ]);

            // The cart is only cleared once the order and payment are written.
            $cart->items()->delete();

            return $order;
        });

        return redirect()
            ->route('checkout.success', $order)
            ->with('success', 'Payment approved. Your artwork is ready in your library.');
    }

    /**
     * Order confirmation page.
     */
    public function success(Request $request, Order $order): View
    {
        $this->authorizeOrder($request, $order);

        $order->load('items.artwork', 'payments');

        return view('checkout.success', [
            'order' => $order,
        ]);
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        $user = $request->user();

        abort_unless($user && ($user->isAdmin() || $order->user_id === $user->id), 404);
    }
}
