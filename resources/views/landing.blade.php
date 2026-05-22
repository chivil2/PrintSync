<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - {{ config('app.name', 'PrintSync') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <style>
        @import url('https://fonts.cdnfonts.com/css/neue-haas-grotesk-display-pro');
        body {
            font-family: 'Neue Haas Grotesk Display Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .scroll-card {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .scroll-card.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-white" style="background: linear-gradient(rgba(59, 130, 246, 0.85), rgba(249, 115, 22, 0.85)), url('https://images.unsplash.com/photo-1562564055-71e051d33c19?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-attachment: fixed; backdrop-filter: blur(10px);">
    <!-- Navigation -->
    <x-navbar />

    <!-- Hero Section -->
    <section class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 text-white">
                PrintSync, your <span class="text-6xl sm:text-7xl lg:text-8xl inline-block px-4 py-2 bg-gradient-to-r from-orange-500 via-white to-orange-500 bg-clip-text text-transparent font-black relative" style="text-shadow: 0 0 80px rgba(249, 115, 22, 0.8), 0 0 120px rgba(249, 115, 22, 0.6);">#1</span> printing company!
            </h1>
            <p class="text-lg sm:text-xl text-white/90 max-w-2xl mx-auto mb-10">
                Create an account to start checking out our professional printing services. Business cards, banners, flyers, and more - all at competitive prices with fast turnaround.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-orange-500 hover:from-blue-600 hover:to-orange-600 text-white font-medium rounded-lg transition-colors">
                        Get Started
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-black font-medium rounded-lg transition-colors">
                        Sign In
                    </a>
                @else
                    <a href="{{ route('customer.store') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-orange-500 hover:from-blue-600 hover:to-orange-600 text-white font-medium rounded-lg transition-colors">
                        Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 z-0" style="background: linear-gradient(rgba(23, 37, 84, 0.92), rgba(23, 37, 84, 0.92)), url('https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;"></div>
        <div class="relative z-10 max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-center mb-12 text-white">Our Printing Services</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="scroll-card p-6 rounded-xl border border-white/20 bg-white/50 backdrop-blur-lg hover:bg-white/70 transition-all" style="transition-delay: 0ms;">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Business Cards</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Premium quality business cards with various finishes. Make a lasting impression with professional cards.</p>
                </div>

                <!-- Service 2 -->
                <div class="scroll-card p-6 rounded-xl border border-white/20 bg-white/50 backdrop-blur-lg hover:bg-white/70 transition-all" style="transition-delay: 100ms;">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Banners & Signage</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Eye-catching banners and signage for events, promotions, and storefronts. Durable materials for indoor and outdoor use.</p>
                </div>

                <!-- Service 3 -->
                <div class="scroll-card p-6 rounded-xl border border-white/20 bg-white/50 backdrop-blur-lg hover:bg-white/70 transition-all" style="transition-delay: 200ms;">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Flyers & Brochures</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Marketing materials that get results. High-quality flyers and brochures to promote your business effectively.</p>
                </div>

                <!-- Service 4 -->
                <div class="scroll-card p-6 rounded-xl border border-white/20 bg-white/50 backdrop-blur-lg hover:bg-white/70 transition-all" style="transition-delay: 300ms;">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Large Format Printing</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Posters, posters, and large format prints. Perfect for trade shows, exhibitions, and retail displays.</p>
                </div>

                <!-- Service 5 -->
                <div class="scroll-card p-6 rounded-xl border border-white/20 bg-white/50 backdrop-blur-lg hover:bg-white/70 transition-all" style="transition-delay: 400ms;">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Custom Apparel</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Custom printed t-shirts, hoodies, and apparel. Perfect for corporate branding, events, and merchandise.</p>
                </div>

                <!-- Service 6 -->
                <div class="scroll-card p-6 rounded-xl border border-white/20 bg-white/50 backdrop-blur-lg hover:bg-white/70 transition-all" style="transition-delay: 500ms;">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Fast Turnaround</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">Quick delivery without compromising quality. Same-day and next-day options available for urgent orders.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 z-0" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.9), rgba(249, 115, 22, 0.9)), url('https://images.unsplash.com/photo-1504270997636-07ddfbd48945?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Ready to Check Out Our Services?</h2>
            <p class="text-lg text-blue-100 mb-8">Create an account to start exploring our professional printing services today.</p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-orange-50 transition-colors">
                    Create Account
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            @else
                <a href="{{ route('customer.store') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-orange-50 transition-colors">
                    Explore Services
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 px-4 sm:px-6 lg:px-8 bg-white/70 backdrop-blur-xl border-t border-white/20">
        <div class="max-w-7xl mx-auto text-center text-sm text-zinc-600 dark:text-zinc-400">
            <p>&copy; {{ date('Y') }} PrintSync. All rights reserved.</p>
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

            document.querySelectorAll('.scroll-card').forEach(card => observer.observe(card));
        });
    </script>
</body>
</html>
