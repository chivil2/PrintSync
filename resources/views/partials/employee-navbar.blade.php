<nav class="bg-white border-b border-zinc-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-2">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="#E8743B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 7H15M9 11H15M9 15H12" stroke="#19A7CE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-xl font-bold text-zinc-900">PrintSync</span>
                </a>
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('employee.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('employee.dashboard') ? 'text-blue-600' : 'text-zinc-600 hover:text-zinc-900' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('employee.quotes') }}" class="text-sm font-medium {{ request()->routeIs('employee.quotes') ? 'text-blue-600' : 'text-zinc-600 hover:text-zinc-900' }}">
                        My Quotes
                    </a>
                    <a href="{{ route('employee.jobs') }}" class="text-sm font-medium {{ request()->routeIs('employee.jobs') ? 'text-blue-600' : 'text-zinc-600 hover:text-zinc-900' }}">
                        Service Jobs
                    </a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-orange-500 flex items-center justify-center text-white text-sm font-semibold">
                        {{ auth()->user()->initials() }}
                    </div>
                    <span class="hidden md:block text-sm font-medium text-zinc-700">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-zinc-600 hover:text-zinc-900">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
