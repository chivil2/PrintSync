<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - {{ config('app.name', 'PrintSync') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <style>
        .scroll-card, .scroll-fade {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .scroll-card.visible, .scroll-fade.visible {
            opacity: 1;
            transform: translateY(0);
        }
        @media (prefers-reduced-motion: reduce) {
            .scroll-card, .scroll-fade { opacity: 1; transform: none; transition: none; }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-zinc-900 bg-white">

    <x-navbar />

    <!-- Hero -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden -mt-20 pt-20">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-950 via-blue-900 to-slate-900"></div>
        <div class="absolute inset-0 bg-gradient-to-tr from-blue-500/10 via-transparent to-orange-500/10"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-gradient-to-r from-blue-500/5 to-orange-500/5 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 text-center">
    
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/10 text-sm text-zinc-400 mb-8 cursor-default">
                <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/50"></span>
                Professional Printing Services
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-white leading-[1.1] mb-6">
                Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-orange-400">Premium</span> Printing Partner
            </h1>

            <p class="text-lg sm:text-xl text-zinc-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Business cards, banners, flyers, and more — all crafted with precision. Create an account to explore our professional printing services.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gradient-to-r from-blue-500 to-orange-500 hover:from-blue-600 hover:to-orange-600 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 cursor-pointer">
                        Get Started
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3.5 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-white font-medium rounded-xl transition-all duration-200 cursor-pointer">
                        Sign In
                    </a>
                @else
                    <a href="{{ route('customer.store') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gradient-to-r from-blue-500 to-orange-500 hover:from-blue-600 hover:to-orange-600 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 cursor-pointer">
                        Go to Store
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-white font-medium rounded-xl transition-all duration-200 cursor-pointer">
                            Logout
                        </button>
                    </form>
                @endguest
            </div>

            <div class="mt-16 grid grid-cols-3 gap-8 md:gap-16 max-w-lg mx-auto">
                <div class="scroll-fade text-center" style="transition-delay: 0ms;">
                    <div class="text-2xl sm:text-3xl font-bold text-white">10K+</div>
                    <div class="text-sm text-zinc-500 mt-1">Prints Delivered</div>
                </div>
                <div class="scroll-fade text-center" style="transition-delay: 100ms;">
                    <div class="text-2xl sm:text-3xl font-bold text-white">500+</div>
                    <div class="text-sm text-zinc-500 mt-1">Happy Clients</div>
                </div>
                <div class="scroll-fade text-center" style="transition-delay: 200ms;">
                    <div class="text-2xl sm:text-3xl font-bold text-white">24h</div>
                    <div class="text-sm text-zinc-500 mt-1">Fast Turnaround</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="relative py-24 sm:py-32 px-4 sm:px-6 lg:px-8 bg-zinc-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-zinc-900 mb-4">Our Printing Services</h2>
                <p class="text-zinc-500 max-w-2xl mx-auto">Everything you need to bring your ideas to life — from business cards to large format printing.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="scroll-card p-6 rounded-2xl bg-white border border-zinc-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                    <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center mb-4 ring-1 ring-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Business Cards</h3>
                    <p class="text-sm text-zinc-500 leading-relaxed">Premium quality business cards with various finishes. Make a lasting impression with professional cards.</p>
                </div>

                <div class="scroll-card p-6 rounded-2xl bg-white border border-zinc-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" style="transition-delay: 100ms;">
                    <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center mb-4 ring-1 ring-orange-100">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Banners & Signage</h3>
                    <p class="text-sm text-zinc-500 leading-relaxed">Eye-catching banners and signage for events, promotions, and storefronts. Durable materials for indoor and outdoor use.</p>
                </div>

                <div class="scroll-card p-6 rounded-2xl bg-white border border-zinc-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" style="transition-delay: 200ms;">
                    <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center mb-4 ring-1 ring-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Flyers & Brochures</h3>
                    <p class="text-sm text-zinc-500 leading-relaxed">Marketing materials that get results. High-quality flyers and brochures to promote your business effectively.</p>
                </div>

                <div class="scroll-card p-6 rounded-2xl bg-white border border-zinc-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" style="transition-delay: 100ms;">
                    <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center mb-4 ring-1 ring-orange-100">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Large Format Printing</h3>
                    <p class="text-sm text-zinc-500 leading-relaxed">Posters, posters, and large format prints. Perfect for trade shows, exhibitions, and retail displays.</p>
                </div>

                <div class="scroll-card p-6 rounded-2xl bg-white border border-zinc-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" style="transition-delay: 200ms;">
                    <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center mb-4 ring-1 ring-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Custom Apparel</h3>
                    <p class="text-sm text-zinc-500 leading-relaxed">Custom printed t-shirts, hoodies, and apparel. Perfect for corporate branding, events, and merchandise.</p>
                </div>

                <div class="scroll-card p-6 rounded-2xl bg-white border border-zinc-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" style="transition-delay: 300ms;">
                    <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center mb-4 ring-1 ring-orange-100">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Fast Turnaround</h3>
                    <p class="text-sm text-zinc-500 leading-relaxed">Quick delivery without compromising quality. Same-day and next-day options available for urgent orders.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="relative py-24 sm:py-32 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-orange-600"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(255,255,255,0.1)_0%,_transparent_60%)] pointer-events-none"></div>
        <div class="relative max-w-3xl mx-auto text-center">
            <h2 class="scroll-fade text-3xl sm:text-4xl font-bold text-white mb-4">Ready to Get Started?</h2>
            <p class="scroll-fade text-lg text-white/70 mb-10 max-w-xl mx-auto" style="transition-delay: 100ms;">Create an account to start exploring our professional printing services today.</p>
            @guest
                <a href="{{ route('register') }}" class="scroll-fade inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-blue-600 font-semibold rounded-xl hover:bg-zinc-50 transition-all duration-200 shadow-lg hover:shadow-xl cursor-pointer" style="transition-delay: 200ms;">
                    Create Account
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            @else
                <a href="{{ route('customer.store') }}" class="scroll-fade inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-blue-600 font-semibold rounded-xl hover:bg-zinc-50 transition-all duration-200 shadow-lg hover:shadow-xl cursor-pointer" style="transition-delay: 200ms;">
                    Explore Services
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-zinc-900 border-t border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="scroll-fade lg:col-span-2" style="transition-delay: 0ms;">
                    <div class="flex items-center gap-2.5 mb-4">
                        <x-printsync-icon size="w-8 h-8" textSize="text-lg" variant="gradient" />
                    </div>
                    <p class="text-sm text-zinc-400 max-w-md leading-relaxed">
                        Your premium printing partner for business cards, banners, flyers, and more. Quality printing with fast turnaround.
                    </p>
                </div>
                <div class="scroll-fade" style="transition-delay: 100ms;">
                    <h4 class="text-sm font-semibold text-white mb-4">Services</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-zinc-400 hover:text-white transition-colors duration-200 cursor-pointer">Business Cards</a></li>
                        <li><a href="#" class="text-sm text-zinc-400 hover:text-white transition-colors duration-200 cursor-pointer">Banners & Signage</a></li>
                        <li><a href="#" class="text-sm text-zinc-400 hover:text-white transition-colors duration-200 cursor-pointer">Flyers & Brochures</a></li>
                        <li><a href="#" class="text-sm text-zinc-400 hover:text-white transition-colors duration-200 cursor-pointer">Custom Apparel</a></li>
                    </ul>
                </div>
                <div class="scroll-fade" style="transition-delay: 200ms;">
                    <h4 class="text-sm font-semibold text-white mb-4">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('register') }}" class="text-sm text-zinc-400 hover:text-white transition-colors duration-200 cursor-pointer">Create Account</a></li>
                        <li><a href="{{ route('login') }}" class="text-sm text-zinc-400 hover:text-white transition-colors duration-200 cursor-pointer">Sign In</a></li>
                    </ul>
                </div>
            </div>
            <div class="scroll-fade mt-12 pt-8 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4" style="transition-delay: 300ms;">
                <p class="text-sm text-zinc-500">&copy; {{ date('Y') }} PrintSync. All rights reserved.</p>
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
            }, { threshold: 0.1 });

            document.querySelectorAll('.scroll-card, .scroll-fade').forEach(card => observer.observe(card));
        });
    </script>
</body>
</html>