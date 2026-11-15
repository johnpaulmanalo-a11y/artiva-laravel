<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationPersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_new_customer_is_visible_in_the_admin_customer_list(): void
    {
        $this->post('/register', [
            'name' => 'Dashboard Collector',
            'email' => 'dashboard.collector@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertRedirect('/');

        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.customers.index', ['q' => 'dashboard.collector@example.com']))
            ->assertOk()
            ->assertSee('Dashboard Collector')
            ->assertSee('dashboard.collector@example.com');
    }
}
