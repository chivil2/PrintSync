<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="jobsData()" x-init="initJobs()">
        @if(session('success'))
            <x-printsync-toast :message="session('success')" />
        @endif

        <!-- Banner -->
        <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden mb-8">
            <div class="welcome-dots"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Jobs</h1>
                    <p class="text-orange-100">Manage assignments, track progress, and oversee all active print jobs.</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ $jobs->total() }}</div>
                    <div class="text-orange-100 text-sm">Total Jobs</div>
                </div>
            </div>
        </div>

        <!-- Section 1: Pending Quote Approval -->
        <div class="bg-amber-50 rounded-lg p-5 sm:p-6 shadow-sm border border-amber-200 mb-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="text-xs font-semibold text-amber-800 uppercase tracking-wider">Pending Quote Approval</h3>
                        <p class="text-sm text-amber-600">These orders are waiting for customer approval</p>
                    </div>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">{{ $pendingQuoteJobs->count() }} orders awaiting approval</span>
            </div>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach($pendingQuoteJobs as $job)
                    <div class="rounded-lg border border-amber-200 bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900 truncate">{{ $job->customer->first_name ?? 'N/A' }} {{ $job->customer->last_name ?? '' }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ ucfirst(str_replace('_', ' ', $job->service_type)) }} · {{ $job->name }}</p>
                                <p class="text-xs text-amber-600 mt-1">Due {{ $job->deadline ? $job->deadline->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            @php
                                $quoteStatusColors = [
                                    'draft' => 'bg-slate-100 text-slate-700',
                                    'sent' => 'bg-blue-100 text-blue-700',
                                    'accepted' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                                $quoteStatusColor = $quoteStatusColors[$job->quote->status ?? 'draft'] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-medium {{ $quoteStatusColor }}">{{ ucfirst($job->quote->status ?? 'draft') }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <div class="text-xs text-slate-600">
                                @if($job->quote && $job->quote->status === 'draft')
                                    <span class="block text-amber-600">Review quote & send to customer</span>
                                @elseif($job->quote && $job->quote->status === 'sent')
                                    <span class="block text-blue-600">Waiting for customer approval</span>
                                @endif
                            </div>
                            <a href="{{ route('owner.quotes.view', $job->quote) }}" class="flex items-center gap-1 rounded-lg bg-amber-500 px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-amber-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Quote
                            </a>
                        </div>
                    </div>
                @endforeach
                @if($pendingQuoteJobs->isEmpty())
                    <div class="col-span-full text-center py-8 text-amber-700 text-sm">No orders pending quote approval</div>
                @endif
            </div>
        </div>

        <!-- Section 2: Ready to Assign -->
        <div class="bg-white rounded-lg p-5 sm:p-6 shadow-sm border border-slate-200 mb-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ready to Assign</h3>
                        <p class="text-sm text-slate-500">Quote accepted - assign employees to these orders</p>
                    </div>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">{{ $unassignedJobsCount ?? 0 }} unassigned · {{ $readyToAssignJobs->count() }} total</span>
            </div>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach($readyToAssignJobs as $job)
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900 truncate">{{ $job->customer->first_name ?? 'N/A' }} {{ $job->customer->last_name ?? '' }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ ucfirst(str_replace('_', ' ', $job->service_type)) }} · {{ $job->name }}</p>
                                <p class="text-xs text-emerald-600 mt-1">Due {{ $job->deadline ? $job->deadline->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-medium bg-emerald-100 text-emerald-700">Accepted</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <div class="text-xs text-slate-600">
                                <span class="block text-slate-500">Assigned to</span>
                                <span class="font-semibold text-slate-900">{{ $job->employee ? $job->employee->first_name . ' ' . $job->employee->last_name : 'Unassigned' }}</span>
                            </div>
                            @if($job->status !== 'cancelled')
                                <button @click="assignEmployee({{ $job->id }})" class="flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-emerald-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $job->employee ? 'Change Assign' : 'Assign' }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($readyToAssignJobs->isEmpty())
                    <div class="col-span-full text-center py-8 text-slate-500 text-sm">No jobs ready for assignment</div>
                @endif
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <!-- Jobs Overview -->
            <div class="bg-white rounded-lg p-5 sm:p-6 shadow-sm border border-slate-200 flex flex-col">
                <div class="flex flex-col gap-3 mb-4">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-5">Jobs Overview</h3>
                        <div class="flex items-baseline gap-2">
                            <div class="text-3xl font-bold text-blue-500">{{ $jobs->total() }}</div>
                            <div class="text-slate-500 text-sm">Total Jobs</div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="jobStatusFilter = 'all'" :class="jobStatusFilter === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1 text-xs font-medium transition">All</button>
                        <button @click="jobStatusFilter = 'in_progress'" :class="jobStatusFilter === 'in_progress' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1 text-xs font-medium transition">In Progress</button>
                        <button @click="jobStatusFilter = 'pending'" :class="jobStatusFilter === 'pending' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1 text-xs font-medium transition">Pending</button>
                        <button @click="jobStatusFilter = 'completed'" :class="jobStatusFilter === 'completed' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1 text-xs font-medium transition">Completed</button>
                    </div>
                </div>
                <div class="space-y-3 overflow-y-auto max-h-[380px] pr-2 -mr-2">
                    @foreach($jobs->items() as $job)
                        <div x-show="jobStatusFilter === 'all' || '{{ $job->status }}' === jobStatusFilter" class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-slate-900">{{ $job->name }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ $job->customer->first_name ?? 'N/A' }} {{ $job->customer->last_name ?? '' }}<span class="ml-2 text-blue-600">• {{ $job->employee ? $job->employee->first_name . ' ' . $job->employee->last_name : 'Unassigned' }}</span></div>
                            </div>
                            <div class="flex items-center gap-2 ml-2">
                                <a href="{{ route('owner.jobs.show', $job) }}" class="flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-medium text-slate-600 hover:bg-white/70" title="View details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <button @click="confirmDeleteJob({{ $job->id }}, {{ Js::from($job->name) }})" class="flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50" title="Delete job">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $job->status === 'completed' ? 'bg-green-100 text-green-700' : ($job->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : ($job->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Employee Schedule -->
            <div class="bg-white rounded-lg p-5 sm:p-6 shadow-sm border border-slate-200 flex flex-col" x-data="{ scheduleFilter: 'all' }">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-5">Employee Schedule</h3>
                <div class="flex gap-1.5 mb-4">
                    <button @click="scheduleFilter = 'all'" :class="scheduleFilter === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1 text-xs font-medium transition">All</button>
                    <button @click="scheduleFilter = 'technical_staff'" :class="scheduleFilter === 'technical_staff' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1 text-xs font-medium transition">Technician</button>
                    <button @click="scheduleFilter = 'printing_staff'" :class="scheduleFilter === 'printing_staff' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1 text-xs font-medium transition">Printing</button>
                </div>
                <div class="space-y-4 overflow-y-auto max-h-[400px] pr-2 -mr-2 flex-1 h-[380px]">
                    @foreach($employees as $employee)
                        <?php
                            $empJobs = $jobs->filter(function($job) use ($employee) {
                                return $job->employee_id === $employee->id;
                            });
                        ?>
                        <div class="border border-slate-200 rounded-lg p-4"
                             x-show="scheduleFilter === 'all' || scheduleFilter === '{{ $employee->specialization }}'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                        >
                            <div class="flex items-center gap-3 mb-3">
                                @if($employee->profile_photo_path)
                                    <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="{{ $employee->first_name }} {{ $employee->last_name }}" class="w-9 h-9 rounded-lg object-cover">
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-blue-500 flex items-center justify-center text-white font-bold text-sm">{{ $employee->initials() }}</div>
                                @endif
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $employee->specializationLabel() }}</div>
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
                                            <span class="rounded-full px-2 py-0.5 font-medium {{ $job->status === 'completed' ? 'bg-green-100 text-green-700' : ($job->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : ($job->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeDeleteModal()" style="display: none;">
            <div class="w-full max-w-sm mx-4 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-center text-slate-900 mb-1">Delete Job</h3>
                    <p class="text-sm text-center text-slate-500">Are you sure you want to delete <span x-text="deleteJobName" class="font-medium text-slate-700"></span>? This action cannot be undone.</p>
                </div>
                <div class="flex gap-3 px-6 py-4 bg-slate-50">
                    <button @click="closeDeleteModal()" class="flex-1 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">Cancel</button>
                    <button @click="submitDelete()" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Delete</button>
                </div>
            </div>
        </div>

        <!-- Employee Assignment Modal -->
        <div x-show="showEmployeeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeModal()" style="display: none;">
            <div class="w-full max-w-md mx-4 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-900">Select Employee</h3>
                    <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="max-h-[320px] overflow-y-auto p-2 space-y-1">
                    <template x-for="employee in {{ Js::from($employees->values()->toArray()) }}" :key="employee.id">
                        <button @click="confirmAssign(employee.id)" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-[#f5ede3] transition text-left">
                            <template x-if="employee.profile_photo_path">
                                <img :src="'/storage/' + employee.profile_photo_path" :alt="employee.first_name + ' ' + employee.last_name" class="w-9 h-9 rounded-xl object-cover">
                            </template>
                            <template x-if="!employee.profile_photo_path">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-orange-400 flex items-center justify-center text-white font-bold text-sm" x-text="(employee.first_name?.[0] ?? '') + (employee.last_name?.[0] ?? '')"></div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-900 text-sm" x-text="employee.first_name + ' ' + employee.last_name"></div>
                                <div class="text-xs text-slate-500"><span x-text="'ID: ' + (employee.employee_id || 'N/A')"></span> · <span x-text="(employee.assigned_jobs_count || 0) + ' active jobs'"></span></div>
                            </div>
                        </button>
                    </template>
                </div>
                <div class="px-5 py-3 border-t border-slate-100">
                    <button @click="closeModal()" class="w-full px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function jobsData() {
            return {
                jobStatusFilter: 'all',
                showEmployeeModal: false,
                selectedJobId: null,
                showDeleteModal: false,
                deleteJobId: null,
                deleteJobName: '',

                initJobs() {
                    // Initialize any jobs-specific logic
                },

                assignEmployee(jobId) {
                    this.selectedJobId = jobId;
                    this.showEmployeeModal = true;
                },

                confirmAssign(employeeId) {
                    fetch(`/owner/jobs/${this.selectedJobId}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
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
                    })
                    .finally(() => {
                        this.showEmployeeModal = false;
                        this.selectedJobId = null;
                    });
                },

                closeModal() {
                    this.showEmployeeModal = false;
                    this.selectedJobId = null;
                },

                confirmDeleteJob(jobId, jobName) {
                    this.deleteJobId = jobId;
                    this.deleteJobName = jobName;
                    this.showDeleteModal = true;
                },

                submitDelete() {
                    fetch(`/owner/jobs/${this.deleteJobId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.ok || response.redirected) {
                            window.location.reload();
                        } else {
                            alert('Failed to delete job');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to delete job');
                    })
                    .finally(() => {
                        this.closeDeleteModal();
                    });
                },

                closeDeleteModal() {
                    this.showDeleteModal = false;
                    this.deleteJobId = null;
                    this.deleteJobName = '';
                }
            };
        }
    </script>
</x-layouts::app.owner>
