<nav class="sticky top-4 mx-auto w-[calc(100%-2rem)] sm:w-[90%] lg:w-[70%] max-w-5xl rounded-2xl z-[9999] bg-blue-950/60 backdrop-blur-xl border border-blue-800/30 shadow-lg shadow-blue-950/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 cursor-pointer">
                <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="w-[160px] h-[80px] object-cover">
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('customer.store') }}" class="text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-orange-500 hover:from-blue-600 hover:to-orange-600 px-4 py-2 rounded-lg transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md">
                        Go to Store
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-blue-200 hover:text-white transition-colors duration-200 px-3 py-2 rounded-lg hover:bg-white/10 cursor-pointer">
                            Logout
                        </button>
                    </form>
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
