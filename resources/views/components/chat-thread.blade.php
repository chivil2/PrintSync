@props([
    'conversation',
    'currentUserId' => null,
    'pollUrl' => null,
    'sendUrl' => null,
])

@php
    $currentUserId = $currentUserId ?? auth()->id();
    $other = $conversation->customer_id === $currentUserId ? $conversation->owner : $conversation->customer;
@endphp

<div
    class="flex flex-col h-full"
    x-data="chatThread({
        pollUrl: @js($pollUrl),
        sendUrl: @js($sendUrl),
        currentUserId: {{ $currentUserId }},
        initialMessages: @js($conversation->messages->map(fn ($m) => [
            'id' => $m->id,
            'body' => $m->body,
            'sender_id' => $m->sender_id,
            'sender_name' => $m->sender->name,
            'created_at' => $m->created_at->toIso8601String(),
        ])),
    })"
    x-init="init()"
>
    <div
        id="chat-messages"
        class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50 rounded-xl border border-slate-200"
        style="min-height: 400px; max-height: 60vh;"
    >
        <template x-if="messages.length === 0">
            <div class="text-center text-slate-500 text-sm py-12">
                No messages yet. Send the first one!
            </div>
        </template>
        <template x-for="m in messages" :key="m.id">
            <div class="flex" :class="m.sender_id === currentUserId ? 'justify-end' : 'justify-start'">
                <div
                    class="max-w-[75%] px-4 py-2.5 rounded-2xl text-sm shadow-sm"
                    :class="m.sender_id === currentUserId
                        ? 'bg-blue-600 text-white rounded-br-sm'
                        : 'bg-white text-slate-900 border border-slate-200 rounded-bl-sm'"
                >
                    <div x-text="m.body" class="whitespace-pre-wrap break-words"></div>
                    <div
                        class="text-[10px] mt-1"
                        :class="m.sender_id === currentUserId ? 'text-blue-100' : 'text-slate-400'"
                        x-text="formatTime(m.created_at)"
                    ></div>
                </div>
            </div>
        </template>
    </div>

    <form @submit.prevent="send()" class="mt-4 flex items-end gap-2">
        @csrf
        <textarea
            x-model="body"
            @keydown.enter.prevent="if(!$event.shiftKey) send()"
            rows="2"
            placeholder="Type a message..."
            class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-sm text-slate-900 bg-white"
            :disabled="sending"
        ></textarea>
        <button
            type="submit"
            :disabled="!body.trim() || sending"
            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors text-sm"
        >
            <span x-show="!sending">Send</span>
            <span x-show="sending">...</span>
        </button>
    </form>

    <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
        <span>
            <span x-text="messages.length"></span> message<span x-show="messages.length !== 1">s</span>
        </span>
        <button
            type="button"
            @click="fetch()"
            class="hover:text-slate-600 transition-colors"
        >
            Refresh
        </button>
    </div>
</div>

<script>
function chatThread({ pollUrl, sendUrl, currentUserId, initialMessages }) {
    return {
        messages: initialMessages || [],
        body: '',
        sending: false,
        lastModified: null,
        pollUrl: pollUrl,
        sendUrl: sendUrl,
        currentUserId: currentUserId,

        init() {
            this.scrollToBottom();
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.fetch();
            });
            window.addEventListener('focus', () => this.fetch());
        },

        async fetch() {
            const headers = {};
            if (this.lastModified) {
                headers['If-Modified-Since'] = this.lastModified;
            }
            try {
                const res = await fetch(this.pollUrl, { headers, credentials: 'same-origin' });
                if (res.status === 304) return;
                if (!res.ok) return;
                const data = await res.json();
                this.lastModified = data.last_modified || res.headers.get('Last-Modified');
                const known = new Set(this.messages.map(m => m.id));
                const incoming = (data.messages || []).filter(m => !known.has(m.id));
                if (incoming.length) {
                    this.messages = [...this.messages, ...incoming];
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (e) {
                console.error('chat fetch failed', e);
            }
        },

        async send() {
            const text = this.body.trim();
            if (!text || this.sending) return;
            this.sending = true;
            try {
                const res = await fetch(this.sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            || document.querySelector('input[name="_token"]')?.value,
                    },
                    body: JSON.stringify({ body: text }),
                    credentials: 'same-origin',
                });
                if (res.ok) {
                    this.body = '';
                    await this.fetch();
                } else {
                    console.error('send failed', res.status);
                }
            } catch (e) {
                console.error('chat send failed', e);
            } finally {
                this.sending = false;
            }
        },

        scrollToBottom() {
            const el = document.getElementById('chat-messages');
            if (el) el.scrollTop = el.scrollHeight;
        },

        formatTime(iso) {
            try {
                const d = new Date(iso);
                return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                return '';
            }
        },
    };
}
</script>
