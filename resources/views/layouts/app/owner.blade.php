<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-orange-500 z-50"></div>
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('owner.dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Owner')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('owner.dashboard')" :current="request()->routeIs('owner.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-text" :href="route('owner.quotes')" :current="request()->routeIs('owner.quotes')" wire:navigate>
                        {{ __('All Quotes') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('owner.employees')" :current="request()->routeIs('owner.employees')" wire:navigate>
                        {{ __('Employees') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="wrench" :href="route('owner.jobs')" :current="request()->routeIs('owner.jobs')" wire:navigate>
                        {{ __('Service Jobs') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <div class="px-4 pb-4">
                <div class="flex items-center gap-2 mb-3">
                    <flux:avatar
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                    />
                    <div class="flex-1">
                        <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                        <x-role-badge :role="auth()->user()->getRoleNames()->first()" />
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button
                        as="button"
                        type="submit"
                        variant="outline"
                        size="sm"
                        class="w-full"
                    >
                        Log out
                    </flux:button>
                </form>
            </div>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
