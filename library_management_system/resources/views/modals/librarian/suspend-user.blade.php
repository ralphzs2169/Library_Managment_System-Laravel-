<div id="suspend-user-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-70 transition-opacity duration-150">
    <div id="suspend-user-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-lg my-8 overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                Suspend User
            </h2>
            <button id="close-suspend-user-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5">
            <!-- User Info Banner -->
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        <span id="suspend-user-initials">--</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Suspending</p>
                        <p id="suspend-user-name" class="font-bold text-gray-900">--</p>
                    </div>
                </div>
            </div>

            <form id="suspend-user-form">
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-semibold text-gray-700 mb-2">Reason for Suspension</label>
                    <textarea id="reason" name="reason" rows="4" class="w-full bg-white border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm resize-none" placeholder="Please provide a reason for suspending this user..."></textarea>
                    <div class="error-placeholder text-xs mt-1" id="reason-error-placeholder"></div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
            <button id="cancel-suspend-btn" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                Cancel
            </button>
            <button id="confirm-suspend-btn" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow flex items-center gap-2">
                Confirm Suspension
            </button>
        </div>
    </div>
</div>
