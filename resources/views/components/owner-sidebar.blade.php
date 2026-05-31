@php
    $employees = \App\Models\User::role('employee')->where('employee_status', 'active')->latest()->take(5)->get();
@endphp

<aside class="sticky top-0 w-[280px] bg-white border-r border-slate-200 flex flex-col h-screen shadow-lg overflow-y-auto" id="sidebar">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center justify-start px-4 py-1 mt-5">
            <a href="{{ route('owner.dashboard') }}" id="owner-sidebar-logo" class="block overflow-hidden w-[270px] h-[100px]" style="background-image: url('{{ asset('images/logo.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: center;" aria-label="PrintSync">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-2 px-2 mt-2">
            <a href="{{ route('owner.dashboard') }}" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg transition-all {{ request()->routeIs('owner.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-900 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                    <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                </svg>
                <span class="font-medium text-sm whitespace-nowrap">Dashboard</span>
            </a>

            <a href="{{ route('owner.quotes') }}" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg transition-all {{ request()->routeIs('owner.quotes') ? 'bg-blue-600 text-white' : 'text-slate-900 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                <span class="font-medium text-sm whitespace-nowrap">Reports</span>
            </a>

            <a href="{{ route('owner.inventory.index') }}" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg transition-all {{ request()->routeIs('owner.inventory.*') ? 'bg-blue-600 text-white' : 'text-slate-900 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="font-medium text-sm whitespace-nowrap">Inventory</span>
            </a>

            <a href="{{ route('owner.services.index') }}" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg transition-all {{ request()->routeIs('owner.services.*') ? 'bg-blue-600 text-white' : 'text-slate-900 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                <span class="font-medium text-sm whitespace-nowrap">Services</span>
            </a>

            <a href="{{ route('owner.jobs') }}" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg transition-all {{ request()->routeIs('owner.jobs*') ? 'bg-blue-600 text-white' : 'text-slate-900 hover:bg-slate-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="font-medium text-sm whitespace-nowrap">Orders</span>
            </a>

            <!-- Employee Management Section -->
            <div x-data="{ employeesExpanded: localStorage.getItem('ownerSidebarEmployeesExpanded') === 'true' }" x-init="$watch('employeesExpanded', value => localStorage.setItem('ownerSidebarEmployeesExpanded', value))">
                <button @click="employeesExpanded = !employeesExpanded" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg text-slate-900 hover:bg-slate-100 transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="font-medium text-sm">Employees</span>
                    <svg x-show="!employeesExpanded" class="w-4 h-4 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M9 5l7 7-7 7"/>
                    </svg>
                    <svg x-show="employeesExpanded" class="w-4 h-4 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="employeesExpanded" x-transition class="mt-2 space-y-1">
                    <a href="{{ route('owner.employees') }}" class="flex items-center w-full justify-start gap-2 px-3 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-100 transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>All Employees</span>
                    </a>
                    <a href="{{ route('owner.employees.create') }}" class="flex items-center w-full justify-start gap-2 px-3 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-100 transition-all">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Add Employee</span>
                    </a>

                    @if($employees->count() > 0)
                        <div class="px-3 py-2">
                            <div class="text-xs font-medium text-slate-400 mb-2">Active Employees</div>
                            @foreach($employees as $employee)
                                <a href="{{ route('owner.employees.edit', $employee) }}" class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-100 transition-all">
                                    @if($employee->profile_photo_path)
                                        <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="{{ $employee->first_name }} {{ $employee->last_name }}" class="w-6 h-6 rounded-full object-cover">
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-semibold">
                                            {{ $employee->initials() }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-medium text-slate-900 truncate">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                        <div class="text-xs text-slate-500 truncate">{{ $employee->specialization ?? 'Employee' }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </nav>

        <!-- Logout -->
        <div class="border-t border-slate-200 p-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full justify-start gap-2 px-3 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span class="font-medium text-sm">Log out</span>
                </button>
            </form>
        </div>
    </div>
</aside>
