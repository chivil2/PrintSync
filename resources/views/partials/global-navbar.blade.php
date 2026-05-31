@php
    $navbarConfig = auth()->check() && auth()->user()->can('view_assigned_service_jobs')
        ? config('navbar.employee')
        : config('navbar.customer');
    $logoRoute = isset($navbarConfig['logo']['role_based_route']) && auth()->check()
        ? $navbarConfig['logo']['role_based_route'][auth()->user()->roles->first()->name] ?? $navbarConfig['logo']['route']
        : $navbarConfig['logo']['route'];
    $colors = config('colors');
@endphp

<header class="sticky top-0 z-50 flex items-center justify-between border-b border-[#E5E7EB] bg-white px-6 py-3">
    <div class="flex items-center gap-8">
                <a href="{{ route($logoRoute) }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="w-[160px] h-[80px] object-cover">
                </a>

                <div class="flex items-center gap-1">
                    @foreach ($navbarConfig['links'] as $link)
                        @if (isset($link['permission']) && !auth()->user()->can($link['permission']))
                            @continue
                        @endif

                        @php
                            $route = isset($link['role_based_route']) && auth()->check()
                                ? $link['role_based_route'][auth()->user()->roles->first()->name] ?? $link['route']
                                : $link['route'];
                            $isActive = request()->routeIs($route) || str_starts_with(request()->route()?->getName() ?? '', $route . '.');
                        @endphp

                        <a
                            href="{{ route($route) }}"
                            class="flex items-center gap-2 rounded-full px-4 py-2 text-[14px] font-medium transition-colors {{ $isActive ? 'bg-[#FEF0E7] text-[#F47C3C]' : 'text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#374151]' }}"
                        >
                            <i class="{{ $link['icon'] }} h-4 w-4"></i>
                            <span>{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 rounded-full border border-[#E5E7EB] px-3 py-1.5 transition-colors hover:border-[#D1D5DB] hover:bg-[#F9FAFB]"
                        >
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#FEF0E7]">
                                <span class="text-[12px] font-semibold text-[#F47C3C]">{{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}</span>
                            </div>
                            <span class="hidden text-[14px] font-medium text-[#374151] sm:block">{{ auth()->user()->first_name }}</span>
                            <svg class="h-[14px] w-[14px] text-[#6B7280] transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition
                            class="absolute right-0 mt-2 w-[176px] rounded-xl border border-[#E5E7EB] bg-white py-1.5 shadow-lg z-50"
                        >
                            @foreach ($navbarConfig['user_dropdown']['items'] as $item)
                                @if (isset($item['permission']) && !auth()->user()->can($item['permission']))
                                    @continue
                                @endif
                                <a href="{{ route($item['route']) }}" @click="open = false" class="block px-4 py-2 text-[14px] text-[#4B5563] hover:bg-[#F9FAFB]">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach

                            <div class="my-1.5 border-t border-[#F3F4F6]"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" @click="open = false" class="w-full text-left px-4 py-2 text-[14px] text-[#EF4444] hover:bg-[#FEF2F2]">
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-[14px] font-medium text-[#6B7280] transition-colors hover:text-[#111827]">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="rounded-full bg-[#F47C3C] px-4 py-2 text-[14px] font-medium text-white transition-colors hover:bg-[#E8654A]">
                        Get Started
                    </a>
                @endauth
            </div>
</header>
