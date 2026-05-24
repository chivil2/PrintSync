<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Dashboard</h1>
                    <p class="text-white/80">Hello, {{ ucfirst(auth()->user()->first_name) }} {{ ucfirst(auth()->user()->last_name) }}</p>
                </div>
                <div class="text-right" x-data="{ time: '', date: '' }" x-init="time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); date = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }); setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }, 1000)">
                    <div class="text-white text-4xl font-mono font-bold" x-text="time"></div>
                    <div class="text-white/70 text-xs font-mono" x-text="date"></div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm w-[calc(33.333%-1rem)] flex flex-col">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Jobs Overview</div>
                <div class="mt-4 flex items-center gap-3">
                    <span class="text-3xl font-bold text-blue-600">{{ $jobsCount }}</span>
                    <span class="text-sm text-zinc-500">Total Jobs</span>
                </div>
                <hr class="my-4 border-zinc-100">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Recent Jobs</div>
                <div class="flex-1 overflow-y-auto max-h-56 space-y-2">
                    @forelse($recentJobs as $job)
                        <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium text-zinc-800">{{ $job->name }}</div>
                                <div class="mt-0.5 flex items-center gap-2 text-xs text-zinc-400">
                                    <span>{{ $job->customer?->first_name }} {{ $job->customer?->last_name }}</span>
                                    @if($job->employee)
                                        <span class="text-zinc-300">|</span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700">Cc: {{ $job->employee?->first_name }} {{ $job->employee?->last_name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-2 flex-shrink-0 text-right">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ match($job->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-zinc-100 text-zinc-600',
                                } }}">{{ str_replace('_', ' ', $job->status) }}</span>
                                <div class="mt-0.5 text-xs text-zinc-400">{{ $job->created_at->format('M d') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-sm text-zinc-400">No jobs yet</div>
                    @endforelse
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm w-[calc(33.333%-1rem)] flex flex-col">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Customers</div>
                <div class="mt-4 flex items-center gap-3">
                    <span class="text-3xl font-bold text-green-600">{{ $customersCount }}</span>
                    <span class="text-sm text-zinc-500">Registered</span>
                </div>
                <hr class="my-4 border-zinc-100">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Recent Signups</div>
                <div class="flex-1 overflow-y-auto max-h-56 space-y-2">
                    @forelse($recentCustomers as $customer)
                        <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium text-zinc-800">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                                <div class="text-xs text-zinc-400">{{ $customer->email }}</div>
                            </div>
                            <div class="ml-2 flex-shrink-0 text-xs text-zinc-400">
                                {{ $customer->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-sm text-zinc-400">No customers yet</div>
                    @endforelse
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm w-[calc(33.333%-1rem)]">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 3</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm w-[calc(33.333%-1rem)]">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 4</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm w-[calc(33.333%-1rem)]">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 5</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm w-[calc(33.333%-1rem)]">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 6</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app.owner>
