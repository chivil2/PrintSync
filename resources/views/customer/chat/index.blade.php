@extends('layouts.app.customer')

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">
                Messages
            </h1>
            <p class="mt-1 text-sm text-slate-500">Conversations about your quotes with PrintSync.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($conversations->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <svg class="mx-auto w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="font-medium text-slate-700">No conversations yet</p>
                <p class="text-sm mt-1">Open a quote and click "Message owner" to start a conversation.</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($conversations as $conv)
                    <li>
                        <a href="{{ route('customer.chat.show', $conv) }}"
                           class="flex items-center gap-4 p-5 hover:bg-slate-50 transition-colors">
                            <div class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-sm shrink-0">
                                {{ strtoupper(substr($conv->owner->first_name ?? 'O', 0, 1)) }}{{ strtoupper(substr($conv->owner->last_name ?? '', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-900 truncate">
                                        {{ $conv->owner->name ?? 'Owner' }}
                                    </p>
                                    <span class="text-xs text-slate-400 shrink-0">
                                        {{ optional($conv->last_message_at)->diffForHumans() ?? 'No messages' }}
                                    </span>
                                </div>
                                @if($conv->quote)
                                    <p class="text-xs text-slate-500 mt-0.5">Re: Quote {{ $conv->quote->quote_number }}</p>
                                @endif
                                @php $last = $conv->messages->first(); @endphp
                                @if($last)
                                    <p class="text-sm text-slate-600 mt-1 truncate">
                                        {{ $last->sender_id === auth()->id() ? 'You: ' : '' }}{{ $last->body }}
                                    </p>
                                @endif
                            </div>
                            @if(($conv->unread ?? 0) > 0)
                                <span class="shrink-0 inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                    {{ $conv->unread }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
