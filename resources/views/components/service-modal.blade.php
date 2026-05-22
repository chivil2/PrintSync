<div x-data="{ 
    show: false,
    service: null,
    close() {
        this.show = false;
        // Clear service after animation completes
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

                        <!-- Notes -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-zinc-900 mb-2">Notes</h4>
                            <p class="text-zinc-600 text-sm">
                                Please contact us for custom requirements or bulk orders. 
                                Prices may vary based on specifications and quantity.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        @click="close()"
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
