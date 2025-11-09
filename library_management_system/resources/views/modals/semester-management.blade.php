<!-- Add Semester Modal -->
<div id="add-semester-modal" class="fixed inset-0 bg-background-unfocused hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="bg-accent/10 p-2 rounded-lg">
                    <img src="{{ asset('build/assets/icons/semester-black.svg') }}" alt="Semester" class="w-5 h-5">
                </div>
                <h2 class="text-xl font-bold text-gray-900">Add New Semester</h2>
            </div>
            <button id="close-add-semester-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="add-semester-form" novalidate class="p-6 space-y-5">
            @csrf
            <!-- Semester Name -->
            <div>
                <label for="semester_name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Semester Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="semester_name" name="semester_name" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-accent focus:border-accent transition" placeholder="e.g., First Semester 2024-2025">
                <div id="semester_name-error-placeholder" class="error-placeholder"></div>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="semester_start" class="block text-sm font-semibold text-gray-700 mb-2">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="semester_start" name="semester_start" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-accent focus:border-accent transition">
                    <div id="semester_start-error-placeholder" class="error-placeholder"></div>
                </div>

                <!-- End Date -->
                <div>
                    <label for="semester_end" class="block text-sm font-semibold text-gray-700 mb-2">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="semester_end" name="semester_end" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-accent focus:border-accent transition">
                    <div id="semester_end-error-placeholder" class="error-placeholder"></div>
                </div>
            </div>

            <!-- Info Note -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-blue-800">
                        Only one semester can be active at a time. Adding a new semester will not automatically activate it.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="cancel-add-semester" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent/90 transition-colors shadow-sm hover:shadow">
                    Add Semester
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Semester Modal -->
<div id="edit-semester-modal" class="fixed inset-0 bg-background-unfocused hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="bg-accent/10 p-2 rounded-lg">
                    <img src="{{ asset('build/assets/icons/semester-black.svg') }}" alt="Semester" class="w-5 h-5">
                </div>
                <h2 class="text-xl font-bold text-gray-900">Edit Semester</h2>
            </div>
            <button id="close-edit-semester-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="edit-semester-form" novalidate class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="edit_semester_id" name="semester_id">

            <!-- Semester Name -->
            <div>
                <label for="edit_semester_name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Semester Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit_semester_name" name="semester_name" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-accent focus:border-accent transition" placeholder="e.g., First Semester 2024-2025">
                <div id="edit_semester_name-error-placeholder" class="error-placeholder"></div>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="edit_semester_start" class="block text-sm font-semibold text-gray-700 mb-2">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="edit_semester_start" name="semester_start" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-accent focus:border-accent transition">
                    <div id="edit_semester_start-error-placeholder" class="error-placeholder"></div>
                </div>

                <!-- End Date -->
                <div>
                    <label for="edit_semester_end" class="block text-sm font-semibold text-gray-700 mb-2">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="edit_semester_end" name="semester_end" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-accent focus:border-accent transition">
                    <div id="edit_semester_end-error-placeholder" class="error-placeholder"></div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="cancel-edit-semester" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent/90 transition-colors shadow-sm hover:shadow">
                    Update Semester
                </button>
            </div>
        </form>
    </div>
</div>
