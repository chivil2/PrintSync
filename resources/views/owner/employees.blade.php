<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <h1 class="text-2xl font-bold text-white">Employees</h1>
            <p class="text-white/80">Manage your team members</p>
        </div>

        @if($employees->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-zinc-700 mb-2">No employees yet</h3>
                <p class="text-zinc-500 mb-8 max-w-sm">Get started by adding your first team member.</p>
                <a href="{{ route('owner.employees.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Employee
                </a>
            </div>
        @else
            <div class="flex items-center justify-between mb-4">
                <p class="text-zinc-500">{{ count($employees) }} employee{{ count($employees) !== 1 ? 's' : '' }}</p>
                <a href="{{ route('owner.employees.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Employee
                </a>
            </div>
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 bg-zinc-50 text-left">
                            <th class="px-6 py-3 font-medium text-zinc-600">Name</th>
                            <th class="px-6 py-3 font-medium text-zinc-600">Email</th>
                            <th class="px-6 py-3 font-medium text-zinc-600">Specialization</th>
                            <th class="px-6 py-3 font-medium text-zinc-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($employees as $employee)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-orange-400 flex items-center justify-center text-white text-xs font-semibold shadow-sm">
                                            {{ $employee->initials() }}
                                        </div>
                                        <span class="font-medium text-zinc-800">{{ $employee->first_name }} {{ $employee->last_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-zinc-600">{{ $employee->email }}</td>
                                <td class="px-6 py-4 text-zinc-600">{{ $employee->specialization ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->employee_status === 'active' ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-600' }}">
                                        {{ ucfirst($employee->employee_status ?? 'inactive') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts::app.owner>
