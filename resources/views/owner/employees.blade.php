<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <h1 class="text-2xl font-bold text-white">Employees</h1>
            <p class="text-white/80">Manage your team members</p>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('owner.employees') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                        class="w-full rounded-lg border border-zinc-300 px-4 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div class="md:w-48">
                    <select name="status" class="w-full rounded-lg border border-zinc-300 px-4 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Search
                    </button>
                    @if(request()->has('search') || request()->has('status'))
                        <a href="{{ route('owner.employees') }}" class="px-4 py-2 text-zinc-700 font-medium rounded-lg hover:bg-zinc-100 transition-colors text-sm">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="flex items-center justify-between mb-4">
            <p class="text-zinc-500">{{ $employees->total() }} employee{{ $employees->total() !== 1 ? 's' : '' }}</p>
            <a href="{{ route('owner.employees.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Employee
            </a>
        </div>

        @if($employees->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-zinc-700 mb-2">No employees found</h3>
                <p class="text-zinc-500 mb-8 max-w-sm">{{ request()->has('search') || request()->has('status') ? 'Try adjusting your search or filters.' : 'Get started by adding your first team member.' }}</p>
                @if(!request()->has('search') && !request()->has('status'))
                    <a href="{{ route('owner.employees.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Employee
                    </a>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($employees as $employee)
                    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-orange-400 flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                                    {{ $employee->initials() }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-zinc-900">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                                    @if($employee->employee_id)
                                        <p class="text-xs text-zinc-400">{{ $employee->employee_id }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->employee_status === 'active' ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-600' }}">
                                {{ ucfirst($employee->employee_status ?? 'inactive') }}
                            </span>
                        </div>

                        <div class="space-y-2 text-sm mb-4">
                            <div class="flex items-center gap-2 text-zinc-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $employee->email }}
                            </div>
                            @if($employee->phone)
                                <div class="flex items-center gap-2 text-zinc-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $employee->phone }}
                                </div>
                            @endif
                            <div class="flex items-center gap-2 text-zinc-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                {{ $employee->specializationLabel() }}
                            </div>
                            @if($employee->hourly_rate)
                                <div class="flex items-center gap-2 text-zinc-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    ${{ number_format($employee->hourly_rate, 2) }}/hr
                                </div>
                            @endif
                            @if($employee->hire_date)
                                <div class="flex items-center gap-2 text-zinc-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Hired {{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-4 border-t border-zinc-100">
                            <a href="{{ route('owner.employees.edit', $employee) }}" class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('owner.employees.toggle-status', $employee) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-3 py-2 {{ $employee->employee_status === 'active' ? 'bg-orange-50 text-orange-700 hover:bg-orange-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }} rounded-lg text-sm font-medium transition-colors">
                                    {{ $employee->employee_status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('owner.employees.destroy', $employee) }}" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-sm text-zinc-500">
                        Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} employees
                    </p>
                    {{ $employees->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts::app.owner>
