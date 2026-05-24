<x-layouts::app.owner>
    <div class="p-6">
        @if(session('success'))
            <x-printsync-toast :message="session('success')" />
        @endif

        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <h1 class="text-2xl font-bold text-white">Jobs</h1>
            <p class="text-white/80">Manage all service jobs</p>
        </div>

        <div class="flex gap-6">
            <!-- Jobs Table -->
            <div class="flex-1">
                @if($jobs->isEmpty())
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-700 mb-2">No jobs yet</h3>
                        <p class="text-zinc-500 max-w-sm">Service jobs will appear here when customers request services.</p>
                    </div>
                @else
                    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-200 bg-zinc-50 text-left">
                                    <th class="px-6 py-3 font-medium text-zinc-600">Job</th>
                                    <th class="px-6 py-3 font-medium text-zinc-600">Customer</th>
                                    <th class="px-6 py-3 font-medium text-zinc-600">Employee</th>
                                    <th class="px-6 py-3 font-medium text-zinc-600">Status</th>
                                    <th class="px-6 py-3 font-medium text-zinc-600">Priority</th>
                                    <th class="px-6 py-3 font-medium text-zinc-600">Deadline</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($jobs as $job)
                                    <tr class="hover:bg-zinc-50 transition-colors {{ $loop->even ? 'bg-zinc-50' : '' }}">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-medium text-zinc-800">{{ $job->name }}</p>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-zinc-100 text-zinc-600 mt-1">
                                                    {{ ucfirst(str_replace('_', ' ', $job->service_type)) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($job->customer)
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-orange-400 flex items-center justify-center text-white text-xs font-semibold shadow-sm">
                                                        {{ $job->customer->initials() }}
                                                    </div>
                                                    <span class="text-zinc-600">{{ $job->customer->first_name }} {{ $job->customer->last_name }}</span>
                                                </div>
                                            @else
                                                <span class="text-zinc-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <form x-data="{ submitting: false }" @submit.prevent="submitting = true; $el.submit()" action="{{ route('owner.jobs.assign', $job) }}" method="POST" class="relative">
                                                @csrf
                                                @method('PATCH')
                                                <select name="employee_id" @change="$el.form.submit()" class="w-full max-w-[200px] text-sm border border-zinc-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-zinc-700 {{ $job->employee_id ? 'bg-green-50 border-green-300' : 'bg-zinc-50' }}">
                                                    <option value="">Unassigned</option>
                                                    @foreach($employees as $employee)
                                                        <option value="{{ $employee->id }}" {{ $job->employee_id === $employee->id ? 'selected' : '' }}>
                                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ match($job->status) {
                                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                                    'completed' => 'bg-green-100 text-green-700',
                                                    'cancelled' => 'bg-red-100 text-red-700',
                                                    default => 'bg-zinc-100 text-zinc-600',
                                                } }}">
                                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ match($job->priority) {
                                                    'low' => 'bg-zinc-100 text-zinc-600',
                                                    'medium' => 'bg-blue-100 text-blue-700',
                                                    'high' => 'bg-orange-100 text-orange-700',
                                                    'urgent' => 'bg-red-100 text-red-700',
                                                    default => 'bg-zinc-100 text-zinc-600',
                                                } }}">
                                                {{ ucfirst($job->priority ?? 'medium') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-zinc-600">
                                            {{ $job->deadline ? $job->deadline->format('M d, Y') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $jobs->links() }}
                @endif
            </div>

            <!-- Employee List -->
            <div class="w-80">
                <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-zinc-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                            <span class="font-medium text-zinc-700">Employees</span>
                            <span class="text-xs text-zinc-500">({{ $employees->count() }})</span>
                        </div>
                    </div>
                    @if($employees->isEmpty())
                        <div class="p-6 text-center text-zinc-500 text-sm">
                            No active employees
                        </div>
                    @else
                        <div class="divide-y divide-zinc-100">
                            @foreach($employees as $employee)
                                <div class="p-4 hover:bg-zinc-50 transition-colors {{ $loop->even ? 'bg-zinc-50' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-400 flex items-center justify-center text-white text-xs font-semibold shadow-sm flex-shrink-0">
                                            {{ $employee->initials() }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-zinc-800 truncate">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                            <p class="text-xs text-zinc-500 truncate">{{ $employee->specialization ?? 'No specialization' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts::app.owner>
