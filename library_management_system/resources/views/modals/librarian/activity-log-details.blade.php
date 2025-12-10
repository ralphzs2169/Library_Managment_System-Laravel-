{{-- filepath: c:\Users\Angela\library_management_system\resources\views\modals\activity-log-details.blade.php --}}
<div id="activity-log-details-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused bg-opacity-0 hidden z-70 transition-opacity duration-150" role="dialog" aria-modal="true">
    <div id="activity-log-details-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-2xl my-8 max-h-[90vh] overflow-hidden transform scale-95 opacity-0 transition-all duration-150 flex flex-col">
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-black flex items-center gap-2">

                Activity Log Details
            </h2>
            <button id="close-activity-log-details" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5 overflow-y-auto">
            <!-- User Info Banner -->
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="log-user-initials">--</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Performed by</p>
                        <p id="log-user-name" class="font-bold text-gray-900"></p>
                        <p id="log-user-id" class="text-xs text-gray-500 font-mono"></p>
                    </div>
                </div>
                <span id="log-user-role-badge"></span>
            </div>

            <!-- Activity Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-200">Activity Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Action</p>
                        <p id="log-action" class="font-semibold text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Entity Type</p>
                        <p id="log-entity-type" class="font-semibold text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Entity ID</p>
                        <p id="log-entity-id" class="font-semibold text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Timestamp</p>
                        <p id="log-timestamp" class="font-semibold text-gray-800"></p>
                    </div>
                </div>
            </div>

            <!-- Details Section -->
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-200">Activity Details</h3>
                <p id="log-details" class="text-sm text-gray-800 leading-relaxed"></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end">
            <button id="close-activity-log-button" class="px-6 py-2.5 cursor-pointer bg-secondary-light hover:bg-secondary-light/90 text-white rounded-lg font-medium transition shadow-sm">
                Close
            </button>
        </div>
    </div>
</div>
