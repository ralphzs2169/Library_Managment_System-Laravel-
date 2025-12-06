<div id="confirm-reservation-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-50 transition-opacity duration-150">
    <div id="confirm-reservation-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="back-to-reserve-book-button" class="cursor-pointer text-gray-600 hover:text-gray-800 transition">
                    <img src="{{ asset('build/assets/icons/back-gray.svg') }}" alt="Back" class="w-6 h-6">
                </button>
                <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                    Review Reservation Details
                </h2>
            </div>
            <button id="close-confirm-reserve-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5 overflow-y-auto">
            <!-- Borrower Info Banner -->
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="confirm-reserver-initials">--</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Reservation for</p>
                        <p id="confirm-reserver-name" class="font-bold text-gray-900"></p>
                        <p id="confirm-reserver-id" class="text-xs text-gray-500 font-mono"></p>
                    </div>
                </div>
                <span id="reserver-role-badge"></span>
            </div>

            <!-- Book Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <label class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" class="size-5 text-accent">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>

                    Book Information
                </label>
                <div class="flex gap-4">

                    <img id="confirm-reserve-book-cover" src="" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">

                    <div class="flex-1 space-y-2">
                        <div>
                            <h4 id="confirm-reserve-book-title" class="line-clamp-2 text-lg font-bold text-gray-900"></h4>
                            <p id="confirm-reserve-book-author" class="text-xs text-gray-600"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">ISBN</p>
                                <p id="confirm-reserve-book-isbn" class="font-semibold text-gray-800 font-mono text-[10px]"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Year</p>
                                <p id="confirm-reserve-book-year" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Category</p>
                                <p id="confirm-reserve-book-category" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Genre</p>
                                <p id="confirm-reserve-book-genre" class="font-semibold text-gray-800"></p>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Info Note -->
            <div class="bg-blue-50 border border-blue-200 mt-4 rounded-lg p-3">
                <div class="flex gap-2 items-center">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p id="reservation-queue-position" class="text-xs text-blue-800 ">

                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
            <button id="cancel-reserve-button" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                Cancel
            </button>
            <button id="confirm-reserve-button" class="px-6 py-2.5 bg-secondary bg-secondary/90 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Confirm Reservation
            </button>
        </div>
    </div>
</div>
