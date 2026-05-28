<div x-data="{
    show: false,
    service: null,
    deadline: '',
    quantity: 1,
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
    close() {
        this.show = false;
        this.deadline = '';
        this.quantity = 1;
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
class="fixed inset-0 z-[9999] overflow-y-auto"
style="display: none;">
    
    <!-- Modal panel -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-2xl border-2 border-zinc-200 transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-bold text-zinc-900" x-text="service?.name || 'Service Details'"></h3>
                            <button @click="close()" class="text-zinc-400 hover:text-zinc-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Service Type Badge -->
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                  :class="service?.type === 'printing' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                                  x-text="service?.type === 'printing' ? 'Printing Service' : 'Technical Service'">
                            </span>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-zinc-900 mb-2">Description</h4>
                            <p class="text-zinc-600" x-text="service?.description || 'No description available.'"></p>
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-zinc-900 mb-2">Price</h4>
                            <p class="text-3xl font-bold"
                               :class="service?.type === 'printing' ? 'text-[#E8743B]' : 'text-[#19A7CE]'">
                                ₱<span x-text="service?.price ? parseFloat(service.price).toFixed(2) : '0.00'"></span>
                            </p>
                        </div>

                        <!-- Additional Details -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-zinc-900 mb-2">Service Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-zinc-500">Service ID:</span>
                                    <span class="text-zinc-900 ml-2" x-text="service?.id || 'N/A'"></span>
                                </div>
                                <div>
                                    <span class="text-zinc-500">Status:</span>
                                    <span class="text-green-600 ml-2 font-medium">Available</span>
                                </div>
                                <div>
                                    <span class="text-zinc-500">Estimated Time:</span>
                                    <span class="text-zinc-900 ml-2">2-3 business days</span>
                                </div>
                                <div>
                                    <span class="text-zinc-500">Category:</span>
                                    <span class="text-zinc-900 ml-2" x-text="service?.type || 'General'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Options -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-zinc-900 mb-2">When do you need this?</h4>
                            <p class="text-zinc-600 text-sm mb-3">
                                Select the date you need this service completed. Delivery depends on our schedule and availability in your area.
                            </p>
                            <div class="relative">
                                <input
                                    type="date"
                                    name="deadline"
                                    x-model="deadline"
                                    class="w-full px-4 py-3 pl-12 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E8743B] focus:border-transparent text-gray-900 text-sm bg-white"
                                    :min="new Date().toISOString().split('T')[0]"
                                    required
                                >
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-xs text-zinc-500 mt-2">
                                Note: We deliver when near your area and based on staff availability.
                            </p>
                        </div>

                        <!-- Estimated Completion -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h5 class="font-medium text-blue-900 mb-1">Estimated Completion</h5>
                                    <p class="text-sm text-blue-700">
                                        Based on this service's production time (<span x-text="service?.production_time || 'N/A'"></span> business days), your order should be ready by:
                                    </p>
                                    <p class="text-lg font-semibold text-blue-900 mt-1" x-text="estimatedCompletion || 'Select a service to see estimate'"></p>
                                    <p class="text-xs text-blue-600 mt-2">
                                        This is an estimate. Actual completion may vary based on workload and delivery schedule.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-zinc-900 mb-2">Additional Notes</h4>
                            <p class="text-zinc-600 text-sm mb-3">
                                Please provide any specific requirements or details for your service request.
                            </p>
                            <form action="{{ route('customer.request-service') }}" method="POST" id="service-request-form">
                                @csrf
                                <input type="hidden" name="service_id" :value="service?.id">
                                <input type="hidden" name="service_type" :value="service?.type">
                                <input type="hidden" name="deadline" :value="deadline">
                                <textarea
                                    name="notes"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E8743B] focus:border-transparent text-gray-900"
                                    placeholder="Describe your specific requirements, quantity, timeline, or any other details..."
                                ></textarea>

                                <!-- Invoice Request Checkbox -->
                                <div class="mt-4 p-4 bg-zinc-50 rounded-lg border border-zinc-200">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <div class="relative flex items-center">
                                            <input
                                                type="checkbox"
                                                name="request_invoice"
                                                value="1"
                                                class="peer sr-only"
                                            >
                                            <div class="w-5 h-5 border-2 border-zinc-300 rounded peer-checked:bg-[#E8743B] peer-checked:border-[#E8743B] transition-colors flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-sm font-medium text-zinc-900">Send me an invoice</span>
                                            </div>
                                            <p class="text-xs text-zinc-500 mt-1">Receive a PDF invoice via email when your order is completed</p>
                                        </div>
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" 
                        form="service-request-form"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                        :class="service?.type === 'printing' ? 'bg-[#E8743B] hover:bg-[#d66532]' : 'bg-[#19A7CE] hover:bg-[#1596b8]'">
                    Request Service
                </button>
                <button type="button" 
                        @click="close()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
