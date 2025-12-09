<div id="lift-suspension-modal" class="fixed inset-0 flex items-center justify-center bg-background-unfocused hidden bg-opacity-0 z-70 transition-opacity duration-150">
    <div id="lift-suspension-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-lg my-8 overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                Lift Suspension
            </h2>
            <button id="close-lift-suspension-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset('build/assets/icons/close-gray.svg') }}" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-5">
            <!-- User Info Banner -->
            <div class="border border-gray-200 rounded-lg p-3 mb-4 flex items-center justify-between bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600  rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                        <span id="lift-suspension-initials">--</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Lifting suspension for</p>
                        <p id="lift-suspension-name" class="font-bold text-gray-900">--</p>
                        <p id="lift-suspension-id-number" class="text-xs text-gray-700 font-mono">User ID: --</p>
                    </div>
                </div>
                <span id="lift-suspension-role-badge"></span>
            </div>

            <form id="lift-suspension-form">
                <div class="mb-4">
                    <label for="lift-suspension-reason" class="block text-sm font-semibold text-gray-700 mb-2">Reason / Remarks (Optional)</label>
                    <textarea id="lift-suspension-reason" name="reason" rows="4" class="w-full bg-white border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm resize-none" placeholder="Enter remarks..."></textarea>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
            <button id="cancel-lift-btn" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                Cancel
            </button>
            <button id="confirm-lift-btn" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow flex items-center gap-2">
                Confirm Lift
            </button>
        </div>
    </div>
</div>
