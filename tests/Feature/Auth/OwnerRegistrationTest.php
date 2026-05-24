<?php

namespace Tests\Feature\Auth;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_owner_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('owner.register'));

        $response->assertOk()
            ->assertSee('Owner Registration')
            ->assertSee('Create an owner account');
    }

    public function test_new_owner_can_register(): void
    {
        $response = $this->post(route('owner.register'), [
            'first_name' => 'Jane',
            'last_name' => 'Owner',
            'email' => 'owner@example.com',
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

    public function test_owner_registration_requires_password_confirmation(): void
    {
        $response = $this->post(route('owner.register'), [
            'first_name' => 'Jane',
            'last_name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
