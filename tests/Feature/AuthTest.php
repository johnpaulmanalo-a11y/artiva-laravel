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

    public function test_auth_pages_render_with_the_existing_artiva_layout(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('css/auth.css')
            ->assertSee('Sign in to reach your cart');

        $this->get('/register')
            ->assertOk()
            ->assertSee('Create your account')
            ->assertSee('name="phone"', false);

        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Email password reset link');

        $this->get('/reset-password/test-token?email=collector%40example.com')
            ->assertOk()
            ->assertSee('Choose a new password');
    }

    public function test_customer_profile_still_uses_the_storefront_profile_page(): void
    {
        $customer = User::where('email', 'customer@artiva.test')->firstOrFail();

        $this->actingAs($customer)
            ->get('/profile')
            ->assertOk()
            ->assertSee('My profile');
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
            'phone' => '+1 555 0100',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = User::where('email', 'collector@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_CUSTOMER, $user->role);
        $this->assertSame('+1 555 0100', $user->phone);
        $this->assertTrue(Hash::check('Password123', $user->password));
    }

    public function test_admin_can_open_the_dashboard(): void
    {
        $admin = User::where('email', 'jpcomes.globalmktg@gmail.com')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Total revenue');
    }

    public function test_guests_are_redirected_to_login_from_dashboard_and_admin_crud(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/artworks')->assertRedirect('/login');
        $this->get('/admin/categories')->assertRedirect('/login');
        $this->post('/admin/artworks')->assertRedirect('/login');
    }

    public function test_customers_cannot_access_admin_crud_actions(): void
    {
        $customer = User::where('email', 'customer@artiva.test')->firstOrFail();

        $this->actingAs($customer)->get('/admin/categories')->assertForbidden();
        $this->actingAs($customer)->post('/admin/categories', [])->assertForbidden();
        $this->actingAs($customer)->get('/admin/reports')->assertForbidden();
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
