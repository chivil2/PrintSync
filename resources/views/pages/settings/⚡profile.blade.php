<?php

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Flux\Flux;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;
    use PasswordValidationRules;
    use WithFileUploads;

    public string $activeTab = 'profile';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public $photo;

    public ?string $profilePhotoPath = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->profilePhotoPath = $user->profile_photo_path;
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;

        if ($tab === 'security') {
            $this->reset('current_password', 'password', 'password_confirmation');
        }
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast(text: __('A new verification link has been sent to your email address.'));
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

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }

    #[Computed]
    public function lastUpdated(): string
    {
        return Auth::user()->updated_at->diffForHumans();
    }

    public function cancel(): void
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    #[Computed]
    public function profilePhotoUrl(): ?string
    {
        $user = Auth::user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            return Storage::disk('public')->url($user->profile_photo_path);
        }

        return null;
    }

    public function updatedPhoto(): void
    {
        if ($this->photo) {
            $this->uploadPhoto();
        }
    }

    public function uploadPhoto(): void
    {
        $this->validate([
            'photo' => ['image', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $this->photo->store('avatars', 'public');

        $user->update(['profile_photo_path' => $path]);

        $this->profilePhotoPath = $path;
        $this->photo = null;

        Flux::toast(variant: 'success', text: __('Profile photo updated.'));
    }

    public function removePhoto(): void
    {
        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->update(['profile_photo_path' => null]);

        $this->profilePhotoPath = null;

        Flux::toast(variant: 'success', text: __('Profile photo removed.'));
    }
}; ?>

<section class="w-full bg-[#f7f8fa]">
    <div class="mx-auto max-w-4xl px-4 py-10">
        {{-- Page header --}}
        <div class="mb-8">
            <h1 class="text-xl font-bold text-gray-900">{{ __('Account Settings') }}</h1>
            <p class="mt-0.5 text-sm text-gray-500">{{ __('Manage your profile and account settings.') }}</p>
        </div>

        <div class="flex flex-col gap-5 lg:flex-row lg:items-start">

            {{-- ── Sidebar ── --}}
            <aside class="w-full shrink-0 flex flex-col gap-4 lg:w-56">
                {{-- User info card --}}
                <x-settings.card>
                    <div class="flex flex-col items-center gap-2 px-4 py-6">
                        <x-settings.avatar :name="Auth::user()->name" size="lg" :src="$this->profilePhotoUrl" />

                        <div class="mt-1 text-center">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="mt-0.5 break-all text-[11px] text-gray-400">{{ Auth::user()->email }}</p>
                        </div>

                        <span
                            class="mt-1 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-semibold"
                            style="background: rgba(232,112,26,0.1); color: #e8701a"
                        >
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            {{ __('Active Account') }}
                        </span>
                    </div>
                </x-settings.card>

                {{-- Nav tabs --}}
                <x-settings.card>
                    <nav class="flex flex-col gap-0.5 p-1.5">
                        <button
                            wire:click="switchTab('profile')"
                            class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-all duration-150"
                            style="{{ $activeTab === 'profile' ? 'background: rgba(232,112,26,0.08); color: #e8701a' : 'color: #6b7280' }}"
                        >
                            <span style="{{ $activeTab === 'profile' ? 'color: #e8701a' : 'color: #9ca3af' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            {{ __('Profile') }}
                            @if ($activeTab === 'profile')
                                <span class="ml-auto h-1.5 w-1.5 rounded-full" style="background: #e8701a"></span>
                            @endif
                        </button>

                        <button
                            wire:click="switchTab('security')"
                            class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-all duration-150"
                            style="{{ $activeTab === 'security' ? 'background: rgba(232,112,26,0.08); color: #e8701a' : 'color: #6b7280' }}"
                        >
                            <span style="{{ $activeTab === 'security' ? 'color: #e8701a' : 'color: #9ca3af' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            {{ __('Security') }}
                            @if ($activeTab === 'security')
                                <span class="ml-auto h-1.5 w-1.5 rounded-full" style="background: #e8701a"></span>
                            @endif
                        </button>

                    </nav>
                </x-settings.card>
            </aside>

            {{-- ── Content ── --}}
            <div class="min-w-0 flex-1">

                {{-- ════════════════════════════════════════════════════════
                    PROFILE TAB
                ════════════════════════════════════════════════════════ --}}
                @if ($activeTab === 'profile')
                    <x-settings.card wire:key="profile-card">
                        {{-- Header --}}
                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900">{{ __('Personal Information') }}</h2>
                                <p class="mt-0.5 text-xs text-gray-400">{{ __('Update your name and contact details.') }}</p>
                            </div>
                            <span class="hidden h-9 w-9 items-center justify-center rounded-full sm:flex" style="background: rgba(232,112,26,0.07)">
                                <svg class="h-4 w-4" style="color: #e8701a" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                        </div>

                        {{-- Avatar upload --}}
                        <div class="flex items-center gap-4 border-b border-gray-100 bg-gray-50/60 px-6 py-4">
                            <x-settings.avatar :name="Auth::user()->name" size="lg" :src="$this->profilePhotoUrl" />
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ __('Profile Photo') }}</p>
                                <p class="mt-0.5 text-xs text-gray-400">{{ __('JPG, PNG or GIF') }} · {{ __('Max 2 MB') }}</p>
                                <div class="mt-2.5 flex gap-2">
                                    <input
                                        type="file"
                                        wire:model.live="photo"
                                        x-ref="photoInput"
                                        class="hidden"
                                        accept="image/png,image/jpeg,image/gif"
                                    />
                                    <button
                                        type="button"
                                        x-on:click="$refs.photoInput.click()"
                                        wire:loading.attr="disabled"
                                        class="rounded-lg px-3.5 py-1.5 text-xs font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-60"
                                        style="background: #e8701a"
                                    >
                                        <span wire:loading.remove wire:target="photo">{{ __('Upload photo') }}</span>
                                        <span wire:loading wire:target="photo">{{ __('Uploading') }}…</span>
                                    </button>
                                    @if ($this->profilePhotoUrl)
                                        <button
                                            type="button"
                                            wire:click="removePhoto"
                                            wire:loading.attr="disabled"
                                            class="rounded-lg border border-gray-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-gray-500 transition-colors hover:bg-gray-100"
                                        >
                                            {{ __('Remove') }}
                                        </button>
                                    @endif
                                </div>
                                @error('photo')
                                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Form --}}
                        <form wire:submit="updateProfileInformation">
                            <div class="space-y-4 px-6 py-5">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    {{-- First Name --}}
                                    <div class="flex flex-col gap-1.5">
                                        <label for="firstName" class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            {{ __('First Name') }}
                                        </label>
                                        <div
                                            class="flex items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 shadow-[0_1px_2px_rgba(0,0,0,0.04)] transition-all duration-150 focus-within:border-[#e8701a] focus-within:shadow-[0_0_0_3px_rgba(232,112,26,0.1)]"
                                        >
                                            <span class="shrink-0 text-gray-400">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </span>
                                            <input
                                                id="firstName"
                                                type="text"
                                                wire:model="first_name"
                                                placeholder="{{ __('First name') }}"
                                                required
                                                autofocus
                                                autocomplete="given-name"
                                                class="w-full bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none"
                                            />
                                        </div>
                                    </div>

                                    {{-- Last Name --}}
                                    <div class="flex flex-col gap-1.5">
                                        <label for="lastName" class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            {{ __('Last Name') }}
                                        </label>
                                        <div
                                            class="flex items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 shadow-[0_1px_2px_rgba(0,0,0,0.04)] transition-all duration-150 focus-within:border-[#e8701a] focus-within:shadow-[0_0_0_3px_rgba(232,112,26,0.1)]"
                                        >
                                            <span class="shrink-0 text-gray-400">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </span>
                                            <input
                                                id="lastName"
                                                type="text"
                                                wire:model="last_name"
                                                placeholder="{{ __('Last name') }}"
                                                required
                                                autocomplete="family-name"
                                                class="w-full bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none"
                                            />
                                        </div>
                                    </div>
                                </div>

                                {{-- Phone --}}
                                <div class="flex flex-col gap-1.5">
                                    <label for="phone" class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        {{ __('Phone Number') }}
                                    </label>
                                    <div
class="flex items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 shadow-[0_1px_2px_rgba(0,0,0,0.04)] transition-all duration-150 focus-within:border-[#e8701a] focus-within:shadow-[0_0_0_3px_rgba(232,112,26,0.1)]"
                                    >
                                        <span class="shrink-0 text-gray-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </span>
                                        <input
                                            id="phone"
                                            type="tel"
                                            wire:model="phone"
                                            placeholder="+1 (555) 000-0000"
                                            autocomplete="tel"
                                            class="w-full bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none"
                                        />
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="flex flex-col gap-1.5">
                                    <label for="email" class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        {{ __('Email Address') }}
                                    </label>
                                    <div
class="flex items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 shadow-[0_1px_2px_rgba(0,0,0,0.04)] transition-all duration-150 focus-within:border-[#e8701a] focus-within:shadow-[0_0_0_3px_rgba(232,112,26,0.1)]"
                                    >
                                        <span class="shrink-0 text-gray-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </span>
                                        <input
                                            id="email"
                                            type="email"
                                            wire:model="email"
                                            placeholder="email@example.com"
                                            required
                                            autocomplete="email"
                                            class="w-full bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none"
                                        />
                                    </div>

                                    @if ($this->hasUnverifiedEmail)
                                        <div class="mt-2">
                                            <p class="text-xs text-amber-600">
                                                {{ __('Your email address is unverified.') }}
                                                <button type="button" wire:click="resendVerificationNotification" class="cursor-pointer font-semibold underline transition-colors hover:text-amber-800" style="color: #e8701a">
                                                    {{ __('Click here to re-send the verification email.') }}
                                                </button>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/60 px-6 py-4">
                                <p class="text-xs text-gray-400">{{ __('Last updated') }} · {{ $this->lastUpdated }}</p>
                                <div class="flex gap-2.5">
                                    <button
                                        type="button"
                                        wire:click="cancel"
                                        class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100"
                                    >
                                        {{ __('Cancel') }}
                                    </button>
                                    <button
                                        type="submit"
                                        wire:loading.attr="disabled"
                                        class="flex items-center gap-1.5 rounded-lg px-5 py-2 text-sm font-semibold text-white transition-all active:scale-95 disabled:opacity-60"
                                        style="background: #e8701a"
                                    >
                                        <span wire:loading.remove wire:target="updateProfileInformation">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                        <span wire:loading wire:target="updateProfileInformation">
                                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                            </svg>
                                        </span>
                                        <span wire:loading.remove wire:target="updateProfileInformation">{{ __('Save Changes') }}</span>
                                        <span wire:loading wire:target="updateProfileInformation">{{ __('Saving') }}…</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </x-settings.card>

                    @if ($this->showDeleteUser)
                        <div class="mt-6">
                            <livewire:pages::settings.delete-user-form />
                        </div>
                    @endif

                {{-- ════════════════════════════════════════════════════════
                    SECURITY TAB
                ════════════════════════════════════════════════════════ --}}
                @elseif ($activeTab === 'security')
                    <x-settings.card wire:key="security-card">
                        {{-- Header --}}
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h2 class="text-sm font-semibold text-gray-900">{{ __('Security Settings') }}</h2>
                            <p class="mt-0.5 text-xs text-gray-400">{{ __('Manage your password and account security.') }}</p>
                        </div>

                        {{-- Password form --}}
                        <form wire:submit="updatePassword">
                            <div class="space-y-4 px-6 py-5">
                                {{-- Current Password --}}
                                <div class="flex flex-col gap-1.5">
                                    <label for="currentPassword" class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        {{ __('Current Password') }}
                                    </label>
                                    <div
class="flex items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 shadow-[0_1px_2px_rgba(0,0,0,0.04)] transition-all duration-150 focus-within:border-[#e8701a] focus-within:shadow-[0_0_0_3px_rgba(232,112,26,0.1)]"
                                    >
                                        <span class="shrink-0 text-gray-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </span>
                                        <input
                                            id="currentPassword"
                                            type="password"
                                            wire:model="current_password"
                                            placeholder="••••••••"
                                            required
                                            autocomplete="current-password"
                                            class="w-full bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none"
                                        />
                                    </div>
                                    @error('current_password')
                                        <p class="text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- New Password --}}
                                <div class="flex flex-col gap-1.5">
                                    <label for="newPassword" class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        {{ __('New Password') }}
                                    </label>
                                    <div
class="flex items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 shadow-[0_1px_2px_rgba(0,0,0,0.04)] transition-all duration-150 focus-within:border-[#e8701a] focus-within:shadow-[0_0_0_3px_rgba(232,112,26,0.1)]"
                                    >
                                        <span class="shrink-0 text-gray-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </span>
                                        <input
                                            id="newPassword"
                                            type="password"
                                            wire:model="password"
                                            placeholder="••••••••"
                                            required
                                            autocomplete="new-password"
                                            class="w-full bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none"
                                        />
                                    </div>
                                    @error('password')
                                        <p class="text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Confirm Password --}}
                                <div class="flex flex-col gap-1.5">
                                    <label for="confirmPassword" class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        {{ __('Confirm New Password') }}
                                    </label>
                                    <div
class="flex items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 shadow-[0_1px_2px_rgba(0,0,0,0.04)] transition-all duration-150 focus-within:border-[#e8701a] focus-within:shadow-[0_0_0_3px_rgba(232,112,26,0.1)]"
                                    >
                                        <span class="shrink-0 text-gray-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </span>
                                        <input
                                            id="confirmPassword"
                                            type="password"
                                            wire:model="password_confirmation"
                                            placeholder="••••••••"
                                            required
                                            autocomplete="new-password"
                                            class="w-full bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none"
                                        />
                                    </div>
                                    @error('password_confirmation')
                                        <p class="text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            {{-- Footer --}}
                            <div class="flex justify-end border-t border-gray-100 bg-gray-50/60 px-6 py-4">
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="flex items-center gap-1.5 rounded-lg px-5 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-60"
                                    style="background: #e8701a"
                                >
                                    <span wire:loading.remove wire:target="updatePassword">{{ __('Update Password') }}</span>
                                    <span wire:loading wire:target="updatePassword">{{ __('Updating') }}…</span>
                                </button>
                            </div>
                        </form>
                    </x-settings.card>
                @endif

            </div>
        </div>
    </div>
</section>
