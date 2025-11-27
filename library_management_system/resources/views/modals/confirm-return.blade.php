{{-- filepath: c:\Users\Angela\library_management_system\resources\views\modals\confirm-return.blade.php --}}
<div id="confirm-return-modal" class="fixed inset-0 flex items-center hidden justify-center bg-background-unfocused bg-opacity-0 z-70 transition-opacity duration-150" role="dialog" aria-modal="true" aria-labelledby="confirm-return-title">
    <div id="confirm-return-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden transform scale-95 opacity-0 transition-all duration-150 flex flex-col">
        <!-- Header -->
        <div class="bg-secondary px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="back-to-profile-button" class="cursor-pointer text-gray-600 hover:text-gray-800 transition">
                    <img src="{{ asset('build/assets/icons/back.svg') }}" alt="Back" class="w-6 h-6">
                </button>
                <h2 class="text-xl font-semibold text-white">Confirm Return</h2>
            </div>
            <button id="close-confirm-return" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5 overflow-y-auto">
            <!-- Borrower Info Banner -->
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="confirm-return-borrower-initials">--</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Returning for</p>
                        <p id="confirm-return-borrower-name" class="font-bold text-gray-900"></p>
                    </div>
                </div>
                <p id="confirm-return-borrower-id" class="text-xs text-gray-500 font-mono"></p>
            </div>

            <!-- Book Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 relative">
                <!-- Status Badge (top-right) -->
                <div id="confirm-return-status-badge" class="absolute top-3 right-3"></div>

                <div class="flex gap-4">
                    <!-- Book Cover -->
                    <img id="confirm-return-book-cover" src="" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">

                    <!-- Book Info -->
                    <div class="flex-1 space-y-2">
                        <div>
                            <h4 id="confirm-return-book-title" class="text-lg font-bold text-gray-900"></h4>
                            <p id="confirm-return-book-author" class="text-xs text-gray-600"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">ISBN</p>
                                <p id="confirm-return-book-isbn" class="font-semibold text-gray-800 font-mono text-[10px]"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Copy No.</p>
                                <p id="confirm-return-copy-number" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Borrowed</p>
                                <p id="confirm-return-borrowed-date" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Due Date</p>
                                <p id="confirm-return-due-date" class="font-semibold text-gray-800"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Damaged Section -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mt-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" id="report_damaged" name="report_damaged" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 focus:ring-2 cursor-pointer">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Report book as damaged</span>
                    </div>
                </label>
                <p class="text-xs text-gray-600 mt-2 ml-8">Check this if the book is returned in damaged condition</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
            <button id="cancel-return-button" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                Cancel
            </button>
            <button id="confirm-return-button" class="px-6 py-2.5 bg-secondary hover:bg-secondary/90 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Confirm Return
            </button>
        </div>
    </div>
</div>
