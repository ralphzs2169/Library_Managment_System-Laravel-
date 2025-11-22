<div id="confirm-payment-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-70 transition-opacity duration-150">
    <div id="confirm-payment-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="back-to-borrower-profile-button" class="cursor-pointer text-gray-600 hover:text-gray-800 transition">
                    <img src="{{ asset('build/assets/icons/back-gray.svg') }}" alt="Back" class="w-6 h-6">
                </button>
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    {{-- <img src="{{ asset('build/assets/icons/payment-accent.svg') }}" alt="Payment Icon" class="w-8 h-8"> --}}
                    Confirm Payment
                </h2>
            </div>
            <button id="close-confirm-payment-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5 overflow-y-auto">
            <!-- Borrower Info Banner -->
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm flex-shrink-0">
                        <span id="confirm-payment-borrower-initials">--</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Paying for</p>
                        <p id="confirm-payment-borrower-name" class="font-bold text-gray-900"></p>
                    </div>
                </div>
                <p id="confirm-payment-borrower-id" class="text-xs text-gray-500 font-mono"></p>
            </div>

            <!-- Penalty Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 relative">

                {{-- Status Badge --}}
                <div id="confirm-payment-penalty-status" class="absolute top-3 right-3"></div>

                <div class="flex gap-4">
                    <!-- Book Cover -->
                    <img id="confirm-payment-book-cover" src="" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">

                    <!-- Book Info & Penalty -->
                    <div class="flex-1 space-y-2">
                        <div>
                            <h4 id="confirm-payment-book-title" class="text-lg font-bold text-gray-900"></h4>
                            <p id="confirm-payment-book-author" class="text-xs text-gray-600"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">ISBN</p>
                                <p id="confirm-payment-book-isbn" class="font-semibold text-gray-800 font-mono text-[10px]"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Copy No.</p>
                                <p id="confirm-payment-copy-number" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Penalty Type</p>
                                <p id="confirm-payment-penalty-type" class="font-semibold text-red-600"></p>
                            </div>
                            <div>
                                <p id="amount-label" class="text-gray-500 uppercase tracking-wide">Amount</p>
                                <p id="confirm-payment-penalty-amount" class="font-semibold text-red-600"></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Payment Input Section -->
            <form id="confirm-payment-form" novalidate>
                @csrf
                <p class="text-sm font-semibold text-gray-700 mb-2">Enter Amount to Pay</p>
                <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 ">
                    <div class="flex items-center gap-2">
                        <div class="field-container w-full">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">₱</span>
                                <input type="number" step="0.01" id="amount" placeholder="Enter amount to pay" name="amount" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 pl-8 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                            </div>
                            <div class="error-placeholder text-xs mt-1" id="amount-error-placeholder"></div>
                        </div>
                    </div>
                </div>

                <!-- Add the confirm button here -->
                <div>
                    <div class="flex justify-end gap-3 mt-4">
                        <button id="cancel-payment-button" type="button" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                            Cancel
                        </button>
                        <button id="confirm-payment-button" type="submit" class="px-6 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Confirm Payment
                        </button>
                    </div>
            </form>
        </div>


    </div>
</div>
