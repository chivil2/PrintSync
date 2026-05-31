<x-layouts::app.owner>
    <div class="p-6">
        <x-employee-banner title="Employees" subtitle="Manage your team members" />

        <!-- Search and Filter Bar -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-5 mb-6">
            <form method="GET" action="{{ route('owner.employees') }}" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                        class="w-full rounded-xl border border-zinc-300 pl-10 pr-4 py-2.5 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
                <div class="lg:w-48">
                    <select name="status" class="w-full rounded-xl border border-zinc-300 px-4 py-2.5 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm text-sm">
                        Search
                    </button>
                    @if(request()->has('search') || request()->has('status'))
                        <a href="{{ route('owner.employees') }}" class="px-6 py-2.5 text-zinc-600 font-medium rounded-xl hover:bg-zinc-100 transition-all text-sm">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Stats and Actions Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                <p class="text-zinc-600 font-medium">{{ $employees->total() }} employee{{ $employees->total() !== 1 ? 's' : '' }}</p>
            </div>
            <a href="{{ route('owner.employees.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all shadow-md text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Employee
            </a>
        </div>

        @if($employees->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-50 to-orange-50 flex items-center justify-center mb-6 border border-zinc-200">
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-800 mb-2">No employees found</h3>
                <p class="text-zinc-500 mb-8 max-w-sm">{{ request()->has('search') || request()->has('status') ? 'Try adjusting your search or filters.' : 'Get started by adding your first team member.' }}</p>
                @if(!request()->has('search') && !request()->has('status'))
                    <a href="{{ route('owner.employees.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all shadow-md cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Employee
                    </a>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($employees as $employee)
                    <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-6 hover:shadow-lg transition-all duration-300 cursor-pointer group">
                        <div class="flex items-start justify-between mb-5">
                            <div class="flex items-center gap-4">
                                @if($employee->profile_photo_path)
                                    <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="{{ $employee->first_name }} {{ $employee->last_name }}" class="w-14 h-14 rounded-xl object-cover ring-2 ring-zinc-100">
                                @else
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-lg font-semibold ring-2 ring-zinc-100">
                                        {{ $employee->initials() }}
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-semibold text-zinc-900 text-base">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                                    @if($employee->employee_id)
                                        <p class="text-xs text-zinc-400 font-medium mt-0.5">{{ $employee->employee_id }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $employee->employee_status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-zinc-100 text-zinc-600 ring-1 ring-zinc-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $employee->employee_status === 'active' ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                                {{ ucfirst($employee->employee_status ?? 'inactive') }}
                            </span>
                        </div>

                        <div class="space-y-3 text-sm mb-5">
                            <div class="flex items-center gap-3 text-zinc-600">
                                <svg class="w-4.5 h-4.5 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="truncate">{{ $employee->email }}</span>
                            </div>
                            @if($employee->phone)
                                <div class="flex items-center gap-3 text-zinc-600">
                                    <svg class="w-4.5 h-4.5 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $employee->phone }}</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-3 text-zinc-600">
                                <svg class="w-4.5 h-4.5 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                <span>{{ $employee->specializationLabel() }}</span>
                            </div>
                            @if($employee->hourly_rate)
                                <div class="flex items-center gap-3 text-zinc-600">
                                    <svg class="w-4.5 h-4.5 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium text-zinc-700">₱{{ number_format($employee->hourly_rate, 2) }}/hr</span>
                                </div>
                            @endif
                            @if($employee->hire_date)
                                <div class="flex items-center gap-3 text-zinc-600">
                                    <svg class="w-4.5 h-4.5 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Hired {{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-4 border-t border-zinc-100">
                            <a href="{{ route('owner.employees.edit', $employee) }}" class="flex-1 text-center px-3 py-2.5 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-medium hover:bg-emerald-100 transition-colors cursor-pointer">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('owner.employees.toggle-status', $employee) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-3 py-2.5 {{ $employee->employee_status === 'active' ? 'bg-orange-50 text-orange-700 hover:bg-orange-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} rounded-xl text-sm font-medium transition-colors cursor-pointer">
                                    {{ $employee->employee_status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('owner.employees.destroy', $employee) }}" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2.5 bg-red-50 text-red-700 rounded-xl text-sm font-medium hover:bg-red-100 transition-colors cursor-pointer">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-zinc-500">
                        Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} employees
                    </p>
                    {{ $employees->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts::app.owner>
