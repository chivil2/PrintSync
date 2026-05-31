<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5 space-y-2">
        <flux:heading level="2" size="lg" class="text-gray-900 font-semibold tracking-tight">
            {{ __('Delete Account') }}
        </flux:heading>
        <flux:subheading size="md" class="text-gray-600 leading-relaxed max-w-2xl">
            {{ __('Delete your account and all of its resources.') }}
        </flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" data-test="delete-user-button">
            {{ __('Delete account') }}
        </flux:button>
    </flux:modal.trigger>

    <livewire:pages::settings.delete-user-modal />
</section>
