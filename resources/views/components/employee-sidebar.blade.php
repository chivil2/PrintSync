<aside class="sticky top-0 w-[280px] bg-white border-r border-slate-200 flex flex-col h-screen shadow-lg overflow-y-auto hidden lg:block" id="sidebar">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center justify-start px-4 py-1 mt-5">
            <a href="{{ route('employee.dashboard') }}" id="employee-sidebar-logo" class="block overflow-hidden w-[270px] h-[100px]" style="background-image: url('{{ asset('images/logo.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: center;" aria-label="PrintSync">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-2 px-2 mt-2">
            <a href="{{ route('employee.dashboard') }}" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg transition-all {{ request()->routeIs('employee.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-900 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                    <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                </svg>
                <span class="font-medium text-sm whitespace-nowrap">Dashboard</span>
            </a>

            <a href="{{ route('employee.jobs') }}" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg transition-all {{ request()->routeIs('employee.jobs') ? 'bg-blue-600 text-white' : 'text-slate-900 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="font-medium text-sm whitespace-nowrap">Jobs</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="px-2 pb-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg text-slate-900 hover:bg-slate-100 transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span class="font-medium text-sm whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
