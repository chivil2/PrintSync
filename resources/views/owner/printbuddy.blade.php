<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1800px] mx-auto" x-data="printbuddyPage()">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <img src="{{ asset('images/guy_welcome.svg') }}" alt="PrintBuddy" class="w-12 h-12 rounded-full object-cover">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">PrintBuddy</h1>
                <p class="text-sm text-slate-500">Your AI assistant</p>
            </div>
            <div class="ml-auto flex gap-2">
                <button @click="activeTab = 'chat'" :class="activeTab === 'chat' ? 'bg-purple-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'" class="px-4 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-2 border border-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Chat
                </button>
                <button @click="activeTab = 'notes'" :class="activeTab === 'notes' ? 'bg-purple-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'" class="px-4 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-2 border border-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Notes
                </button>
                <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-purple-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'" class="px-4 py-2 rounded-lg font-medium text-sm transition-all flex items-center gap-2 border border-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </button>
            </div>
        </div>

        <!-- Chat Tab - Main View -->
        <div x-show="activeTab === 'chat'" x-transition>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="flex h-[calc(100vh-220px)] min-h-[500px]">
                    <!-- Main Chat Area -->
                    <div class="flex-1 flex flex-col">
                        <!-- Messages -->
                        <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50" id="printbuddy-page-messages">
                            <div class="flex gap-3">
                                <img src="{{ asset('images/guy_welcome.svg') }}" alt="PrintBuddy" class="w-10 h-10 rounded-full flex-shrink-0">
                                <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-none p-4 max-w-[80%] shadow-sm">
                                    <div class="text-sm text-slate-700">
                                        <p class="font-medium mb-2">Hello! I'm PrintBuddy, your AI assistant.</p>
                                        <p class="text-slate-500 text-sm">Ask me about your printing business:</p>
                                        <ul class="mt-2 text-sm text-slate-600 space-y-1">
                                            <li>• Services and pricing</li>
                                            <li>• Inventory levels</li>
                                            <li>• Employee status</li>
                                            <li>• Job progress</li>
                                            <li>• Quote details</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <template x-for="message in messages" :key="message.id">
                                <div class="flex gap-3" :class="message.role === 'user' ? 'flex-row-reverse' : ''">
                                    <template x-if="message.role === 'user'">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-pink-500 flex items-center justify-center text-white font-semibold flex-shrink-0">U</div>
                                    </template>
                                    <template x-if="message.role === 'assistant'">
                                        <img src="{{ asset('images/guy_welcome.svg') }}" alt="PrintBuddy" class="w-10 h-10 rounded-full flex-shrink-0">
                                    </template>
                                    <div class="rounded-2xl p-4 max-w-[80%] shadow-sm text-sm"
                                         :class="message.role === 'user' ? 'bg-gradient-to-r from-orange-400 to-pink-500 text-white rounded-tr-none' : 'bg-white border border-slate-200 rounded-tl-none text-slate-700'">
                                        <div x-html="message.role === 'assistant' ? parseMarkdown(message.content) : message.content"></div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="isLoading" class="flex gap-3">
                                <img src="{{ asset('images/guy_welcome.svg') }}" alt="PrintBuddy" class="w-10 h-10 rounded-full flex-shrink-0">
                                <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-none p-4 shadow-sm">
                                    <div class="flex gap-2">
                                        <div class="w-3 h-3 bg-purple-400 rounded-full animate-bounce"></div>
                                        <div class="w-3 h-3 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                        <div class="w-3 h-3 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Input -->
                        <div class="p-4 border-t border-slate-200 bg-white">
                            <form @submit.prevent="sendMessage" class="flex gap-3">
                                <input 
                                    type="text" 
                                    x-model="newMessage" 
                                    placeholder="Ask PrintBuddy about your business..." 
                                    class="flex-1 px-5 py-3 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    :disabled="isLoading"
                                >
                                <button 
                                    type="submit" 
                                    class="px-6 py-3 bg-purple-600 text-white rounded-full font-medium hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    :disabled="isLoading || !newMessage.trim()"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Sidebar - Quick Actions -->
                    <div class="w-64 border-l border-slate-200 p-4 bg-white flex flex-col">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Quick Actions</h3>
                        <div class="space-y-1">
                            <button @click="quickAsk('Show me all printing services')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                View Services
                            </button>
                            <button @click="quickAsk('Show me current inventory levels')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                Check Inventory
                            </button>
                            <button @click="quickAsk('Show me all employees and their status')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                View Employees
                            </button>
                            <button @click="quickAsk('Show me recent jobs and their status')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                View Jobs
                            </button>
                            <button @click="quickAsk('Show me recent quotes')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                View Quotes
                            </button>
                        </div>

                        <!-- Manage Section -->
                        <div class="mt-3 pt-3 border-t border-slate-200">
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Manage</h3>
                            <div class="space-y-1">
                                <button @click="quickAsk('Add a new service')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                    Add Service
                                </button>
                                <button @click="quickAsk('Edit a service')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                    Edit Service
                                </button>
                                <button @click="quickAsk('Remove a service')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                    Remove Service
                                </button>
                                <button @click="quickAsk('Add new inventory item')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                    Add Inventory
                                </button>
                                <button @click="quickAsk('Edit an inventory item')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                    Edit Inventory
                                </button>
                                <button @click="quickAsk('Remove inventory item')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs text-slate-600 cursor-pointer">
                                    Remove Inventory
                                </button>
                            </div>
                        </div>

                        <!-- Recent Notes -->
                        @if($notes->count() > 0)
                        <div class="mt-3 pt-3 border-t border-slate-200">
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Recent Notes</h3>
                            <div class="space-y-1">
                                @foreach($notes->take(3) as $note)
                                    <button @click="quickAsk('Show me my notes')" class="w-full text-left px-2 py-1.5 rounded hover:bg-slate-100 transition-colors text-xs cursor-pointer">
                                        @if($note->title)
                                            <div class="font-medium text-slate-700 truncate">{{ $note->title }}</div>
                                        @endif
                                        <div class="text-slate-500 truncate">{{ Str::limit($note->content, 30) }}</div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Tab -->
        <div x-show="activeTab === 'notes'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add Note Form -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <h3 class="font-semibold text-slate-900 mb-4 text-sm">Add Note</h3>
                    <form action="{{ route('owner.printbuddy.notes.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="title" placeholder="Title (optional)" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="mb-3">
                            <textarea name="content" rows="5" placeholder="Write your note..." class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none" required></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors cursor-pointer">
                            Save Note
                        </button>
                    </form>
                </div>

                <!-- Notes List -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <h3 class="font-semibold text-slate-900 mb-4 text-sm">Your Notes</h3>
                    @if($notes->count() > 0)
                        <div class="space-y-3 max-h-[500px] overflow-y-auto">
                            @foreach($notes as $note)
                                <div class="p-4 bg-slate-50 rounded-xl">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1">
                                            @if($note->title)
                                                <h4 class="font-medium text-slate-900 text-sm mb-1">{{ $note->title }}</h4>
                                            @endif
                                            <p class="text-sm text-slate-600 whitespace-pre-wrap">{{ $note->content }}</p>
                                            <p class="text-xs text-slate-400 mt-2">{{ $note->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                        <form action="{{ route('owner.printbuddy.notes.destroy', $note) }}" method="POST" onsubmit="return confirm('Delete?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-slate-500 text-sm">
                            <p>No notes yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div x-show="activeTab === 'settings'" x-transition>
            <div class="max-w-xl">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <h3 class="font-semibold text-slate-900 mb-4 text-sm">API Settings</h3>
                    <form action="{{ route('owner.printbuddy.api-key') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">GROQ API Key</label>
                            <input 
                                type="password" 
                                name="groq_api_key" 
                                value="{{ auth()->user()->groq_api_key ?? '' }}"
                                placeholder="Enter your GROQ API key"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                            >
                            <p class="text-xs text-slate-500 mt-1">Get your free API key at <a href="https://console.groq.com" target="_blank" class="text-purple-600 hover:underline">console.groq.com</a></p>
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors cursor-pointer">
                            Save API Key
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts::app.owner>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
function printbuddyPage() {
    return {
        activeTab: 'chat',
        messages: [],
        newMessage: '',
        isLoading: false,
        conversationId: null,
        messageId: 0,
        STORAGE_KEY: 'printbuddy_chat',
        MAX_MESSAGES: 10,
        MAX_CONTENT_LENGTH: 500,

        init() {
            // Clear storage on each page load for fresh session
            this.clearStorage();
        },

        loadFromStorage() {
            const stored = localStorage.getItem(this.STORAGE_KEY);
            if (stored) {
                try {
                    const data = JSON.parse(stored);
                    this.messages = data.messages || [];
                    this.conversationId = data.conversationId || null;
                    this.messageId = this.messages.length > 0 ? Math.max(...this.messages.map(m => m.id)) + 1 : 0;
                } catch (e) {
                    this.messages = [];
                }
            }
        },

        saveToStorage() {
            const compactMessages = this.messages.slice(-this.MAX_MESSAGES).map(m => ({
                id: m.id,
                role: m.role,
                content: m.content.length > this.MAX_CONTENT_LENGTH 
                    ? m.content.substring(0, this.MAX_CONTENT_LENGTH) + '...'
                    : m.content
            }));
            localStorage.setItem(this.STORAGE_KEY, JSON.stringify({
                messages: compactMessages,
                conversationId: this.conversationId
            }));
        },

        clearStorage() {
            localStorage.removeItem(this.STORAGE_KEY);
        },

        parseMarkdown(text) {
            return marked.parse(text);
        },

        quickAsk(question) {
            this.newMessage = question;
            this.sendMessage();
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
                        conversation_id: this.conversationId,
                        history: this.messages.slice(0, -1).map(m => ({ role: m.role, content: m.content }))
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
                        content: data.message || 'Sorry, something went wrong.'
                    });
                }
            } catch (error) {
                this.messages.push({
                    id: this.messageId++,
                    role: 'assistant',
                    content: 'Sorry, I encountered an error.'
                });
            } finally {
                this.isLoading = false;
                this.saveToStorage();
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            const chatMessages = document.getElementById('printbuddy-page-messages');
            setTimeout(() => {
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        }
    }
}
</script>
