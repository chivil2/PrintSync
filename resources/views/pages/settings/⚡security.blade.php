<?php

use App\Concerns\PasswordValidationRules;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Security settings')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    #[Computed]
    public function passwordsMatch(): ?bool
    {
        if ($this->password === '' || $this->password_confirmation === '') {
            return null;
        }

        return $this->password === $this->password_confirmation;
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The current password is incorrect.'),
            'password.confirmed' => __('The password confirmation does not match.'),
        ]);

        $user = Auth::user();
        $user->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Flux::toast(variant: 'success', text: __('Password updated successfully.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Security settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form wire:submit="updatePassword" class="mt-6 space-y-6">
            <!-- Current password -->
            <flux:input wire:model="current_password" :label="__('Current password')" type="password" viewable />

            <!-- New password -->
            <flux:input wire:model="password" :label="__('New password')" type="password" viewable />

            <!-- Confirm password -->
            <flux:input wire:model="password_confirmation" :label="__('Confirm new password')" type="password" viewable />

            @if ($this->passwordsMatch === false)
                <flux:text variant="danger" class="text-sm">
                    {{ __('Passwords do not match.') }}
                </flux:text>
            @endif

            <!-- Submit -->
            <div class="flex items-center gap-4">
                <flux:button
                    variant="primary"
                    type="submit"
                    :disabled="$this->passwordsMatch === false"
                    data-test="update-password-button"
                >
                    {{ __('Update Password') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
