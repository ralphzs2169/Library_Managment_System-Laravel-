<div id="borrow-record-details-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-50 transition-opacity duration-150">
    <div id="borrow-record-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                    Borrowing Transaction Details
                    <span id="transaction-id" class="text-sm text-gray-500 font-mono">#ID</span>
                </h2>
            </div>
            <button id="close-borrow-record-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <div class="px-6 py-5 overflow-y-auto">

            {{-- Borrower Details Section --}}
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="borrower-initials">AB</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Borrower</p>
                        <p id="borrower-name" class="font-bold text-gray-900">Name Loading...</p>
                        <p id="borrower-id-number" class="text-xs text-gray-700 font-mono">User ID: --</p>

                    </div>
                </div>
                <span id="borrower-role-badge"></span>
            </div>


            {{-- Book Information Section  --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 relative">
                <div id="record-status-badge" class="absolute top-3 right-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800 shadow-sm">
                        Status Here
                    </span>
                </div>

                <label for="renew-due-date" class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Book Information
                </label>

                <div class="flex gap-4">
                    <img id="record-book-cover" src="https://placehold.co/96x144/f0f0f0/808080?text=Cover" alt="Book Cover" class="w-24 h-36 object-cover rounded-lg shadow-sm border border-gray-200 flex-shrink-0">

                    <div class="flex-1 space-y-2">
                        <div>
                            <h4 id="record-book-title" class="text-lg font-bold text-gray-900">Book Title Loading...</h4>
                            <p id="record-book-author" class="text-xs text-gray-600">Author Name</p>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Copy No.</p>
                                <p id="record-book-copy-id" class="font-semibold text-gray-800 font-mono text-sm"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Category</p>
                                <p id="record-book-category" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 uppercase tracking-wide">Genre</p>
                                <p id="record-book-genre" class="font-semibold text-gray-800"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <label class="block text-xs font-semibold text-gray-700 mb-3 flex items-center gap-2 border-gray-300 border-b pb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Transaction Timeline
                </label>

                <div class="grid grid-cols-2 gap-y-4 gap-x-6">

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Associated Semester</p>
                        <p id="record-semester-name" class="font-semibold text-sm  text-gray-800">S-001</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Times Renewed</p>
                        <p id="record-renewed-count" class="font-semibold text-sm  text-gray-800">0</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Borrowed At</p>
                        <p id="record-borrowed-at" class="font-semibold text-sm  text-gray-800">YYYY-MM-DD</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Due Date</p>
                        <p id="record-due-at" class="font-semibold text-sm  text-red-600">YYYY-MM-DD</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Returned At</p>
                        <p id="record-returned-at" class="font-semibold text-sm  text-gray-800">-- (Pending)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">

            <button id="close-borrow-details-button" class="px-6 py-2.5 border bg-secondary text-white rounded-lg font-medium hover:bg-secondary/90 cursor-pointer transition-all shadow-sm">
                Close
            </button>
        </div>
    </div>
</div>
