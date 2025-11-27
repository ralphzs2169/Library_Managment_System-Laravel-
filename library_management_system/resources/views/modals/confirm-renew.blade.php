<div id="confirm-renew-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-70 transition-opacity duration-150">
    <div id="confirm-renew-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-secondarypx-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="renew-back-to-borrower-profile" class="cursor-pointer text-gray-600 hover:text-gray-800 transition">
                    <img src="{{ asset('build/assets/icons/back.svg') }}" alt="Back" class="w-6 h-6">
                </button>
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    Confirm Renewal
                </h2>
            </div>
            <button id="renew-close-confirm-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <form id="confirm-renew-form" novalidate>
            @csrf
            <!-- Content -->
            <div class="px-6 py-5 overflow-y-auto">
                <!-- Renewer Info Banner -->
                <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            <span id="renewer-initials">--</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Renewing for</p>
                            <p id="renewer-name" class="font-bold text-gray-900"></p>
                        </div>
                    </div>
                    <p id="renewer-id" class="text-xs text-gray-500 font-mono"></p>
                </div>
                <!-- Book Details Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-4 relative">
                    <!-- Status Badge -->
                    <div id="confirm-renew-status-badge" class="absolute top-3 right-3"></div>
                    <div class="flex gap-4">
                        <!-- Book Cover -->
                        <img id="renew-book-cover" src="" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">
                        <!-- Book Info -->
                        <div class="flex-1 space-y-2">
                            <div>
                                <h4 id="renew-book-title" class="text-lg font-bold text-gray-900"></h4>
                                <p id="renew-book-author" class="text-xs text-gray-600"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                                <div>
                                    <p class="text-gray-500 uppercase tracking-wide">ISBN</p>
                                    <p id="renew-book-isbn" class="font-semibold text-gray-800 font-mono text-[10px]"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 uppercase tracking-wide">Copy No.</p>
                                    <p id="renew-copy-number" class="font-semibold text-gray-800"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 uppercase tracking-wide">Initial Borrow Date</p>
                                    <p id="renew-initial-borrow-date" class="font-semibold text-gray-800"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 uppercase tracking-wide">Current Due Date</p>
                                    <p id="renew-current-due-date" class="font-semibold text-gray-800"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 uppercase tracking-wide">Times Renewed</p>
                                    <p id="renew-current-due-date" class="font-semibold text-gray-800">0 / 2</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Due Date Selection -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mt-4">
                    <label for="renew-due-date" class="block text-xs font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        New Due Date
                    </label>
                    <input type="date" id="new-due-date" name="new-due-date" value="2025-08-10" class="w-full border border-gray-300 bg-white rounded-lg px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition">
                    <div id="new-due-date-error-placeholder" class="error-placeholder"></div>
                    <p class="text-xs text-gray-600 mt-2"><span id="renew-borrow-duration"></span></p>
                </div>
            </div>
            <!-- Footer Actions -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                <button id="renew-cancel-button" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                    Cancel
                </button>
                <button id="renew-confirm-button" class="px-6 py-2.5 bg-secondary hover:bg-secondary/90 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Confirm Renewal
                </button>
            </div>
        </form>
    </div>
</div>
