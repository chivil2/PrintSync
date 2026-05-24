<nav class="sticky top-4 mx-auto w-[calc(100%-2rem)] sm:w-[90%] lg:w-[70%] max-w-5xl rounded-2xl z-[9999] bg-blue-950/60 backdrop-blur-xl border border-blue-800/30 shadow-lg shadow-blue-950/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 cursor-pointer">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-orange-500 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">PrintSync</span>
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('customer.store') }}" class="text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-orange-500 hover:from-blue-600 hover:to-orange-600 px-4 py-2 rounded-lg transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md">
                        Go to Store
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-blue-200 hover:text-white transition-colors duration-200 px-3 py-2 rounded-lg hover:bg-white/10 cursor-pointer">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-orange-500 hover:from-blue-600 hover:to-orange-600 px-4 py-2 rounded-lg transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
