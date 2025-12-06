<div id="clearance-record-details-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-50 transition-opacity duration-150">
    <div id="clearance-record-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">

        {{-- Header --}}
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                    Clearance Record Details
                    <span id="clearance-transaction-id" class="text-sm text-gray-500 font-mono">#ID</span>
                </h2>
            </div>
            <button id="close-clearance-record-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 overflow-y-auto">

            {{-- Borrower Details --}}
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="clearance-borrower-initials">AB</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Borrower</p>
                        <p id="clearance-borrower-name" class="font-bold text-gray-900">Name Loading...</p>
                        <p id="clearance-borrower-id-number" class="text-xs text-gray-700 font-mono">User ID: --</p>
                    </div>
                </div>
                <span id="clearance-borrower-role-badge"></span>
            </div>

            {{-- Clearance Info --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 relative">
                <div id="clearance-record-status-badge" class="absolute top-3 right-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gray-100 text-gray-800 shadow-sm">
                        Status Here
                    </span>
                </div>

                <label class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Clearance Information
                </label>

                <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm mt-3">
                    <div>
                        <p class="text-gray-500 uppercase tracking-wide text-xs">Semester</p>
                        <p id="clearance-record-semester" class="font-semibold text-gray-800">--</p>
                    </div>
                    <div>
                        <p class="text-gray-500 uppercase tracking-wide text-xs">Requested By</p>
                        <p id="clearance-record-requested-by" class="font-semibold text-gray-800">--</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-500 uppercase tracking-wide text-xs">Remarks</p>
                        <p id="clearance-record-remarks" class="font-medium text-gray-700 italic">--</p>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <label class="block text-xs font-semibold text-gray-700 mb-3 flex items-center gap-2 border-gray-300 border-b pb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Timeline
                </label>

                <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Date Requested</p>
                        <p id="clearance-record-requested-at" class="font-semibold text-sm text-gray-800">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Date Processed</p>
                        <p id="clearance-record-processed-at" class="font-semibold text-sm text-gray-800">-- (Pending)</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Processed By</p>
                        <p id="clearance-record-processed-by" class="font-semibold text-sm text-gray-800">--</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">

            <button id="approve-clearance-btn" class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 cursor-pointer transition-all shadow-sm hidden flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Approve
            </button>

            <button id="reject-clearance-btn" class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 cursor-pointer transition-all shadow-sm hidden flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject
            </button>

            <button id="close-clearance-details-button" class="px-6 py-2.5 border bg-secondary-light text-white rounded-lg font-medium hover:bg-secondary/90 cursor-pointer transition-all shadow-sm">
                Close
            </button>
        </div>
    </div>
</div>
