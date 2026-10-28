<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function customer(): User
    {
        return User::where('email', 'mara@artiva.test')->firstOrFail();
    }

    private function artwork(): Artwork
    {
        return Artwork::query()->active()->firstOrFail();
    }

    public function test_guests_are_redirected_from_the_cart(): void
    {
        $this->get('/cart')->assertRedirect('/login');
    }

    public function test_customer_can_add_an_artwork_to_the_cart(): void
    {
        $this->actingAs($this->customer())
            ->post('/cart/'.$this->artwork()->slug)
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['artwork_id' => $this->artwork()->id]);
    }

    public function test_customer_can_view_their_cart_page(): void
    {
        $customer = $this->customer();
        $artwork = $this->artwork();

        $this->actingAs($customer)->post('/cart/'.$artwork->slug);

        $this->actingAs($customer)
            ->get('/cart')
            ->assertOk()
            ->assertSee($artwork->title)
            ->assertSee('Shopping cart');
    }

    public function test_checkout_creates_an_order_and_clears_the_cart(): void
    {
        $customer = $this->customer();
        $artwork = Artwork::query()->active()->latest()->firstOrFail();

        $this->actingAs($customer)->post('/cart/'.$artwork->slug);

        $response = $this->actingAs($customer)->post('/checkout', [
            'billing_name' => $customer->name,
            'billing_email' => $customer->email,
            'payment_method' => 'card',
            'card_number' => '4242 4242 4242 4242',
            'card_expiry' => '12/30',
            'card_cvv' => '123',
            'terms' => '1',
        ]);

        $order = Order::query()->where('user_id', $customer->id)->latest('id')->firstOrFail();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertSame(Order::PAYMENT_PAID, $order->payment_status);
        $this->assertEquals($artwork->price, $order->total);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'status' => 'paid']);
    }

    public function test_checkout_requires_billing_details(): void
    {
        $customer = $this->customer();
        $artwork = Artwork::query()->active()->latest()->firstOrFail();

        $this->actingAs($customer)->post('/cart/'.$artwork->slug);

        $response = $this->actingAs($customer)->from('/checkout')->post('/checkout', [
            'payment_method' => 'card',
            'terms' => '1',
        ]);

        $response->assertRedirect('/checkout');
        $response->assertSessionHasErrors(['billing_name', 'billing_email', 'card_number']);
        $this->assertDatabaseCount('orders', 6);
    }

    public function test_customer_cannot_download_an_unpurchased_artwork(): void
    {
        $artwork = Artwork::query()->active()->whereNotIn(
            'id',
            $this->customer()->purchasedArtworkIds()
        )->firstOrFail();

        $this->actingAs($this->customer())
            ->get('/library/'.$artwork->slug.'/download')
            ->assertForbidden();
    }

    public function test_customer_can_download_purchased_artwork(): void
    {
        $customer = $this->customer();

        /** @var Artwork $owned */
        $owned = Artwork::query()->findOrFail($customer->purchasedArtworkIds()[0]);

        $this->actingAs($customer)
            ->get('/library/'.$owned->slug.'/download')
            ->assertOk();
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $otherOrder = Order::query()
            ->where('user_id', '!=', $this->customer()->id)
            ->firstOrFail();

        $this->actingAs($this->customer())
            ->get('/orders/'.$otherOrder->id)
            ->assertNotFound();
    }
}
