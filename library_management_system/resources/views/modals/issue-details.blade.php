{{-- filepath: c:\Users\Angela\library_management_system\resources\views\modals\issue-details.blade.php --}}
<div id="issue-details-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused bg-opacity-0 z-50 transition-opacity duration-150 hidden" role="dialog" aria-modal="true">
    <div id="issue-details-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">

                <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                    Issue Report Details
                </h2>
            </div>
            <button id="close-issue-details-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5 overflow-y-auto">
            <!-- Report Type & Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-2">Report Type</p>
                        <div id="report-type"></div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-2">Status</p>
                        <div id="report-status"></div>
                    </div>
                </div>
            </div>

            <!-- Book & Copy Information -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
                <label class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                    Copy Information
                </label>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Copy Number</p>
                        <p id="report-copy-number" class="text-sm font-semibold text-gray-800 font-mono">—</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Reported Date</p>
                        <p id="reported-date" class="text-sm font-semibold text-gray-800">—</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
                <label class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Description
                </label>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <p id="report-description" class="text-sm text-gray-700 leading-relaxed">—</p>
                </div>
            </div>

            <!-- Reporter & Borrower Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <label class="block text-xs font-semibold text-gray-700 pb-2 border-gray-300 border-b mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    People Involved
                </label>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Reported By</p>
                        <p id="reported-by" class="text-sm font-semibold text-gray-800">—</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Borrower</p>
                        <p id="borrower-name" class="text-sm font-semibold text-gray-800">—</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
            <button id="close-issue-details-btn" class="px-6 py-2.5 border bg-secondary-light text-white rounded-lg font-medium hover:bg-secondary/90 cursor-pointer transition-all shadow-sm">
                Close
            </button>
        </div>
    </div>
</div>
