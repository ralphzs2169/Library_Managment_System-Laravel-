<div id="confirm-borrow-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-50 transition-opacity duration-150">
    <div id="confirm-borrow-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="back-to-borrow-book-button" class="cursor-pointer text-gray-600 hover:text-gray-800 transition">
                    <img src="{{ asset('build/assets/icons/back-gray.svg') }}" alt="Back" class="w-6 h-6">
                </button>
                <h2 id="confirm-borrow-title" class="text-xl font-semibold text-black flex items-center gap-2">

                    Finalize Borrowing Details
                </h2>
            </div>
            <button id="close-confirm-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5 overflow-y-auto">
            <!-- Borrower Info Banner -->
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="confirm-borrower-initials">--</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Borrowing for</p>
                        <p id="confirm-borrower-name" class="font-bold text-gray-900"></p>
                    </div>
                </div>
                <p id="confirm-borrower-id" class="text-xs text-gray-500 font-mono">1352085</p>
            </div>

            <!-- Book Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex gap-4">
                    <!-- Book Cover -->
                    <img id="confirm-book-cover" src="" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">

                    <!-- Book Info -->
                    <div class="flex-1 space-y-2">
                        <div>
                            <h4 id="confirm-book-title" class="text-lg font-bold text-gray-900"></h4>
                            <p id="confirm-book-author" class="text-xs text-gray-600"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">ISBN</p>
                                <p id="confirm-book-isbn" class="font-semibold text-gray-800 font-mono text-[10px]"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Year</p>
                                <p id="confirm-book-year" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Category</p>
                                <p id="confirm-book-category" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Genre</p>
                                <p id="confirm-book-genre" class="font-semibold text-gray-800"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copy Number and Due Date Selection Row -->
            <div class="bg-white border border-gray-200 rounded-xl p-3 mt-4 flex flex-col gap-3">
                <div>
                    <label for="book_copy_id" class="block text-xs font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                        Copy Number
                    </label>
                    <div id="book_copy_id-error-placeholder" class="error-placeholder"></div>
                    <select id="book_copy_id" name="book_copy_id" class="w-full border border-gray-300 bg-white rounded-lg px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition">
                        <option value="">Loading...</option>
                    </select>
                    <p class="text-xs text-gray-600 mt-2">
                        <span id="copies-available-text">Loading available copies</span>
                    </p>
                </div>

                <div>
                    <label for="due_date" class="block text-xs font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Due Date
                    </label>
                    <div id="due_date-error-placeholder" class="error-placeholder"></div>
                    <input type="date" id="due_date" name="due_date" value="2025-08-10" class="w-full border border-gray-300 bg-white rounded-lg px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition">
                    <p class="text-xs text-gray-600 mt-2"><span id="borrow-duration"></span></p>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
            <button id="cancel-borrow-button" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                Cancel
            </button>
            <button id="confirm-borrow-button" class="px-6 py-2.5 bg-secondary bg-secondary/90 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Confirm Borrowing
            </button>
        </div>
    </div>
</div>
