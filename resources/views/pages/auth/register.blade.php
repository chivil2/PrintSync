<x-layouts::auth :title="$isOwner ?? false ? __('Owner Register') : __('Register')">
    <div class="flex flex-col gap-6">
        <div class="text-center">
            <span class="inline-block px-2.5 py-0.5 mb-2 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $isOwner ?? false ? 'bg-red-100 text-red-700' : 'bg-zinc-100 text-zinc-700' }}">
                {{ $isOwner ?? false ? __('Owner Registration') : __('User Registration') }}
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">{{ $isOwner ?? false ? __('Create an owner account') : __('Create an account') }}</h1>
            <p class="mt-1.5 text-sm text-zinc-600">{{ __('Enter your details below to create your account') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ $isOwner ?? false ? route('owner.register') : route('register') }}" class="flex flex-col gap-5">
            @csrf
            <!-- First Name -->
            <flux:input
                name="first_name"
                :label="__('First name')"
                :value="old('first_name')"
                type="text"
                required
                autofocus
                autocomplete="given-name"
                :placeholder="__('First name')"
            />

            <!-- Last Name -->
            <flux:input
                name="last_name"
                :label="__('Last name')"
                :value="old('last_name')"
                type="text"
                required
                autocomplete="family-name"
                :placeholder="__('Last name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <button type="submit" class="inline-flex items-center justify-center w-full gap-2 px-5 py-2.5 bg-[#E8743B] hover:bg-[#d66532] text-white text-sm font-semibold rounded-xl transition-colors shadow-sm cursor-pointer" data-test="register-user-button">
                {{ __('Create account') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-[#E8743B] hover:text-[#d66532] transition-colors">{{ __('Log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
