<div x-data="{
    show: false,
    service: null,
    problemDescription: '',
    priority: 'standard',
    preferredAt: '',
    contactPreference: 'phone',
    notes: '',
    requestInvoice: false,
    get minDateTime() {
        const d = new Date();
        d.setDate(d.getDate() + 1);
        d.setHours(9, 0, 0, 0);
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    },
    get priorityOptions() {
        return [
            { id: 'standard', name: 'Standard', sub: '3-5 days', tone: 'sky' },
            { id: 'urgent', name: 'Urgent', sub: '24-48h', tone: 'amber' },
            { id: 'emergency', name: 'Emergency', sub: 'Same day', tone: 'rose' },
        ];
    },
    get priorityToneClass() {
        const tones = {
            standard: { active: 'bg-sky-600 border-sky-600 text-white', dot: 'bg-sky-500' },
            urgent: { active: 'bg-amber-500 border-amber-500 text-white', dot: 'bg-amber-500' },
            emergency: { active: 'bg-rose-600 border-rose-600 text-white', dot: 'bg-rose-500' },
        };
        return tones[this.priority] || tones.standard;
    },
    get contactOptions() {
        return [
            { id: 'phone', label: 'Phone call' },
            { id: 'email', label: 'Email' },
            { id: 'sms', label: 'SMS / Text' },
        ];
    },
    get imageUrl() {
        return this.service?.image ? '/storage/' + this.service.image : null;
    },
    get isSubmittable() {
        return this.problemDescription.trim().length >= 20 && this.priority && this.preferredAt && this.contactPreference;
    },
    formatDateTime(local) {
        if (!local) return 'Pick a date and time';
        const d = new Date(local);
        if (isNaN(d.getTime())) return 'Pick a date and time';
        return d.toLocaleString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' });
    },
    close() {
        this.show = false;
        this.problemDescription = '';
        this.priority = 'standard';
        this.preferredAt = '';
        this.contactPreference = 'phone';
        this.notes = '';
        this.requestInvoice = false;
        setTimeout(() => {
            this.service = null;
        }, 200);
    }
}"
@open-tech-modal.window="service = { ...$event.detail.service, type: $event.detail.type }; show = true"
@modal-close.window="close()"
x-show="show"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
style="display: none;">

    <div class="fixed inset-0 bg-black/40" @click="close()"></div>

    <div class="relative w-full max-w-5xl bg-white rounded-2xl shadow-2xl border border-zinc-200 overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        <button @click="close()" class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-red-500 hover:bg-red-600 text-white transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-zinc-200">

            <!-- Left: Service Info -->
            <div class="flex-1 p-6 md:p-7 flex flex-col gap-4">
                <div class="relative -mx-6 -mt-6 md:-mx-7 md:-mt-7 h-48 bg-gradient-to-br from-blue-50 to-indigo-50 overflow-hidden">
                    <template x-if="imageUrl">
                        <img :src="imageUrl" :alt="service?.name" class="w-full h-full object-contain p-4">
                    </template>
                    <template x-if="!imageUrl">
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </template>
                    <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide uppercase shadow-sm bg-blue-100 text-blue-700">
                        Technical Support
                    </span>
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-zinc-900" x-text="service?.name || 'Service Details'"></h3>
                    <p class="text-base text-zinc-500 mt-1 leading-relaxed" x-text="service?.description || 'No description available.'"></p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1">Starting Price</p>
                    <p class="text-2xl font-bold text-[#19A7CE]">
                        ₱<span x-text="service?.price ? parseFloat(service.price).toFixed(2) : '0.00'"></span>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div>
                        <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider">Service ID</p>
                        <p class="text-zinc-700 font-medium" x-text="service?.id || 'N/A'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider">Category</p>
                        <p class="text-zinc-700 font-medium">Technical Support</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider">Est. Response</p>
                        <p class="text-zinc-700 font-medium">Based on priority</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider">Status</p>
                        <p class="text-green-600 font-medium">Available</p>
                    </div>
                </div>

                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-blue-900">How it works</p>
                            <p class="text-sm text-blue-700 mt-0.5">Tell us what's wrong, pick a priority, and we'll match you with a technician. You'll get a quote to review before any work starts.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Support Request Form -->
            <div class="w-full md:w-[420px] p-6 md:p-7 flex flex-col gap-4">
                <h4 class="text-base font-bold text-zinc-900 font-[Poppins]">Request Technical Support</h4>

                <form action="{{ route('customer.request-service') }}" method="POST" id="tech-support-request-form" class="flex flex-col gap-4 flex-1">
                    @csrf
                    <input type="hidden" name="service_id" :value="service?.id">
                    <input type="hidden" name="service_type" :value="service?.type">

                    <!-- Problem Description -->
                    <div>
                        <label class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1.5 block">
                            Problem Description <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="problem_description"
                            x-model="problemDescription"
                            rows="4"
                            required
                            minlength="20"
                            maxlength="1000"
                            class="w-full px-3.5 py-2.5 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#19A7CE]/30 focus:border-[#19A7CE] text-sm text-zinc-900 placeholder:text-zinc-400 resize-none"
                            placeholder="What's going wrong? When did it start? Any error messages..."
                        ></textarea>
                        <p class="text-[11px] text-zinc-400 mt-1 flex items-center justify-between">
                            <span>Be as specific as possible.</span>
                            <span x-text="problemDescription.length + ' / 1000'"></span>
                        </p>
                    </div>

                    <!-- Priority Level -->
                    <div>
                        <label class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1.5 block">
                            Priority Level <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="opt in priorityOptions" :key="opt.id">
                                <button
                                    type="button"
                                    @click="priority = opt.id"
                                    :class="priority === opt.id ? priorityToneClass.active : 'bg-white border-zinc-300 text-zinc-700 hover:border-zinc-400'"
                                    class="border rounded-lg px-2 py-2.5 text-left transition-colors"
                                >
                                    <div class="text-xs font-semibold" x-text="opt.name"></div>
                                    <div class="text-[10px] mt-0.5"
                                         :class="priority === opt.id ? 'text-white/80' : 'text-zinc-400'"
                                         x-text="opt.sub"></div>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="priority" :value="priority">
                    </div>

                    <!-- Preferred Date & Time -->
                    <div>
                        <label class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1.5 block">
                            Preferred Date &amp; Time <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <input
                                type="datetime-local"
                                name="preferred_at"
                                x-model="preferredAt"
                                :min="minDateTime"
                                required
                                class="w-full pl-10 pr-3.5 py-2.5 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#19A7CE]/30 focus:border-[#19A7CE] text-sm text-zinc-900"
                            >
                        </div>
                        <p class="text-[11px] text-zinc-400 mt-1" x-text="formatDateTime(preferredAt)"></p>
                    </div>

                    <!-- Contact Preference -->
                    <div>
                        <label class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1.5 block">
                            Contact Preference <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="opt in contactOptions" :key="opt.id">
                                <button
                                    type="button"
                                    @click="contactPreference = opt.id"
                                    :class="contactPreference === opt.id ? 'bg-[#19A7CE] border-[#19A7CE] text-white' : 'bg-white border-zinc-300 text-zinc-700 hover:border-zinc-400'"
                                    class="border rounded-lg px-2 py-2 text-xs font-medium transition-colors"
                                    x-text="opt.label"
                                ></button>
                            </template>
                        </div>
                        <input type="hidden" name="contact_preference" :value="contactPreference">
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1.5 block">Additional Notes</label>
                        <textarea
                            name="notes"
                            x-model="notes"
                            rows="2"
                            class="w-full px-3.5 py-2.5 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#19A7CE]/30 focus:border-[#19A7CE] text-sm text-zinc-900 placeholder:text-zinc-400 resize-none"
                            placeholder="Anything else we should know? (optional)"
                        ></textarea>
                    </div>

                    <!-- Invoice Checkbox -->
                    <label class="flex items-center gap-3 p-3 bg-zinc-50 rounded-lg border border-zinc-200 cursor-pointer hover:bg-zinc-100 transition-colors">
                        <input type="checkbox" name="request_invoice" value="1" x-model="requestInvoice" class="sr-only">
                        <div @click="requestInvoice = !requestInvoice"
                             class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                             :class="requestInvoice ? 'bg-blue-600 border-blue-600' : 'bg-white border-zinc-300'">
                            <svg x-show="requestInvoice" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-zinc-800">Send me an invoice</span>
                            <p class="text-xs text-zinc-400 mt-0.5">Receive a PDF invoice via email when completed</p>
                        </div>
                    </label>

                    <!-- Actions -->
                    <div class="flex items-center gap-2.5 pt-2">
                        <button type="submit"
                                :disabled="!isSubmittable"
                                class="flex-1 inline-flex justify-center items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white bg-[#19A7CE] hover:bg-[#1596b8] focus:outline-none focus:ring-2 focus:ring-[#19A7CE]/50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Request Support
                        </button>
                        <button type="button"
                                @click="close()"
                                class="px-4 py-2.5 rounded-lg border border-zinc-300 text-sm font-medium text-zinc-600 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-300 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
