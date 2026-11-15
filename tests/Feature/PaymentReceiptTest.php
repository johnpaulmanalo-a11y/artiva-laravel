<?php

namespace Tests\Feature;

use App\Mail\PurchaseReceipt;
use App\Models\Artwork;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class PaymentReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_pending_payment_does_not_create_or_expose_a_receipt(): void
    {
        Mail::fake();
        [$customer, $order] = $this->createPendingOrder();

        $this->actingAs($customer)
            ->get(route('orders.receipt', $order))
            ->assertNotFound();

        $this->assertDatabaseCount('receipts', 0);
        Mail::assertNothingSent();
    }

    public function test_admin_verification_issues_one_receipt_and_sends_only_one_email(): void
    {
        Mail::fake();
        [$customer, $order, $payment] = $this->createPendingOrder();
        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        $verification = [
            'confirm_received' => '1',
            'verified_transaction_id' => 'GCASH-VERIFIED-12345',
        ];

        $this->actingAs($admin)
            ->patch(route('admin.orders.verify-payment', $order), $verification)
            ->assertRedirect(route('admin.orders.show', $order));

        $receipt = Receipt::query()->where('order_id', $order->id)->firstOrFail();
        $this->assertSame($payment->id, $receipt->payment_id);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Order::PAYMENT_PAID,
            'provider_reference' => 'GCASH-VERIFIED-12345',
            'verified_by' => $admin->id,
        ]);
        $this->assertNotNull($receipt->email_sent_at);
        Mail::assertSent(PurchaseReceipt::class, function (PurchaseReceipt $mail) use ($customer, $receipt) {
            $html = $mail->render();

            return $mail->hasTo($customer->email)
                && $mail->receipt->receipt_number === $receipt->receipt_number
                && str_contains($html, $customer->name)
                && str_contains($html, $customer->email)
                && str_contains($html, 'GCASH-VERIFIED-12345')
                && str_contains($html, $receipt->order->items->first()->title)
                && str_contains($html, 'PAID / VERIFIED')
                && str_contains($html, 'name="viewport"');
        });

        $this->actingAs($admin)
            ->patch(route('admin.orders.verify-payment', $order), $verification)
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseCount('receipts', 1);
        Mail::assertSent(PurchaseReceipt::class, 1);

        $this->actingAs($customer)
            ->get(route('orders.receipt', $order))
            ->assertOk()
            ->assertSee($receipt->receipt_number)
            ->assertSee('PAID / VERIFIED');
    }

    public function test_customers_cannot_view_another_customers_receipt(): void
    {
        [$owner, $order] = $this->createPendingOrder();
        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();

        $this->actingAs($admin)->patch(route('admin.orders.verify-payment', $order), [
            'confirm_received' => '1',
            'verified_transaction_id' => 'CARD-VERIFIED-12345',
        ]);

        $otherCustomer = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->whereKeyNot($owner->id)
            ->firstOrFail();

        $this->actingAs($otherCustomer)
            ->get(route('orders.receipt', $order))
            ->assertNotFound();
    }

    public function test_invalid_verification_does_not_mark_payment_paid_or_issue_a_receipt(): void
    {
        Mail::fake();
        [, $order] = $this->createPendingOrder();
        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        $duplicateOrder = Order::query()->whereKeyNot($order->id)->firstOrFail();
        $duplicatePayment = $duplicateOrder->payments()->firstOrFail();
        $duplicatePayment->update(['provider_reference' => 'GCASH-ALREADY-USED']);

        $this->actingAs($admin)
            ->patch(route('admin.orders.verify-payment', $order), [
                'confirm_received' => '1',
                'verified_transaction_id' => 'GCASH-ALREADY-USED',
            ])
            ->assertStatus(422);

        $this->assertSame(Order::PAYMENT_PENDING, $order->fresh()->payment_status);
        $this->assertDatabaseCount('receipts', 0);
        Mail::assertNothingSent();
    }

    public function test_admin_can_verify_a_paid_legacy_payment_before_issuing_its_receipt(): void
    {
        Mail::fake();
        [$customer, $order, $payment] = $this->createPendingOrder();
        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        $verifiedAt = now();

        $payment->forceFill([
            'status' => Order::PAYMENT_PAID,
            'paid_at' => $verifiedAt,
        ])->save();
        $order->forceFill([
            'payment_status' => Order::PAYMENT_PAID,
            'status' => Order::STATUS_PROCESSING,
            'paid_at' => $verifiedAt,
        ])->save();

        $this->actingAs($admin)
            ->patch(route('admin.orders.verify-payment', $order), [
                'confirm_received' => '1',
                'verified_transaction_id' => 'GCASH-LEGACY-VERIFIED-12345',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'provider_reference' => 'GCASH-LEGACY-VERIFIED-12345',
            'verified_by' => $admin->id,
        ]);
        $this->assertNotNull($payment->fresh()->verified_at);
        $this->assertDatabaseHas('receipts', [
            'order_id' => $order->id,
            'payment_id' => $payment->id,
        ]);
        Mail::assertSent(PurchaseReceipt::class, fn (PurchaseReceipt $mail) => $mail->hasTo($customer->email));
    }

    public function test_mail_transport_failure_keeps_payment_verified_and_receipt_retryable(): void
    {
        [$customer, $order] = $this->createPendingOrder();
        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        Mail::shouldReceive('to')
            ->once()
            ->with($customer->email)
            ->andThrow(new TransportException('SMTP unavailable'));

        $this->actingAs($admin)
            ->patch(route('admin.orders.verify-payment', $order), [
                'confirm_received' => '1',
                'verified_transaction_id' => 'GCASH-MAIL-FAILURE-12345',
            ])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('warning');

        $this->assertSame(Order::PAYMENT_PAID, $order->fresh()->payment_status);
        $this->assertNotNull($order->receipt()->firstOrFail());
        $this->assertNull($order->receipt()->firstOrFail()->email_sent_at);
    }

    /**
     * @return array{User, Order, Payment}
     */
    private function createPendingOrder(): array
    {
        $customer = User::query()->where('role', User::ROLE_CUSTOMER)->firstOrFail();
        $artwork = Artwork::query()->active()->firstOrFail();
        $amount = (float) $artwork->price;
        $order = Order::query()->create([
            'user_id' => $customer->id,
            'order_number' => Order::generateOrderNumber(),
            'subtotal' => $amount,
            'tax' => 0,
            'total' => $amount,
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_PENDING,
            'payment_method' => 'gcash',
            'billing_name' => $customer->name,
            'billing_email' => $customer->email,
        ]);
        $order->items()->create([
            'artwork_id' => $artwork->id,
            'title' => $artwork->title,
            'artist_name' => $artwork->artist_name,
            'price' => $amount,
            'quantity' => 1,
            'subtotal' => $amount,
        ]);

        $payment = $order->payments()->create([
            'method' => 'gcash',
            'transaction_id' => Payment::generateTransactionId(),
            'amount' => $amount,
            'status' => Order::PAYMENT_PENDING,
            'payload' => ['verification' => 'manual_review_required'],
        ]);

        return [$customer, $order, $payment];
    }
}
