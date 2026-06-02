<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1400px] mx-auto">
        @if(session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden mb-8">
            <div class="welcome-dots"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Payments</h1>
                    <p class="text-orange-100">Verify GCash payments submitted by customers.</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ $counts['pending'] }}</div>
                    <div class="text-orange-100 text-sm">Pending</div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('owner.payments', ['status' => 'pending_verification']) }}"
                class="rounded-full px-4 py-2 text-sm font-medium transition {{ $currentStatus === 'pending_verification' ? 'bg-amber-500 text-white' : 'bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-50' }}">
                Pending ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('owner.payments', ['status' => 'verified']) }}"
                class="rounded-full px-4 py-2 text-sm font-medium transition {{ $currentStatus === 'verified' ? 'bg-emerald-500 text-white' : 'bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-50' }}">
                Verified ({{ $counts['verified'] }})
            </a>
            <a href="{{ route('owner.payments', ['status' => 'rejected']) }}"
                class="rounded-full px-4 py-2 text-sm font-medium transition {{ $currentStatus === 'rejected' ? 'bg-red-500 text-white' : 'bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-50' }}">
                Rejected ({{ $counts['rejected'] }})
            </a>
            <a href="{{ route('owner.payments', ['status' => 'all']) }}"
                class="rounded-full px-4 py-2 text-sm font-medium transition {{ $currentStatus === 'all' ? 'bg-zinc-900 text-white' : 'bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-50' }}">
                All
            </a>
        </div>

        @if($payments->isEmpty())
            <div class="bg-white rounded-2xl border border-zinc-200 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-zinc-100 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-zinc-900">No {{ $currentStatus === 'all' ? '' : $currentStatus }} payments</h3>
                <p class="text-sm text-zinc-500 mt-1">Payments will appear here once customers submit them.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($payments as $payment)
                    <div class="bg-white rounded-2xl border border-zinc-200 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-mono text-zinc-500">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-zinc-300">•</span>
                                    <span class="text-sm text-zinc-700 font-semibold">{{ trim($payment->customer->first_name.' '.$payment->customer->last_name) }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-blue-100 text-blue-700">{{ strtoupper($payment->method) }}</span>
                                    <span class="text-lg font-bold text-zinc-900">₱{{ number_format($payment->amount, 2) }}</span>
                                </div>
                                <p class="text-xs text-zinc-500 mt-1">
                                    Quote
                                    <a href="{{ route('owner.quotes.view', $payment->quote) }}" class="font-mono text-blue-600 hover:underline">{{ $payment->quote->quote_number }}</a>
                                    @if($payment->serviceJob)
                                        · {{ $payment->serviceJob->name }}
                                    @endif
                                    · Submitted {{ $payment->created_at->diffForHumans() }}
                                </p>
                                @if($payment->reference_no)
                                    <p class="text-xs text-zinc-600 mt-1">Ref: <span class="font-mono">{{ $payment->reference_no }}</span></p>
                                @endif
                                @if($payment->notes)
                                    <p class="text-xs text-zinc-600 mt-1 italic">"{{ $payment->notes }}"</p>
                                @endif
                                @if($payment->rejection_reason)
                                    <p class="text-xs text-red-700 mt-1"><span class="font-semibold">Rejected:</span> {{ $payment->rejection_reason }}</p>
                                @endif
                            </div>

                            @if($payment->proof_path)
                                <a href="{{ asset('storage/'.$payment->proof_path) }}" target="_blank" class="shrink-0">
                                    <img src="{{ asset('storage/'.$payment->proof_path) }}" alt="Proof" class="w-20 h-20 object-cover rounded-lg border border-zinc-200 hover:shadow-md transition">
                                </a>
                            @endif
                        </div>

                        @if($payment->status === 'pending_verification')
                            <div class="mt-4 pt-4 border-t border-zinc-100 flex flex-wrap gap-2 justify-end">
                                <button type="button"
                                    x-data
                                    @click="$dispatch('open-reject-{{ $payment->id }}')"
                                    class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                                    Reject
                                </button>
                                <form action="{{ route('owner.payments.verify', $payment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                                        Verify
                                    </button>
                                </form>
                            </div>

                            <div x-data="{ open: false }"
                                @open-reject-{{ $payment->id }}.window="open = true"
                                x-show="open"
                                @keydown.escape.window="open = false"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                                style="display: none;">
                                <div class="w-full max-w-md mx-4 bg-white rounded-2xl shadow-2xl border border-zinc-200 overflow-hidden" @click.outside="open = false">
                                    <form action="{{ route('owner.payments.reject', $payment) }}" method="POST">
                                        @csrf
                                        <div class="px-6 py-5">
                                            <h3 class="text-base font-semibold text-zinc-900 mb-1">Reject Payment</h3>
                                            <p class="text-sm text-zinc-500 mb-4">Tell the customer why this payment is being rejected.</p>
                                            <textarea name="rejection_reason" rows="3" maxlength="500" required
                                                class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm text-zinc-900 bg-white resize-none"
                                                placeholder="e.g. Reference number could not be verified."></textarea>
                                        </div>
                                        <div class="flex gap-3 px-6 py-4 bg-zinc-50">
                                            <button type="button" @click="open = false" class="flex-1 px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-lg hover:bg-zinc-50 transition">Cancel</button>
                                            <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Reject Payment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif($payment->status === 'verified')
                            <div class="mt-4 pt-4 border-t border-zinc-100 flex items-center justify-end text-xs text-emerald-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verified by {{ $payment->verifier ? trim($payment->verifier->first_name.' '.$payment->verifier->last_name) : '—' }}
                                @if($payment->verified_at) on {{ $payment->verified_at->format('M d, Y g:i A') }} @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app.owner>
