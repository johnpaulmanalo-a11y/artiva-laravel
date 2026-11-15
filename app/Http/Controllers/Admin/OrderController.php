<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\ReceiptService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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
        $order->load('user', 'items.artwork', 'payments.verifiedBy', 'receipt.payment');

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statusOptions(),
        ]);
    }

    /**
     * Manually confirm payment only after checking the provider's real transaction records.
     */
    public function verifyPayment(Request $request, Order $order, ReceiptService $receipts): RedirectResponse
    {
        $data = $request->validate([
            'confirm_received' => ['accepted'],
            'verified_transaction_id' => ['required', 'string', 'max:100'],
        ]);

        [$paymentId, $alreadyPaid] = DB::transaction(function () use ($request, $order, $data) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->payment_status === Order::PAYMENT_PAID) {
                $verifiedPayment = $order->payments()
                    ->where('status', Order::PAYMENT_PAID)
                    ->whereNotNull('verified_by')
                    ->whereNotNull('verified_at')
                    ->latest('id')
                    ->first();

                if ($verifiedPayment) {
                    return [$verifiedPayment->id, true];
                }
            }

            $payment = $order->payments()
                ->whereIn('status', [Order::PAYMENT_PENDING, Order::PAYMENT_PAID])
                ->where(function ($query) {
                    $query->whereNull('verified_by')
                        ->orWhereNull('verified_at');
                })
                ->latest('id')
                ->lockForUpdate()
                ->firstOrFail();

            $verifiedReference = trim($data['verified_transaction_id']);
            abort_unless($verifiedReference !== '', 422, 'Enter the transaction reference confirmed with the payment provider.');
            abort_if(
                Payment::query()
                    ->where('provider_reference', $verifiedReference)
                    ->where('id', '!=', $payment->id)
                    ->exists(),
                422,
                'This provider transaction reference is already linked to another payment.',
            );
            abort_unless(
                number_format((float) $payment->amount, 2, '.', '') === number_format((float) $order->total, 2, '.', ''),
                422,
                'The payment amount does not match the order total.',
            );

            $verifiedAt = now();
            $payment->forceFill([
                'provider_reference' => $verifiedReference,
                'status' => Order::PAYMENT_PAID,
                'paid_at' => $verifiedAt,
                'verified_by' => $request->user()->id,
                'verified_at' => $verifiedAt,
                'payload' => array_merge($payment->payload ?? [], [
                    'verification' => 'manually_verified_by_admin',
                    'verified_at' => $verifiedAt->toISOString(),
                    'verified_by' => $request->user()->id,
                ]),
            ])->save();

            $order->forceFill([
                'payment_status' => Order::PAYMENT_PAID,
                'status' => Order::STATUS_PROCESSING,
                'paid_at' => $verifiedAt,
            ])->save();

            return [$payment->id, false];
        });

        $receipt = $receipts->issueForVerifiedPayment(Payment::query()->findOrFail($paymentId));

        try {
            $emailSent = $receipts->send($receipt);
        } catch (TransportExceptionInterface $exception) {
            Log::warning('Artiva receipt email delivery failed.', ['receipt_id' => $receipt->id]);

            return redirect()->route('admin.orders.show', $order)
                ->with('warning', 'Payment is verified and the receipt is recorded, but email delivery failed. Check mail settings and retry from this order.');
        }

        $notice = $emailSent
            ? ($alreadyPaid
                ? 'This payment was already verified; the existing receipt was emailed without creating a duplicate.'
                : 'Payment verified and the receipt was sent to the customer’s registered email.')
            : 'This payment is already verified and its receipt email was already sent.';

        return redirect()->route('admin.orders.show', $order)
            ->with($emailSent && ! $alreadyPaid ? 'success' : 'info', $notice);
    }

    public function sendReceiptEmail(Order $order, ReceiptService $receipts): RedirectResponse
    {
        $receipt = $order->receipt()->firstOrFail();

        try {
            $emailSent = $receipts->send($receipt);
        } catch (TransportExceptionInterface $exception) {
            Log::warning('Artiva receipt email retry failed.', ['receipt_id' => $receipt->id]);

            return redirect()->route('admin.orders.show', $order)
                ->with('warning', 'The receipt remains recorded, but email delivery failed again. Check the mail transport configuration.');
        }

        return redirect()->route('admin.orders.show', $order)
            ->with($emailSent ? 'success' : 'info', $emailSent
                ? 'The existing receipt was sent to the customer’s registered email.'
                : 'This receipt email was already sent; no duplicate email was created.');
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
