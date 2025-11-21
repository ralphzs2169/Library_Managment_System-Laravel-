<div id="issue-details-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused bg-opacity-0 z-50 transition-opacity duration-150 hidden" role="dialog" aria-modal="true">
    <div id="issue-details-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-lg transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->


        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4 flex items-center justify-between  rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-500/10 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">

                    Issue Report Details
                </h2>
            </div>
            <button id="close-issue-details-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5">
            <div class="space-y-4">
                <!-- Report Type & Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1.5">Report Type</p>
                        <div id="report-type"></div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1.5">Status</p>
                        <div id="report-status"></div>
                    </div>
                </div>

                <!-- Copy Number & Date -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1.5">Copy Number</p>
                        <p id="report-copy-number" class="text-sm font-semibold text-gray-800">—</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1.5">Reported Date</p>
                        <p id="reported-date" class="text-sm font-medium text-gray-700">—</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1.5">Description</p>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <p id="report-description" class="text-sm text-gray-700 leading-relaxed">—</p>
                    </div>
                </div>

                <!-- Reporter & Borrower Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1.5">Reported By</p>
                        <p id="reported-by" class="text-sm font-medium text-gray-700">—</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1.5">Borrower</p>
                        <p id="borrower-name" class="text-sm font-medium text-gray-700">—</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end rounded-b-2xl">
            <button id="close-issue-details-btn" class="cursosr-pointer px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-all shadow-sm">
                Close
            </button>
        </div>
    </div>
</div>
