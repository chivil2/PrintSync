<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_settings_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('security.edit'))
            ->assertOk()
            ->assertSee('Update password');
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = Livewire::test('pages::settings.security')
            ->set('current_password', 'password')
            ->set('password', 'new-password123!')
            ->set('password_confirmation', 'new-password123!')
            ->call('updatePassword');

        $response->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-password123!', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = Livewire::test('pages::settings.security')
            ->set('current_password', 'wrong-password')
            ->set('password', 'new-password123!')
            ->set('password_confirmation', 'new-password123!')
            ->call('updatePassword');

        $response->assertHasErrors(['current_password']);
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = Livewire::test('pages::settings.security')
            ->set('current_password', 'password')
            ->set('password', 'new-password123!')
            ->set('password_confirmation', 'different-password')
            ->call('updatePassword');

        $response->assertHasErrors(['password']);
    }

    public function test_passwords_match_returns_null_when_fields_are_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Livewire::test('pages::settings.security');

        $this->assertNull($component->instance()->passwordsMatch);
    }

    public function test_passwords_match_returns_true_when_passwords_match(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Livewire::test('pages::settings.security')
            ->set('password', 'new-password123!')
            ->set('password_confirmation', 'new-password123!');

        $this->assertTrue($component->instance()->passwordsMatch);
    }

    public function test_passwords_match_returns_false_when_passwords_differ(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Livewire::test('pages::settings.security')
            ->set('password', 'new-password123!')
            ->set('password_confirmation', 'different-password');

        $this->assertFalse($component->instance()->passwordsMatch);
    }
}
