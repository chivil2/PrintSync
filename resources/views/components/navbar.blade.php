<nav class="sticky top-4 mx-auto w-[calc(100%-2rem)] sm:w-[90%] lg:w-[70%] max-w-5xl rounded-2xl z-[9999] bg-white border border-zinc-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 cursor-pointer">
                <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="w-[160px] h-[80px] object-cover">
            </a>
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->hasRole('employee'))
                        <a href="{{ route('employee.dashboard') }}" class="text-sm font-semibold text-white bg-[#E8743B] hover:bg-[#d66532] px-4 py-2 rounded-lg transition-colors duration-200 cursor-pointer shadow-sm">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('customer.store') }}" class="text-sm font-semibold text-white bg-[#E8743B] hover:bg-[#d66532] px-4 py-2 rounded-lg transition-colors duration-200 cursor-pointer shadow-sm">
                            Go to Store
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 transition-colors duration-200 px-3 py-2 rounded-lg hover:bg-zinc-100 cursor-pointer">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 transition-colors duration-200 px-3 py-2 rounded-lg hover:bg-zinc-100 cursor-pointer">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-[#E8743B] hover:bg-[#d66532] px-4 py-2 rounded-lg transition-colors duration-200 cursor-pointer shadow-sm">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
