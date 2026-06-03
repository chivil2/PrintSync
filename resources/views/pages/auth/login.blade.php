<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">{{ __('Log in to your account') }}</h1>
            <p class="mt-1.5 text-sm text-zinc-600">{{ __('Enter your email and password below to log in') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <div class="[&_[data-flux-label]]:text-black">
                <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />
            </div>

            <button type="submit" class="inline-flex items-center justify-center w-full gap-2 px-5 py-2.5 bg-[#E8743B] hover:bg-[#d66532] text-white text-sm font-semibold rounded-xl transition-colors shadow-sm cursor-pointer" data-test="login-button">
                {{ __('Log in') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </button>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600">
                <span>{{ __('Don\'t have an account?') }}</span>
                <a href="{{ route('register') }}" wire:navigate class="font-semibold text-[#E8743B] hover:text-[#d66532] transition-colors">{{ __('Sign up') }}</a>
            </div>
        @endif
    </div>
</x-layouts::auth>
