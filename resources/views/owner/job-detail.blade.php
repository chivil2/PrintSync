<x-layouts::app.owner>
    <div class="p-6">
        @if(session('success'))
            <x-printsync-toast :message="session('success')" />
        @endif

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900">Job Details</h1>
                <p class="text-zinc-600">{{ $job->name }}</p>
            </div>
            <a href="{{ route('owner.jobs') }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Jobs
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Job Information -->
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm">
                    <div class="p-6 border-b border-zinc-200">
                        <h2 class="text-lg font-semibold text-zinc-900">Service Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-zinc-500 text-sm">Service:</span>
                                <p class="text-zinc-900 font-medium">{{ $job->name }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 text-sm">Type:</span>
                                <p class="text-zinc-900 font-medium">{{ ucfirst($job->type) }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500 text-sm">Status:</span>
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
                            </div>
                            <div>
                                <span class="text-zinc-500 text-sm">Priority:</span>
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
                            </div>
                            <div>
                                <span class="text-zinc-500 text-sm">Requested:</span>
                                <p class="text-zinc-900 font-medium">{{ $job->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @if($job->deadline)
                                <div>
                                    <span class="text-zinc-500 text-sm">Deadline:</span>
                                    <p class="text-zinc-900 font-medium">{{ $job->deadline->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>
                        @if($job->description)
                            <div class="mt-4">
                                <span class="text-zinc-500 text-sm">Description:</span>
                                <p class="text-zinc-900 mt-1">{{ $job->description }}</p>
                            </div>
                        @endif
                        @if($job->notes)
                            <div class="mt-4 p-3 bg-zinc-50 rounded-lg">
                                <span class="text-sm text-zinc-500">Notes:</span>
                                <p class="text-sm text-zinc-700 mt-1">{{ $job->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quote Information -->
                @if($job->quote)
                    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="p-6 border-b border-zinc-200">
                            <h2 class="text-lg font-semibold text-zinc-900">Quote / Receipt</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        @php
                                            $quoteStatusColors = [
                                                'draft' => 'bg-zinc-100 text-zinc-800',
                                                'sent' => 'bg-blue-100 text-blue-800',
                                                'accepted' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                            $quoteStatusLabels = [
                                                'draft' => 'Pending Review',
                                                'sent' => 'Awaiting Approval',
                                                'accepted' => 'Approved',
                                                'rejected' => 'Rejected',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $quoteStatusColors[$job->quote->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                            {{ $quoteStatusLabels[$job->quote->status] ?? ucfirst($job->quote->status) }}
                                        </span>
                                        <span class="text-sm text-zinc-600">{{ $job->quote->quote_number }}</span>
                                    </div>
                                    <p class="text-sm text-zinc-700">
                                        Total: <span class="font-semibold text-zinc-900">₱{{ number_format($job->quote->total, 2) }}</span>
                                    </p>
                                </div>
                                <a href="{{ route('owner.quotes.edit', $job->quote) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    View Quote
                                </a>
                            </div>
                            @if($job->quote->rejection_reason)
                                <div class="p-3 bg-red-50 rounded-lg">
                                    <span class="text-sm text-red-500">Rejection Reason:</span>
                                    <p class="text-sm text-red-700 mt-1">{{ $job->quote->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="p-6 border-b border-zinc-200">
                            <h2 class="text-lg font-semibold text-zinc-900">Quote / Receipt</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-zinc-600">Quote is being generated.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Job Timeline -->
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm">
                    <div class="p-6 border-b border-zinc-200">
                        <h2 class="text-lg font-semibold text-zinc-900">Job Timeline</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-900">Job Created</p>
                                    <p class="text-xs text-zinc-500">{{ $job->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            @if($job->started_at)
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-zinc-900">Work Started</p>
                                        <p class="text-xs text-zinc-500">{{ $job->started_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($job->completed_at)
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-zinc-900">Job Completed</p>
                                        <p class="text-xs text-zinc-500">{{ $job->completed_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($job->deadline)
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-zinc-900">Deadline</p>
                                        <p class="text-xs text-zinc-500">{{ $job->deadline->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Customer Information -->
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm">
                    <div class="p-6 border-b border-zinc-200">
                        <h2 class="text-lg font-semibold text-zinc-900">Customer</h2>
                    </div>
                    <div class="p-6">
                        @if($job->customer)
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-orange-400 flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                                    {{ $job->customer->initials() }}
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900">{{ $job->customer->first_name }} {{ $job->customer->last_name }}</p>
                                    <p class="text-sm text-zinc-600">{{ $job->customer->email }}</p>
                                </div>
                            </div>
                            @if($job->customer->phone)
                                <div class="text-sm">
                                    <span class="text-zinc-500">Phone:</span>
                                    <p class="text-zinc-900">{{ $job->customer->phone }}</p>
                                </div>
                            @endif
                        @else
                            <p class="text-zinc-500">No customer information</p>
                        @endif
                    </div>
                </div>

                <!-- Employee Assignment -->
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm">
                    <div class="p-6 border-b border-zinc-200">
                        <h2 class="text-lg font-semibold text-zinc-900">Assign Employee</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('owner.jobs.assign', $job) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="employee_id" class="w-full text-sm border border-zinc-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-zinc-700">
                                <option value="">Unassigned</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $job->employee_id === $employee->id ? 'selected' : '' }}>
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="mt-3 w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                Update Assignment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app.owner>
