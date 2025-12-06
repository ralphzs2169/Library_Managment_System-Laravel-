<div id="reservation-record-details-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-50 transition-opacity duration-150">
    <div id="reservation-record-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">

        {{-- Modal Header --}}
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                    Reservation Record Details
                    <span id="reservation-transaction-id" class="text-sm text-gray-500 font-mono">#ID</span>
                </h2>
            </div>
            <button id="close-reservation-record-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        {{-- Modal Body (Scrollable Content) --}}
        <div class="px-6 py-5 overflow-y-auto">

            {{-- Borrower Details Section --}}
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="reservation-borrower-initials">AB</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Borrower</p>
                        <p id="reservation-borrower-name" class="font-bold text-gray-900">Name Loading...</p>
                        <p id="reservation-borrower-id-number" class="text-xs text-gray-700 font-mono">User ID: --</p>
                    </div>
                </div>
                <span id="reservation-borrower-role-badge"></span>
            </div>


            {{-- Book & Reservation Information Section --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 relative">
                <div id="reservation-record-status-badge" class="absolute top-3 right-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800 shadow-sm">
                        Status Here
                    </span>
                </div>

                <label class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.247m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.247" />
                    </svg>
                    Book & Reservation Details
                </label>

                <div class="flex gap-4">
                    <img id="reservation-record-book-cover" src="https://placehold.co/96x144/f0f0f0/808080?text=Cover" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">

                    <div class="flex-1 space-y-2">
                        <div>
                            <h4 id="reservation-record-book-title" class="text-lg font-bold text-gray-900">Book Title Loading...</h4>
                            <p id="reservation-record-book-author" class="text-xs text-gray-600">Author Name</p>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-sm">
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide text-xs">Reserved At</p>
                                <p id="reservation-record-reserved-at" class="font-semibold text-gray-800 text-sm">--</p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide text-xs">Queue Position</p>
                                <p id="reservation-record-queue-position" class="font-bold text-lg text-blue-600">#N/A</p>
                            </div>
                            <div class="col-span">
                                <p class="text-gray-500 uppercase tracking-wide text-xs">Assigned Copy Number</p>
                                <p id="reservation-record-confirmed-copy-number" class="font-semibold text-gray-800 font-mono text-sm">-- (Pending Confirmation)</p>
                            </div>
                            <div class="col-span">
                                <p class="text-gray-500 uppercase tracking-wide text-xs">Reservation Created by</p>
                                <p id="reservation-record-created-by" class="font-semibold text-gray-800"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline & Deadlines Section --}}
            <div class=" bg-white border border-gray-200 rounded-xl p-4">
                <label class="block text-xs font-semibold text-gray-700 mb-3 flex items-center gap-2 border-gray-300 border-b pb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Timeline & Deadlines
                </label>

                <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                    <div id="reservation-completion-date-container">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Completion Date</p>
                        <p id="reservation-record-completed-at" class="font-semibold text-sm text-gray-800">-- (Pending)</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pickup Deadline</p>
                        <p id="reservation-record-pickup-deadline" class="font-bold text-sm text-red-600">-- (N/A)</p>
                    </div>
                    <div class="col-span-1">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Cancelled At</p>
                        <p id="reservation-record-cancelled-at" class="font-semibold text-sm text-gray-800">--</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Footer (Action Buttons) --}}
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">


            <button id="checkout-reserved-book-button" class="items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 cursor-pointer transition-all shadow-sm hidden">
                <img src="{{ asset('build/assets/icons/add-book-white.svg') }}" alt="Check out Book Icon" class="w-5 h-5">
                Check Out Book
            </button>


            {{-- 2. SECONDARY ACTION: CANCEL/NO-SHOW (Conditional) --}}
            <button id="cancel-reservation-button" class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 cursor-pointer transition-all shadow-sm hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel Reservation
            </button>


            {{-- 3. NEUTRAL ACTION --}}
            <button id="close-reservation-details-button" class="px-6 py-2.5 border bg-secondary text-white rounded-lg font-medium hover:bg-secondary/90 cursor-pointer transition-all shadow-sm">
                Close
            </button>
        </div>
    </div>
</div>
