{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\settings.blade.php --}}
<x-layout>
    <section class="md:pl-78 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-6">
            <div class="flex items-center">
                <img src="{{ asset('build/assets/icons/settings.svg') }}" alt="Settings Icon" class="inline-block w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
            </div>
        </div>

        <form id="settings-form" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            <!-- Borrowing Rules -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <img src="{{ asset('build/assets/icons/borrowing.svg') }}" alt="Borrowing Icon" class="w-6 h-6">
                        Borrowing Rules
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Max Books (Student) -->
                        <div class="field-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Books per Student</label>
                            <input type="number" name="max_books_per_student" value="{{ $settings['borrowing.max_books_per_student'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="10">
                            <div class="error-placeholder" id="max_books_per_student-error-placeholder"></div>
                            <p class="mt-1 text-xs text-gray-500">Maximum number of books a student can borrow</p>
                        </div>

                        <!-- Max Books (Teacher) -->
                        <div class="field-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Books per Teacher</label>
                            <input type="number" name="max_books_per_teacher" value="{{ $settings['borrowing.max_books_per_teacher'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="20">
                            <div class="error-placeholder" id="max_books_per_teacher-error-placeholder"></div>
                            <p class="mt-1 text-xs text-gray-500">Maximum number of books a teacher can borrow</p>
                        </div>

                        <!-- Borrow Duration -->
                        <div class="field-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Borrow Duration (Days)</label>
                            <input type="number" name="borrow_duration" value="{{ $settings['borrowing.borrow_duration'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="90">
                            <div class="error-placeholder" id="borrow_duration-error-placeholder"></div>
                            <p class="mt-1 text-xs text-gray-500">Default number of days for book borrowing</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penalty Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <img src="{{ asset('build/assets/icons/total-fines.svg') }}" alt="Penalty Icon" class="w-6 h-6">
                        Penalty & Fine Settings
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Rate Per Day -->
                        <div class="field-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Overdue Rate (₱/day)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                                <input type="number" step="0.01" name="rate_per_day" value="{{ $settings['penalty.rate_per_day'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm pl-8 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="0" max="100">
                            </div>
                            <div class="error-placeholder" id="rate_per_day-error-placeholder"></div>
                            <p class="mt-1 text-xs text-gray-500">Penalty charged per day overdue</p>
                        </div>

                        <!-- Max Penalty -->
                        <div class="field-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Penalty (₱)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                                <input type="number" step="0.01" name="max_amount" value="{{ $settings['penalty.max_amount'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm pl-8 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="0">
                            </div>
                            <div class="error-placeholder" id="max_amount-error-placeholder"></div>
                            <p class="mt-1 text-xs text-gray-500">Maximum penalty amount cap</p>
                        </div>

                        <!-- Lost Book Multiplier -->
                        <div class="field-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lost Book Multiplier</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="lost_fee_multiplier" value="{{ $settings['penalty.lost_fee_multiplier'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="0" max="5">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">×</span>
                            </div>
                            <div class="error-placeholder" id="lost_fee_multiplier-error-placeholder"></div>
                            <p class="mt-1 text-xs text-gray-500">Multiply book price for lost items</p>
                        </div>

                        <!-- Damaged Book Multiplier -->
                        <div class="field-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Damaged Book Multiplier</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="damaged_fee_multiplier" value="{{ $settings['penalty.damaged_fee_multiplier'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="0" max="5">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">×</span>
                            </div>
                            <div class="error-placeholder" id="damaged_fee_multiplier-error-placeholder"></div>
                            <p class="mt-1 text-xs text-gray-500">Multiply book price for damaged items</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notification Settings
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Reminder Days Before Due -->
                        <div class="field-container">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reminder Days Before Due</label>
                            <input type="number" name="reminder_days_before_due" value="{{ $settings['notifications.reminder_days_before_due'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="14">
                            <div class="error-placeholder" id="reminder_days_before_due-error-placeholder"></div>
                            <p class="mt-1 text-xs text-gray-500">Days before due date to send reminder</p>
                        </div>

                        <!-- Enable Borrower Notifications -->
                        <div class="field-container flex items-center h-full">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" name="enable_borrower_notifications" value="1" {{ ($settings['notifications.enable_borrower_notifications'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-accent transition-colors"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Enable Borrower Notifications</span>
                            </label>
                        </div>

                        <!-- Show Overdue Notifications -->
                        <div class="field-container flex items-center h-full">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" name="show_overdue_notifications" value="1" id="show_overdue_notifications" {{ ($settings['notifications.show_overdue_notifications'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-accent transition-colors"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Show Overdue Notifications</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pb-6">
                <button type="reset" class="px-6 py-2.5 border border-accent text-accent rounded-sm font-medium hover:bg-accent hover:text-white transition-all">
                    Reset Changes
                </button>
                <button type="submit" class="px-6 py-2.5 bg-accent text-white rounded-sm font-medium hover:opacity-90 transition-all shadow-sm hover:shadow">
                    Save Settings
                </button>
            </div>
        </form>
    </section>
</x-layout>

@vite('resources/js/pages/librarian/settings.js')
