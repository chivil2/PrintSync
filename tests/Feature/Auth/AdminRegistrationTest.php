<?php

namespace Tests\Feature\Auth;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_admin_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('admin.register'));

        $response->assertOk()
            ->assertSee('Admin Registration')
            ->assertSee('Create an admin account');
    }

    public function test_new_admin_can_register(): void
    {
        $response = $this->post(route('admin.register'), [
            'first_name' => 'Jane',
            'last_name' => 'Owner',
            'email' => 'admin@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertTrue($user->hasRole('owner'));
        $this->assertFalse($user->hasRole('customer'));
    }

    public function test_admin_registration_requires_password_confirmation(): void
    {
        $response = $this->post(route('admin.register'), [
            'first_name' => 'Jane',
            'last_name' => 'Owner',
            'email' => 'admin@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
