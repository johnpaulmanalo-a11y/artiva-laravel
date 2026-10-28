<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (Order::query()->exists()) {
            return;
        }

        $customers = User::where('role', User::ROLE_CUSTOMER)->orderBy('id')->get();
        $artworks = Artwork::query()->orderBy('id')->get();

        if ($customers->isEmpty() || $artworks->isEmpty()) {
            return;
        }

        $plans = [
            ['months_ago' => 4, 'day' => 8, 'picks' => [0, 2], 'status' => Order::STATUS_COMPLETED],
            ['months_ago' => 3, 'day' => 17, 'picks' => [4], 'status' => Order::STATUS_COMPLETED],
            ['months_ago' => 2, 'day' => 3, 'picks' => [1, 3, 5], 'status' => Order::STATUS_COMPLETED],
            ['months_ago' => 1, 'day' => 21, 'picks' => [6], 'status' => Order::STATUS_PROCESSING],
            ['months_ago' => 0, 'day' => 6, 'picks' => [7, 8], 'status' => Order::STATUS_PROCESSING],
            ['months_ago' => 0, 'day' => 12, 'picks' => [9], 'status' => Order::STATUS_COMPLETED],
        ];

        foreach ($plans as $index => $plan) {
            $customer = $customers[$index % $customers->count()];
            $date = now()->subMonths($plan['months_ago'])->day($plan['day'])->setTime(10 + $index, 15);

            $picks = collect($plan['picks'])
                ->map(fn (int $position) => $artworks[$position % $artworks->count()])
                ->unique('id')
                ->values();

            $subtotal = (float) $picks->sum(fn (Artwork $artwork) => (float) $artwork->price);
            $tax = round($subtotal * (float) config('artiva.tax_rate'), 2);
            $total = round($subtotal + $tax, 2);

            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => 'ART-'.$date->format('Ymd').'-'.Str::upper(Str::random(5)),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $plan['status'],
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => 'card',
                'billing_name' => $customer->name,
                'billing_email' => $customer->email,
                'billing_phone' => $customer->phone,
                'billing_address' => '12 Gallery Lane, Suite '.($index + 3),
                'paid_at' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($picks as $artwork) {
                $order->items()->create([
                    'artwork_id' => $artwork->id,
                    'title' => $artwork->title,
                    'artist_name' => $artwork->artist_name,
                    'price' => $artwork->price,
                    'quantity' => 1,
                    'subtotal' => $artwork->price,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $artwork->increment('downloads_count');
            }

            $order->payments()->create([
                'method' => 'card',
                'transaction_id' => 'SIM-'.Str::upper(Str::random(12)),
                'amount' => $total,
                'status' => Order::PAYMENT_PAID,
                'payload' => [
                    'gateway' => 'Artiva Simulated Gateway',
                    'message' => 'Seeded demo transaction — no real money moved.',
                    'card_last_four' => '4242',
                ],
                'paid_at' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
