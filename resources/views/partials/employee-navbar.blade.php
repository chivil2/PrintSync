<nav class="bg-white border-b border-zinc-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-2">
                    <x-printsync-icon size="w-8 h-8" textSize="text-xl" variant="minimal" />
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
