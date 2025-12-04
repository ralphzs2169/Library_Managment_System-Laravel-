<div id="penalty-record-details-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-50 transition-opacity duration-150">
    <div id="penalty-record-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                    Penalty Record Details
                    <span id="penalty-transaction-id" class="text-sm text-gray-500 font-mono">#ID</span>
                </h2>
            </div>
            <button id="close-penalty-record-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <div class="px-6 py-5 overflow-y-auto">

            {{-- Borrower Details Section --}}
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="penalty-borrower-initials">AB</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Borrower</p>
                        <p id="penalty-borrower-name" class="font-bold text-gray-900">Name Loading...</p>
                        <p id="penalty-borrower-id-number" class="text-xs text-gray-700 font-mono">User ID: --</p>
                    </div>
                </div>
                <span id="penalty-borrower-role-badge"></span>
            </div>


            {{-- Penalty & Book Information Section --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 relative">
                <div id="penalty-record-status-badge" class="absolute top-3 right-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800 shadow-sm">
                        Status Here
                    </span>
                </div>

                <label class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2a2.992 2.992 0 01-3-2c0-1.105 1.343-2 3-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Penalty & Item Details
                </label>

                <div class="flex gap-4">
                    <img id="penalty-record-book-cover" src="https://placehold.co/96x144/f0f0f0/808080?text=Cover" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">

                    <div class="flex-1 space-y-2">
                        <div>
                            <h4 id="penalty-record-book-title" class="text-lg font-bold text-gray-900">Book Title Loading...</h4>
                            <p id="penalty-record-book-author" class="text-xs text-gray-600">Author Name</p>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Copy No.</p>
                                <p id="penalty-record-book-copy-id" class="font-semibold text-gray-800 font-mono text-sm"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Penalty Reason</p>
                                <p id="penalty-record-reason" class="font-semibold text-red-700"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Associated Borrow ID</p>
                                <p id="penalty-record-assoc-borrow-id" class="font-semibold text-gray-800 font-mono"></p>
                            </div>
                            <div class="col-span-1 hidden" id="penalty-record-cancelled-at-container">
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Cancelled At</p>
                                <p id="penalty-record-cancelled-at" class="font-semibold text-sm  text-gray-800"></p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Financial & Timeline Section --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <label class="block text-xs font-semibold text-gray-700 mb-3 flex items-center gap-2 border-gray-300 border-b pb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Financial Timeline
                </label>

                <div class="grid grid-cols-2 gap-y-4 gap-x-6">

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Amount Due</p>
                        <p id="penalty-record-amount" class="font-bold text-lg text-red-600">₱ 0.00</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Remaining Balance</p>
                        <p id="penalty-record-remaining-amount" class="font-bold text-lg text-gray-800">₱ 0.00</p>
                    </div>

                    <div class="col-span-1">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Last Payment Date</p>
                        <p id="penalty-record-paid-at" class="font-semibold text-sm  text-gray-800">-- (Pending)</p>
                    </div>
                    <div class="col-span-1">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Issued at</p>
                        <p id="penalty-record-issued-at" class="font-semibold text-sm  text-gray-800">-- (Pending)</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">

            {{-- 1. PRIMARY ACTION (Hidden if Paid/Cancelled) --}}
            <button id="process-payment-button" class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 cursor-pointer transition-all shadow-sm hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
                Process Payment
            </button>

            {{-- 2. DESTRUCTIVE/SECONDARY ACTION (Hidden if Paid/Cancelled) --}}
            {{-- Place this next to Process Payment and before Close. Use a strong color (red) and clear icon. --}}
            <button id="cancel-penalty-button" class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 cursor-pointer transition-all shadow-sm hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel Penalty
            </button>

            {{-- 3. NEUTRAL ACTION --}}
            <button id="close-penalty-details-button" class="px-6 py-2.5 border bg-secondary text-white rounded-lg font-medium hover:bg-secondary/90 cursor-pointer transition-all shadow-sm">
                Close
            </button>
        </div>
    </div>
</div>
