<nav class="sticky top-6 mx-auto w-[70%] rounded-2xl shadow-2xl z-[9999] bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-12">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-800">PrintSync</span>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('customer.store') }}" class="text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 px-4 py-2 rounded-lg transition-colors">
                        Go to Store
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-orange-500 transition-colors">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-gradient-to-r from-blue-500 to-orange-500 hover:from-blue-600 hover:to-orange-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
