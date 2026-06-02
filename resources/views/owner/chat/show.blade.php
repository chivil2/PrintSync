<x-layouts::app.owner>
    @php
        $other = $conversation->customer_id === auth()->id() ? $conversation->owner : $conversation->customer;
    @endphp
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1100px] mx-auto space-y-4">
        <a href="{{ route('owner.chat.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to messages
        </a>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold">
                    {{ strtoupper(substr($other->first_name ?? 'C', 0, 1)) }}{{ strtoupper(substr($other->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-900">{{ $other->name ?? 'Customer' }}</p>
                    @if($conversation->quote)
                        <p class="text-xs text-slate-500">Re: Quote
                            <a href="{{ route('owner.quotes.view', $conversation->quote) }}" class="text-blue-600 hover:underline">
                                {{ $conversation->quote->quote_number }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <x-chat-thread
                    :conversation="$conversation"
                    :poll-url="route('owner.chat.poll', $conversation)"
                    :send-url="route('owner.chat.send', $conversation)"
                />
            </div>
        </div>
    </div>
</x-layouts::app.owner>
