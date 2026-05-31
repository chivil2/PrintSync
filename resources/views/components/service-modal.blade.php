<div x-data="{
    show: false,
    service: null,
    deadline: '',
    quantity: 1,
    calendarOpen: false,
    calendarMonth: new Date().getMonth(),
    calendarYear: new Date().getFullYear(),
    requestInvoice: false,
    get estimatedCompletion() {
        if (!this.service || !this.service.production_time) return '';
        const productionDays = this.service.production_time;
        const today = new Date();
        let completionDate = new Date(today);
        let daysAdded = 0;
        while (daysAdded < productionDays) {
            completionDate.setDate(completionDate.getDate() + 1);
            const day = completionDate.getDay();
            if (day !== 0 && day !== 6) {
                daysAdded++;
            }
        }
        return completionDate.toISOString().split('T')[0];
    },
    get calendarDays() {
        const days = [];
        const firstDay = new Date(this.calendarYear, this.calendarMonth, 1).getDay();
        const daysInMonth = new Date(this.calendarYear, this.calendarMonth + 1, 0).getDate();
        for (let i = 0; i < firstDay; i++) days.push(null);
        for (let d = 1; d <= daysInMonth; d++) days.push(d);
        return days;
    },
    get calendarMonthName() {
        return new Date(this.calendarYear, this.calendarMonth).toLocaleDateString('en-US', { month: 'long' });
    },
    isToday(day) {
        const today = new Date();
        return day === today.getDate() && this.calendarMonth === today.getMonth() && this.calendarYear === today.getFullYear();
    },
    isPast(day) {
        if (!day) return true;
        const date = new Date(this.calendarYear, this.calendarMonth, day);
        const today = new Date();
        today.setHours(0,0,0,0);
        return date < today;
    },
    isSelected(day) {
        if (!day || !this.deadline) return false;
        const selected = new Date(this.deadline);
        return day === selected.getDate() && this.calendarMonth === selected.getMonth() && this.calendarYear === selected.getFullYear();
    },
    selectDay(day) {
        if (this.isPast(day)) return;
        const month = String(this.calendarMonth + 1).padStart(2, '0');
        const d = String(day).padStart(2, '0');
        this.deadline = `${this.calendarYear}-${month}-${d}`;
        this.calendarOpen = false;
    },
    prevMonth() {
        if (this.calendarMonth === 0) {
            this.calendarMonth = 11;
            this.calendarYear--;
        } else {
            this.calendarMonth--;
        }
    },
    nextMonth() {
        if (this.calendarMonth === 11) {
            this.calendarMonth = 0;
            this.calendarYear++;
        } else {
            this.calendarMonth++;
        }
    },
    get imageUrl() {
        return this.service?.image ? '/storage/' + this.service.image : null;
    },
    formatDate(iso) {
        if (!iso) return 'Select a date';
        const d = new Date(iso + 'T00:00:00');
        return d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
    },
    close() {
        this.show = false;
        this.deadline = '';
        this.quantity = 1;
        this.calendarOpen = false;
        setTimeout(() => {
            this.service = null;
        }, 200);
    }
}"
@open-modal.window="service = { ...$event.detail.service, type: $event.detail.type }; show = true"
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

        <!-- Close button -->
        <button @click="close()" class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-red-500 hover:bg-red-600 text-white transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Landscape layout: left info + right form -->
        <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-zinc-200">

            <!-- Left: Service Info -->
            <div class="flex-1 p-6 md:p-7 flex flex-col gap-4">
                <!-- Product Image -->
                <div class="relative -mx-6 -mt-6 md:-mx-7 md:-mt-7 h-48 bg-gradient-to-br overflow-hidden"
                     :class="service?.type === 'printing' ? 'from-orange-50 to-amber-50' : 'from-blue-50 to-indigo-50'">
                    <template x-if="imageUrl">
                        <img :src="imageUrl" :alt="service?.name" class="w-full h-full object-contain p-4">
                    </template>
                    <template x-if="!imageUrl">
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16" :class="service?.type === 'printing' ? 'text-orange-300' : 'text-blue-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="service?.type === 'printing'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                <path x-show="service?.type === 'technical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path x-show="service?.type === 'technical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </template>
                    <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide uppercase shadow-sm"
                          :class="service?.type === 'printing' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'"
                          x-text="service?.type === 'printing' ? 'Printing' : 'Technical'">
                    </span>
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-zinc-900" x-text="service?.name || 'Service Details'"></h3>
                    <p class="text-base text-zinc-500 mt-1 leading-relaxed" x-text="service?.description || 'No description available.'"></p>
                </div>

                <div>
                    <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1">Price</p>
                    <p class="text-2xl font-bold"
                       :class="service?.type === 'printing' ? 'text-[#E8743B]' : 'text-[#19A7CE]'">
                        ₱<span x-text="service?.price ? parseFloat(service.price).toFixed(2) : '0.00'"></span>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div>
                        <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider">Service ID</p>
                        <p class="text-zinc-700 font-medium" x-text="service?.id || 'N/A'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider">Status</p>
                        <p class="text-green-600 font-medium">Available</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider">Est. Time</p>
                        <p class="text-zinc-700 font-medium">2-3 business days</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 font-medium uppercase tracking-wider">Category</p>
                        <p class="text-zinc-700 font-medium capitalize" x-text="service?.type || 'General'"></p>
                    </div>
                </div>

                <!-- Estimated Completion -->
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-blue-900">Estimated Completion</p>
                            <p class="text-sm text-blue-700 mt-0.5" x-text="estimatedCompletion ? 'Ready by ' + estimatedCompletion : 'Select a service to see estimate'"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Order Form -->
            <div class="w-full md:w-[380px] p-6 md:p-7 flex flex-col gap-4">
                <h4 class="text-base font-bold text-zinc-900 font-[Poppins]">Request Service</h4>

                <form action="{{ route('customer.request-service') }}" method="POST" id="service-request-form" class="flex flex-col gap-4 flex-1">
                    @csrf
                    <input type="hidden" name="service_id" :value="service?.id">
                    <input type="hidden" name="service_type" :value="service?.type">

                    <!-- Quantity -->
                    <div>
                        <label class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1.5 block">Quantity</label>
                        <div class="flex items-center border border-zinc-300 rounded-lg overflow-hidden">
                            <button type="button"
                                    @click="quantity = Math.max(1, quantity - 1)"
                                    class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-zinc-700 hover:bg-zinc-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="number" name="quantity" x-model.number="quantity" min="1"
                                   class="w-full h-10 text-center text-sm font-medium text-zinc-900 border-x border-zinc-300 focus:outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                            <button type="button"
                                    @click="quantity++"
                                    class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-zinc-700 hover:bg-zinc-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Delivery Date -->
                    <div class="relative" @click.outside="calendarOpen = false">
                        <label class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1.5 block">Delivery Date</label>
                        <button type="button"
                                @click="calendarOpen = !calendarOpen"
                                class="w-full flex items-center gap-2.5 px-3.5 py-2.5 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E8743B]/30 focus:border-[#E8743B] text-sm bg-white hover:border-zinc-400 transition-colors text-left">
                            <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span :class="deadline ? 'text-zinc-900' : 'text-zinc-400'" class="font-[Open_Sans]" x-text="formatDate(deadline)"></span>
                        </button>
                        <input type="hidden" name="deadline" :value="deadline">

                        <div x-show="calendarOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute z-50 mt-1.5 w-[296px] bg-white border border-zinc-200 rounded-xl shadow-xl p-3.5"
                             style="display: none;">
                            <div class="flex items-center justify-between mb-3">
                                <button type="button"
                                        @click="prevMonth()"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-500 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <span class="text-sm font-semibold text-zinc-900" x-text="calendarMonthName + ' ' + calendarYear"></span>
                                <button type="button"
                                        @click="nextMonth()"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-500 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-7 gap-0.5 text-center mb-1.5">
                                <template x-for="h in ['Su','Mo','Tu','We','Th','Fr','Sa']" :key="h">
                                    <span class="text-[11px] font-semibold text-zinc-400 py-1" x-text="h"></span>
                                </template>
                            </div>
                            <div class="grid grid-cols-7 gap-0.5">
                                <template x-for="day in calendarDays" :key="day ? 'd-' + day : 'e-' + Math.random()">
                                    <button type="button"
                                            x-show="day !== null"
                                            :disabled="isPast(day)"
                                            @click="selectDay(day)"
                                            :class="{
                                                'bg-[#E8743B] text-white hover:bg-[#d66532]': isSelected(day) && !isPast(day),
                                                'bg-[#E8743B]/10 text-[#E8743B] font-semibold': isToday(day) && !isSelected(day) && !isPast(day),
                                                'text-zinc-300 cursor-not-allowed': isPast(day),
                                                'text-zinc-700 hover:bg-zinc-100': !isPast(day) && !isSelected(day)
                                            }"
                                            class="w-full aspect-square rounded-md text-xs font-medium transition-colors flex items-center justify-center"
                                            x-text="day">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="flex-1">
                        <label class="text-xs text-zinc-400 font-medium uppercase tracking-wider mb-1.5 block">Additional Notes</label>
                        <textarea
                            name="notes"
                            rows="3"
                            class="w-full px-3.5 py-2.5 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E8743B]/30 focus:border-[#E8743B] text-sm text-zinc-900 placeholder:text-zinc-400 resize-none"
                            placeholder="Specific requirements, timeline..."
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
                                class="flex-1 inline-flex justify-center items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                                :class="service?.type === 'printing' ? 'bg-[#E8743B] hover:bg-[#d66532] focus:ring-[#E8743B]/50' : 'bg-[#19A7CE] hover:bg-[#1596b8] focus:ring-[#19A7CE]/50'">
                            Request Service
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
