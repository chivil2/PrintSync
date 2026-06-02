@extends('layouts.app.customer')

@section('content')
@php
    $other = $conversation->customer_id === auth()->id() ? $conversation->owner : $conversation->customer;
@endphp
<div class="max-w-4xl mx-auto py-6 space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('customer.chat.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to messages
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold">
                {{ strtoupper(substr($other->first_name ?? 'O', 0, 1)) }}{{ strtoupper(substr($other->last_name ?? '', 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-slate-900">{{ $other->name ?? 'Owner' }}</p>
                @if($conversation->quote)
                    <p class="text-xs text-slate-500">Re: Quote
                        <a href="{{ route('customer.quotes.show', $conversation->quote) }}" class="text-blue-600 hover:underline">
                            {{ $conversation->quote->quote_number }}
                        </a>
                    </p>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <x-chat-thread
                :conversation="$conversation"
                :poll-url="route('customer.chat.poll', $conversation)"
                :send-url="route('customer.chat.send', $conversation)"
            />
        </div>
    </div>
</div>
@endsection
