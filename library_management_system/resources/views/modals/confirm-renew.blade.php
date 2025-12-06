<div id="confirm-renew-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-70 transition-opacity duration-150">
    <div id="confirm-renew-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="renew-back-to-borrower-profile" class="cursor-pointer text-gray-600 hover:text-gray-800 transition">
                    <img src="{{ asset('build/assets/icons/back-gray.svg') }}" alt="Back" class="w-6 h-6">
                </button>
                <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                    Finalize Renewal Details
                </h2>
            </div>
            <button id="renew-close-confirm-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>


        <!-- Content -->
        <div class="px-6 py-5 overflow-y-auto">
            <!-- Renewer Info Banner -->
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">

                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="renewer-initials">AB</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Borrower</p>
                        <p id="renewer-name" class="font-bold text-gray-900">Name Loading...</p>
                        <p id="renewer-id" class="text-xs text-gray-700 font-mono"></p>

                    </div>
                </div>
                <span id="renewer-role-badge"></span>
            </div>
            <!-- Book Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 relative">
                <!-- Status Badge -->
                <label class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-accent">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                    </svg>

                    Renew Status Summary
                </label>
                <div id="confirm-renew-status-badge" class="absolute top-3 right-3"></div>
                <div class="flex gap-4">
                    <!-- Book Cover -->
                    <img id="renew-book-cover" src="" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">
                    <!-- Book Info -->
                    <div class="flex-1 space-y-2">
                        <div>
                            <h4 id="renew-book-title" class="w-[80%] line-clamp-2 text-lg font-bold text-gray-900"></h4>
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
                                <p id="renew-times-renewed" class="font-semibold text-gray-800">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="confirm-renew-form" novalidate>
                @csrf
                <!-- Due Date Selection -->
                <div class="border border-gray-200 rounded-xl p-3 mt-4">
                    <label for="renew-due-date" class="block text-xs font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
            <button id="renew-confirm-button" class="px-6 py-2.5 cursor-pointer bg-secondary-light hover:bg-secondary-light/90 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Confirm Renewal
            </button>
        </div>
        </form>
    </div>
</div>
