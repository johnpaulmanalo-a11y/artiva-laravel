<?php

namespace App\Services;

use App\Mail\PurchaseReceipt;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use LogicException;

class ReceiptService
{
    public function issueForVerifiedPayment(Payment $payment): Receipt
    {
        $receipt = DB::transaction(function () use ($payment) {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            $order = Order::query()->lockForUpdate()->findOrFail($payment->order_id);

            if (
                $payment->status !== Order::PAYMENT_PAID
                || $payment->verified_by === null
                || $payment->verified_at === null
                || $payment->provider_reference === null
                || $order->payment_status !== Order::PAYMENT_PAID
            ) {
                throw new LogicException('A receipt can only be issued for a verified payment.');
            }

            $receipt = Receipt::query()->firstOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_id' => $payment->id,
                    'receipt_number' => Receipt::generateReceiptNumber(),
                    'issued_at' => $payment->verified_at,
                ],
            );

            if ($receipt->payment_id !== $payment->id) {
                throw new LogicException('This order already has a receipt for a different payment.');
            }

            return $receipt;
        });

        return $receipt->load('order.user', 'order.items', 'payment');
    }

    public function send(Receipt $receipt): bool
    {
        return DB::transaction(function () use ($receipt) {
            $receipt = Receipt::query()->lockForUpdate()->findOrFail($receipt->id);
            $receipt->load('payment', 'order.user', 'order.items');
            $payment = $receipt->payment;

            if (
                $payment->status !== Order::PAYMENT_PAID
                || $payment->verified_by === null
                || $payment->verified_at === null
                || $receipt->order->payment_status !== Order::PAYMENT_PAID
            ) {
                throw new LogicException('A receipt email can only be sent for a verified payment.');
            }

            if ($receipt->email_sent_at !== null) {
                return false;
            }

            Mail::to($receipt->order->user->email)->send(new PurchaseReceipt($receipt));

            $receipt->forceFill(['email_sent_at' => now()])->save();

            return true;
        });
    }
}
