<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_customer_can_sign_in(): void
    {
        $customer = User::where('email', 'customer@artiva.test')->firstOrFail();

        $response = $this->post('/login', [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($customer);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'customer@artiva.test',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_registration_creates_a_customer_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Collector',
            'email' => 'collector@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::where('email', 'collector@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_CUSTOMER, $user->role);
        $this->assertTrue(Hash::check('Password123', $user->password));
    }

    public function test_admin_can_open_the_dashboard(): void
    {
        $admin = User::where('email', 'admin@artiva.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Total revenue');
    }

    public function test_customer_cannot_open_admin_pages(): void
    {
        $customer = User::where('email', 'customer@artiva.test')->firstOrFail();

        $this->actingAs($customer)
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs($customer)
            ->get('/admin/artworks')
            ->assertForbidden();
    }
}
