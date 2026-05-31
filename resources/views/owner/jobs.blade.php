<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="jobsData()" x-init="initJobs()">
        @if(session('success'))
            <x-printsync-toast :message="session('success')" />
        @endif

        <!-- Order Status Overview -->
        <div class="bg-white/95 rounded-[24px] sm:rounded-[28px] p-5 sm:p-6 shadow-xl shadow-blue-950/10 border border-white/70 mb-6">
            <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider mb-4">Order Status Overview</h3>
            <div class="flex flex-col items-center gap-6 lg:flex-row lg:items-start">
                @php
                    $totalJobs = array_sum($jobsCountByStatus);
                    $completed = $jobsCountByStatus['completed'] ?? 0;
                    $inProgress = $jobsCountByStatus['in_progress'] ?? 0;
                    $pending = $jobsCountByStatus['pending'] ?? 0;
                    $overdue = $jobsCountByStatus['overdue'] ?? 0;
                    
                    $circumference = 2 * M_PI * 120; // 754
                    $completedOffset = 0;
                    $inProgressOffset = -($completed / $totalJobs) * $circumference;
                    $pendingOffset = -(($completed + $inProgress) / $totalJobs) * $circumference;
                    $overdueOffset = -(($completed + $inProgress + $pending) / $totalJobs) * $circumference;
                    
                    $completedDash = ($completed / $totalJobs) * $circumference;
                    $inProgressDash = ($inProgress / $totalJobs) * $circumference;
                    $pendingDash = ($pending / $totalJobs) * $circumference;
                    $overdueDash = ($overdue / $totalJobs) * $circumference;
                @endphp
                <div class="relative w-[240px] h-[240px] shrink-0">
                    <svg viewBox="0 0 240 240" class="w-full h-full">
                        <!-- Pie chart segments -->
                        <circle cx="120" cy="120" r="120" fill="none" stroke="#e2e8f0" stroke-width="40"/>
                        @if($totalJobs > 0)
                            <circle cx="120" cy="120" r="120" fill="none" stroke="#10b981" stroke-width="40" stroke-dasharray="{{ $completedDash }} {{ $circumference - $completedDash }}" stroke-dashoffset="{{ $completedOffset }}" transform="rotate(90 120 120)"/>
                            <circle cx="120" cy="120" r="120" fill="none" stroke="#3b82f6" stroke-width="40" stroke-dasharray="{{ $inProgressDash }} {{ $circumference - $inProgressDash }}" stroke-dashoffset="{{ $inProgressOffset }}" transform="rotate(90 120 120)"/>
                            <circle cx="120" cy="120" r="120" fill="none" stroke="#f97316" stroke-width="40" stroke-dasharray="{{ $pendingDash }} {{ $circumference - $pendingDash }}" stroke-dashoffset="{{ $pendingOffset }}" transform="rotate(90 120 120)"/>
                            <circle cx="120" cy="120" r="120" fill="none" stroke="#ef4444" stroke-width="40" stroke-dasharray="{{ $overdueDash }} {{ $circumference - $overdueDash }}" stroke-dashoffset="{{ $overdueOffset }}" transform="rotate(90 120 120)"/>
                        @endif
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-4xl font-bold text-slate-900">{{ $completionPercent }}%</div>
                        <div class="text-sm text-slate-500">Completion</div>
                    </div>
                </div>
                <div class="grid w-full grid-cols-1 gap-3 text-sm sm:grid-cols-2 lg:flex-1">
                    <div class="flex items-center justify-between p-4 bg-[#f5ede3] rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-green-500"></span>
                            <span class="text-slate-700">Completed</span>
                        </div>
                        <span class="font-bold text-xl text-slate-900">{{ $completed }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-[#f5ede3] rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-blue-500"></span>
                            <span class="text-slate-700">In Progress</span>
                        </div>
                        <span class="font-bold text-xl text-slate-900">{{ $inProgress }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-[#f5ede3] rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-orange-500"></span>
                            <span class="text-slate-700">Pending</span>
                        </div>
                        <span class="font-bold text-xl text-slate-900">{{ $pending }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-[#f5ede3] rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-red-500"></span>
                            <span class="text-slate-700">Overdue</span>
                        </div>
                        <span class="font-bold text-xl text-slate-900">{{ $overdue }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Assignment Queue -->
        <div class="bg-white/95 rounded-[24px] sm:rounded-[28px] p-5 sm:p-6 shadow-xl shadow-blue-950/10 border border-white/70 mb-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider">Order Assignment Queue</h3>
                    <p class="text-sm text-slate-500">Assign or reassign employees to active orders</p>
                </div>
                <span class="rounded-full bg-[#f5ede3] px-3 py-1 text-xs font-medium text-slate-700">{{ $unassignedJobsCount ?? 0 }} unassigned active orders</span>
            </div>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach($unassignedJobs as $job)
                    <div class="rounded-2xl border border-slate-100 bg-[#f5ede3] p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900 truncate">{{ $job->customer->first_name ?? 'N/A' }} {{ $job->customer->last_name ?? '' }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ ucfirst(str_replace('_', ' ', $job->service_type)) }} · {{ $job->name }} · Due {{ $job->deadline ? $job->deadline->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-medium {{ $job->status === 'pending' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <div class="text-xs text-slate-600">
                                <span class="block text-slate-500">Assigned to</span>
                                <span class="font-semibold text-slate-900">{{ $job->employee ? $job->employee->first_name . ' ' . $job->employee->last_name : 'Unassigned' }}</span>
                            </div>
                            <button @click="assignEmployee({{ $job->id }})" class="flex items-center gap-1 rounded-lg bg-white px-3 py-2 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Assign
                            </button>
                        </div>
                    </div>
                @endforeach
                @if($unassignedJobs->isEmpty())
                    <div class="col-span-full text-center py-8 text-slate-500 text-sm">No unassigned orders</div>
                @endif
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <!-- Jobs Overview -->
            <div class="bg-white/95 rounded-[24px] sm:rounded-[28px] p-5 sm:p-6 shadow-xl shadow-blue-950/10 border border-white/70 flex flex-col">
                <div class="flex flex-col gap-3 mb-4">
                    <div>
                        <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Jobs Overview</h3>
                        <div class="flex items-baseline gap-2">
                            <div class="text-3xl font-bold text-blue-500">{{ $jobs->total() }}</div>
                            <div class="text-slate-500 text-sm">Total Jobs</div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="jobStatusFilter = 'all'" :class="jobStatusFilter === 'all' ? 'bg-slate-900 text-white' : 'bg-[#f5ede3] text-slate-600 hover:bg-slate-100'" class="rounded-full px-3 py-1 text-xs font-medium transition">All</button>
                        <button @click="jobStatusFilter = 'in_progress'" :class="jobStatusFilter === 'in_progress' ? 'bg-slate-900 text-white' : 'bg-[#f5ede3] text-slate-600 hover:bg-slate-100'" class="rounded-full px-3 py-1 text-xs font-medium transition">In Progress</button>
                        <button @click="jobStatusFilter = 'pending'" :class="jobStatusFilter === 'pending' ? 'bg-slate-900 text-white' : 'bg-[#f5ede3] text-slate-600 hover:bg-slate-100'" class="rounded-full px-3 py-1 text-xs font-medium transition">Pending</button>
                        <button @click="jobStatusFilter = 'completed'" :class="jobStatusFilter === 'completed' ? 'bg-slate-900 text-white' : 'bg-[#f5ede3] text-slate-600 hover:bg-slate-100'" class="rounded-full px-3 py-1 text-xs font-medium transition">Completed</button>
                    </div>
                </div>
                <div class="space-y-3 overflow-y-auto max-h-[380px] pr-2 -mr-2">
                    @foreach($jobs->items() as $job)
                        <div x-show="jobStatusFilter === 'all' || '{{ $job->status }}' === jobStatusFilter" class="flex justify-between items-center p-3 bg-[#f5ede3] rounded-xl">
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-slate-900">{{ $job->name }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ $job->customer->first_name ?? 'N/A' }} {{ $job->customer->last_name ?? '' }}<span class="ml-2 text-blue-600">• {{ $job->employee ? $job->employee->first_name . ' ' . $job->employee->last_name : 'Unassigned' }}</span></div>
                            </div>
                            <div class="flex items-center gap-2 ml-2">
                                <a href="{{ route('owner.jobs.show', $job) }}" class="flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-medium text-slate-600 hover:bg-white/70" title="View details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $job->status === 'completed' ? 'bg-green-100 text-green-700' : ($job->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Employee Schedule -->
            <div class="bg-white/95 rounded-[24px] sm:rounded-[28px] p-5 sm:p-6 shadow-xl shadow-blue-950/10 border border-white/70 flex flex-col">
                <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider mb-4">Employee Schedule</h3>
                <div class="space-y-4 overflow-y-auto max-h-[400px] pr-2 -mr-2">
                    @foreach($employees as $employee)
                        <?php
                            $empJobs = $jobs->filter(function($job) use ($employee) {
                                return $job->employee_id === $employee->id;
                            });
                        ?>
                        <div class="border border-slate-100 rounded-2xl p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-orange-400 flex items-center justify-center text-white font-bold text-sm">{{ $employee->initials() }}</div>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $employee->specialization ?? 'Employee' }}</div>
                                </div>
                            </div>
                            @if($empJobs->isEmpty())
                                <div class="ml-12 text-xs text-slate-500 italic">No assignments</div>
                            @else
                                @foreach($empJobs as $job)
                                    <div class="ml-12 text-sm p-2.5 bg-[#f5ede3] rounded-lg mb-2 last:mb-0">
                                        <div class="font-medium text-slate-900">{{ $job->name }}</div>
                                        <div class="flex justify-between text-xs text-slate-500 mt-1">
                                            <span>{{ $job->customer->first_name ?? 'N/A' }} {{ $job->customer->last_name ?? '' }}</span>
                                            <span class="rounded-full px-2 py-0.5 font-medium {{ $job->status === 'completed' ? 'bg-green-100 text-green-700' : ($job->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function jobsData() {
            return {
                jobStatusFilter: 'all',
                
                initJobs() {
                    // Initialize any jobs-specific logic
                },
                
                assignEmployee(jobId) {
                    const employeeId = prompt('Enter employee ID to assign:');
                    if (employeeId) {
                        fetch(`/owner/jobs/${jobId}/assign`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ employee_id: employeeId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Failed to assign employee');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to assign employee');
                        });
                    }
                }
            };
        }
    </script>
</x-layouts::app.owner>
