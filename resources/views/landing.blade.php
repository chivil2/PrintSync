<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - {{ config('app.name', 'PrintSync') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <style>
        .scroll-fade {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
        .scroll-fade.visible {
            opacity: 1;
            transform: translateY(0);
        }
        @media (prefers-reduced-motion: reduce) {
            .scroll-fade { opacity: 1; transform: none; transition: none; }
        }
        .hero-bg {
            background-color: #fafafa;
            background-image:
                radial-gradient(ellipse 80% 50% at 20% 0%, rgba(232, 116, 59, 0.10), transparent 60%),
                radial-gradient(ellipse 70% 50% at 85% 30%, rgba(25, 167, 206, 0.10), transparent 60%),
                radial-gradient(circle, rgba(15, 23, 42, 0.06) 1px, transparent 1px);
            background-size: auto, auto, 22px 22px;
        }
        @media (min-width: 768px) {
            .hero-bg { background-size: auto, auto, 26px 26px; }
        }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-zinc-900 bg-zinc-50">

    <x-navbar />

    <!-- Hero -->
    <section class="relative overflow-hidden -mt-20 pt-28 sm:pt-32 pb-12 sm:pb-16">
        <div class="absolute inset-0 hero-bg pointer-events-none"></div>
        <div class="absolute -top-10 -left-16 w-72 h-72 bg-orange-300/25 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-32 -right-16 w-72 h-72 bg-blue-300/25 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6">
            <h1 class="scroll-fade text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-zinc-900 leading-[1.1] max-w-3xl">
                Print, repair, and ship
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#E8743B] to-[#19A7CE]">with confidence</span>
            </h1>

            <p class="scroll-fade mt-4 text-base sm:text-lg text-zinc-600 max-w-xl leading-relaxed" style="transition-delay: 160ms;">
                Business cards, banners, flyers, and tech repair — all in one place. Quality finish, fast turnaround.
            </p>

            <div class="scroll-fade mt-7 flex flex-wrap items-center gap-3" style="transition-delay: 240ms;">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#E8743B] hover:bg-[#d66532] text-white text-sm font-semibold rounded-xl transition-colors shadow-sm cursor-pointer">
                        Get Started
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-zinc-200 hover:border-zinc-300 text-zinc-700 hover:text-zinc-900 text-sm font-medium rounded-xl transition-colors cursor-pointer">
                        Sign In
                    </a>
                @else
                    @if(auth()->user()->hasRole('employee'))
                        <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#E8743B] hover:bg-[#d66532] text-white text-sm font-semibold rounded-xl transition-colors shadow-sm cursor-pointer">
                            Go to Dashboard
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('customer.store') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#E8743B] hover:bg-[#d66532] text-white text-sm font-semibold rounded-xl transition-colors shadow-sm cursor-pointer">
                            Go to Store
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-zinc-200 hover:border-zinc-300 text-zinc-700 hover:text-zinc-900 text-sm font-medium rounded-xl transition-colors cursor-pointer">
                            Logout
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </section>

    <!-- Products -->
    <section class="relative py-12 sm:py-16 px-4 sm:px-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-7 sm:mb-9">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#E8743B] mb-1.5">Our Services</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-zinc-900">What we offer</h2>
                    <p class="text-sm text-zinc-500 mt-1">A quick look at some of our most-requested services.</p>
                </div>
                <a href="{{ auth()->check() && !auth()->user()->hasRole('employee') ? route('customer.store') : route('register') }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#E8743B] hover:text-[#d66532] transition-colors cursor-pointer">
                    View all
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                @forelse($featuredServices as $i => $service)
                    @php
                        $isPrinting = $service['type'] === 'printing';
                        $accent = $isPrinting ? '#E8743B' : '#19A7CE';
                        $accentText = $isPrinting ? 'text-[#E8743B]' : 'text-[#19A7CE]';
                        $accentBg = $isPrinting ? 'bg-orange-50' : 'bg-blue-50';
                        $accentBorder = $isPrinting ? 'border-orange-100' : 'border-blue-100';
                        $badgeBg = $isPrinting ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700';
                        $delay = ($i % 4) * 60;
                        $canOrder = auth()->check() && !auth()->user()->hasRole('employee');
                    @endphp
                    <div class="scroll-fade group bg-white rounded-2xl border border-zinc-200 overflow-hidden hover:border-zinc-300 hover:shadow-md transition-all duration-300"
                         style="transition-delay: {{ $delay }}ms;"
                         @if($canOrder)
                             data-service='@json($service)'
                             data-type="{{ $service['type'] }}"
                             onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: { service: JSON.parse(this.dataset.service), type: this.dataset.type } }))"
                         @endif>

                        <div class="relative h-40 sm:h-44 flex items-center justify-center overflow-hidden {{ $accentBg }}">
                            <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $badgeBg }}">
                                {{ $isPrinting ? 'Printing' : 'Technical' }}
                            </span>
                            @if(!empty($service['image']))
                                <img src="{{ asset('storage/' . $service['image']) }}" alt="{{ $service['name'] }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                            @else
                                @if($isPrinting)
                                    <svg class="w-12 h-12 sm:w-14 sm:h-14 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                @else
                                    <svg class="w-12 h-12 sm:w-14 sm:h-14 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                @endif
                            @endif
                        </div>

                        <div class="p-4">
                            <h3 class="font-semibold text-zinc-900 mb-1 line-clamp-1 text-sm sm:text-base">{{ $service['name'] }}</h3>
                            <p class="text-xs text-zinc-500 mb-3 line-clamp-2 min-h-[2rem]">{{ $service['description'] ?: 'Quality service, fast turnaround.' }}</p>
                            <div class="flex items-center justify-between">
                                <p class="text-base font-bold {{ $accentText }}">₱{{ number_format((float) $service['price'], 2) }}</p>
                                @guest
                                    <a href="{{ route('login') }}" class="text-xs font-semibold text-zinc-500 hover:text-[#E8743B] transition-colors">Sign in →</a>
                                @else
                                    @if($canOrder)
                                        <span class="text-xs font-semibold text-zinc-700 group-hover:{{ $accentText }} transition-colors">Order →</span>
                                    @else
                                        <span class="text-[10px] uppercase tracking-wider font-semibold text-zinc-400">View only</span>
                                    @endif
                                @endguest
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-zinc-500 text-sm">No services available right now. Check back soon!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-zinc-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="h-5 sm:h-6 object-contain">
                    <p class="text-xs text-zinc-500">© {{ date('Y') }} PrintSync. All rights reserved.</p>
                </div>
                <div class="flex items-center gap-5 text-xs text-zinc-500">
                    <a href="#" class="hover:text-zinc-900 transition-colors">Services</a>
                    <a href="{{ route('register') }}" class="hover:text-zinc-900 transition-colors">Sign up</a>
                    <a href="{{ route('login') }}" class="hover:text-zinc-900 transition-colors">Sign in</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.08 });

            document.querySelectorAll('.scroll-fade').forEach(el => observer.observe(el));
        });
    </script>

    @auth
        @if(!auth()->user()->hasRole('employee'))
            <x-service-modal />
        @endif
    @endauth
</body>
</html>
