<div x-data="printbuddyChat()" x-init="initChat()" class="flex flex-col h-full">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4 px-1 cursor-pointer" @click="toggleExpanded()">
        <div class="font-bold text-slate-900 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            PrintBuddy
        </div>
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/guy_welcome.svg') }}" alt="PrintBuddy" class="w-6 h-6 rounded-full object-cover">
            <svg x-show="!isExpanded" class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
            <svg x-show="isExpanded" class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7 7" />
            </svg>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-50 to-blue-50 border border-purple-100 rounded-2xl overflow-hidden flex flex-col h-full" x-show="isExpanded" x-transition x-cloak>
        <!-- Chat Messages -->
        <div class="p-4 overflow-y-auto flex-1 min-h-0 space-y-3" id="printbuddy-messages">
            <!-- Welcome Message -->
            <div class="flex gap-2">
                <img src="{{ asset('images/guy_welcome.svg') }}" alt="PrintBuddy" class="w-8 h-8 rounded-full flex-shrink-0 object-cover">
                <div class="bg-white border border-purple-200 rounded-2xl rounded-tl-none p-3 max-w-[85%] shadow-sm">
                    <div class="text-sm prose prose-sm prose-purple max-w-none" x-html="parseMarkdown('Hello! I\'m PrintBuddy, your AI assistant. How can I help you manage your printing business today?')"></div>
                </div>
            </div>

            <!-- Dynamic Messages -->
            <template x-for="message in messages" :key="message.id">
                <div class="flex gap-2" :class="message.role === 'user' ? 'flex-row-reverse' : ''">
                    <template x-if="message.role === 'user'">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-pink-500 flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">U</div>
                    </template>
                    <template x-if="message.role === 'assistant'">
                        <img src="{{ asset('images/guy_welcome.svg') }}" alt="PrintBuddy" class="w-8 h-8 rounded-full flex-shrink-0 object-cover">
                    </template>
                    <div class="rounded-2xl p-3 max-w-[85%] shadow-sm"
                         :class="message.role === 'user' ? 'bg-gradient-to-r from-orange-400 to-pink-500 text-white rounded-tr-none' : 'bg-white border border-purple-200 rounded-tl-none'">
                        <div class="text-sm prose prose-sm prose-purple max-w-none" :class="message.role === 'user' ? 'prose-invert' : ''" x-html="message.role === 'assistant' ? parseMarkdown(message.content) : message.content"></div>
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="isLoading" class="flex gap-2">
                <img src="{{ asset('images/guy_welcome.svg') }}" alt="PrintBuddy" class="w-8 h-8 rounded-full flex-shrink-0 object-cover">
                <div class="bg-white border border-purple-200 rounded-2xl rounded-tl-none p-3 max-w-[85%] shadow-sm">
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="p-3 border-t border-purple-100 bg-white/50 flex-shrink-0">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input 
                    type="text" 
                    x-model="newMessage" 
                    placeholder="Ask PrintBuddy..." 
                    class="flex-1 px-4 py-2 bg-white border border-purple-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    :disabled="isLoading"
                >
                <button 
                    type="submit" 
                    class="w-10 h-10 bg-purple-500 text-white rounded-full flex items-center justify-center hover:bg-purple-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isLoading || !newMessage.trim()"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
function printbuddyChat() {
    return {
        isExpanded: false,
        messages: [],
        newMessage: '',
        isLoading: false,
        conversationId: null,
        messageId: 0,

        initChat() {
            // Initialize any existing conversation if needed
        },

        toggleExpanded() {
            this.isExpanded = !this.isExpanded;
            this.$dispatch('toggle-printbuddy');
        },

        parseMarkdown(text) {
            return marked.parse(text);
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.isLoading) return;

            const userMessage = this.newMessage.trim();
            this.messages.push({
                id: this.messageId++,
                role: 'user',
                content: userMessage
            });

            this.newMessage = '';
            this.isLoading = true;

            // Scroll to bottom
            this.scrollToBottom();

            try {
                const response = await fetch('/printbuddy/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: userMessage,
                        conversation_id: this.conversationId
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.messages.push({
                        id: this.messageId++,
                        role: 'assistant',
                        content: data.message
                    });

                    if (data.conversation_id) {
                        this.conversationId = data.conversation_id;
                    }
                } else {
                    this.messages.push({
                        id: this.messageId++,
                        role: 'assistant',
                        content: data.message || 'Sorry, something went wrong. Please try again.'
                    });
                }
            } catch (error) {
                this.messages.push({
                    id: this.messageId++,
                    role: 'assistant',
                    content: 'Sorry, I encountered an error. Please try again.'
                });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            const chatMessages = document.getElementById('printbuddy-messages');
            setTimeout(() => {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 100);
        }
    }
}
</script>
