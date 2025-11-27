<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <img src="{{ asset('build/assets/icons/settings.svg') }}" alt="Settings Icon" class="w-10 h-10">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Library Settings</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage library policies for borrowing, renewals, penalties, and notifications</p>
                    </div>
                </div>

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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Borrowing Config -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col gap-4">
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ asset('build/assets/icons/student.svg') }}" alt="Student Icon" class="w-5 h-5">
                                <h3 class="text-base font-semibold text-purple-700">Student Borrowing Settings</h3>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Max Books (Student) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Books per Student</label>
                                    <div class="error-placeholder flex-1 text-sm" id="max_books_per_student-error-placeholder"></div>
                                    <input id="max_books_per_student" type="number" name="max_books_per_student" value="{{ $settings['borrowing.max_books_per_student'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="10">
                                    <p class="mt-1 text-xs text-gray-500">Maximum number of books a student can borrow</p>
                                </div>
                                <!-- Borrow Duration (Student) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Borrow Duration (Days)</label>
                                    <div class="error-placeholder flex-1 text-sm" id="student_duration-error-placeholder"></div>
                                    <input id="student_duration" type="number" name="student_duration" value="{{ $settings['borrowing.student_duration'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="90">
                                    <p class="mt-1 text-xs text-gray-500">Default number of days for student borrowing</p>
                                </div>
                            </div>
                        </div>
                        <!-- Teacher Borrowing Config -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col gap-4">
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ asset('build/assets/icons/teacher.svg') }}" alt="Teacher Icon" class="w-5 h-5">
                                <h3 class="text-base font-semibold text-blue-700">Teacher Borrowing Settings</h3>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Max Books (Teacher) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Books per Teacher</label>
                                    <div class="error-placeholder flex-1 text-sm" id="max_books_per_teacher-error-placeholder"></div>
                                    <input id="max_books_per_teacher" type="number" name="max_books_per_teacher" value="{{ $settings['borrowing.max_books_per_teacher'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="20">
                                    <p class="mt-1 text-xs text-gray-500">Maximum number of books a teacher can borrow</p>
                                </div>
                                <!-- Borrow Duration (Teacher) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Borrow Duration (Days)</label>
                                    <div class="error-placeholder flex-1 text-sm" id="teacher_duration-error-placeholder"></div>
                                    <input id="teacher_duration" type="number" name="teacher_duration" value="{{ $settings['borrowing.teacher_duration'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="90">
                                    <p class="mt-1 text-xs text-gray-500">Default number of days for teacher borrowing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Renewing Rules -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <img src="{{ asset('build/assets/icons/renewing.svg') }}" alt="Renewing Icon" class="w-6 h-6">
                        Renewing Rules
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Renewal Config -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col gap-4">
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ asset('build/assets/icons/student.svg') }}" alt="Student Icon" class="w-5 h-5">
                                <h3 class="text-base font-semibold text-purple-700">Student Renewal Settings</h3>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Max Renewals (Student) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Renewals</label>
                                    <div class="error-placeholder flex-1 text-sm" id="student_renewal_limit-error-placeholder"></div>
                                    <input id="student_renewal_limit" type="number" name="student_renewal_limit" value="{{ $settings['renewing.student_renewal_limit'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="10">
                                    <p class="mt-1 text-xs text-gray-500">Maximum renewals allowed per borrow cycle.</p>
                                </div>
                                <!-- Renewal Duration (Student) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Renewal Duration (Days)</label>
                                    <div class="error-placeholder flex-1 text-sm" id="renewing_student_duration-error-placeholder"></div>
                                    <input id="renewing_student_duration" type="number" name="renewing_student_duration" value="{{ $settings['renewing.student_duration'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="90">
                                    <p class="mt-1 text-xs text-gray-500">Days added to due date per renewal.</p>
                                </div>
                                <!-- Min Days Before Renewal (Student) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Days Before Renewal</label>
                                    <div class="error-placeholder flex-1 text-sm" id="student_min_days_before_renewal-error-placeholder"></div>
                                    <input id="student_min_days_before_renewal" type="number" name="student_min_days_before_renewal" value="{{ $settings['renewing.student_min_days_before_renewal'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="90">
                                    <p class="mt-1 text-xs text-gray-500">Minimum days after borrowing before renewal is allowed.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Teacher Renewal Config -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col gap-4">
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ asset('build/assets/icons/teacher.svg') }}" alt="Teacher Icon" class="w-5 h-5">
                                <h3 class="text-base font-semibold text-blue-700">Teacher Renewal Settings</h3>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Max Renewals (Teacher) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Renewals</label>
                                    <div class="error-placeholder flex-1 text-sm" id="teacher_renewal_limit-error-placeholder"></div>
                                    <input id="teacher_renewal_limit" type="number" name="teacher_renewal_limit" value="{{ $settings['renewing.teacher_renewal_limit'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="10">
                                    <p class="mt-1 text-xs text-gray-500">Maximum renewals allowed per borrow cycle.</p>
                                </div>
                                <!-- Renewal Duration (Teacher) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Renewal Duration (Days)</label>
                                    <div class="error-placeholder flex-1 text-sm" id="renewing_teacher_duration-error-placeholder"></div>
                                    <input id="renewing_teacher_duration" type="number" name="renewing_teacher_duration" value="{{ $settings['renewing.teacher_duration'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="90">
                                    <p class="mt-1 text-xs text-gray-500">Days added to due date per renewal.</p>
                                </div>
                                <!-- Min Days Before Renewal (Teacher) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Days Before Renewal</label>
                                    <div class="error-placeholder flex-1 text-sm" id="teacher_min_days_before_renewal-error-placeholder"></div>
                                    <input id="teacher_min_days_before_renewal" type="number" name="teacher_min_days_before_renewal" value="{{ $settings['renewing.teacher_min_days_before_renewal'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="90">
                                    <p class="mt-1 text-xs text-gray-500">Minimum days after borrowing before renewal is allowed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservation Rules -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <img src="{{ asset('build/assets/icons/reservation.svg') }}" alt="Reservation Icon" class="w-6 h-6">
                        Reservation Rules
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Reservation Config -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col gap-4">
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ asset('build/assets/icons/student.svg') }}" alt="Student Icon" class="w-5 h-5">
                                <h3 class="text-base font-semibold text-purple-700">Student Reservation Settings</h3>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Pickup Window (Student) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Window (Days)</label>
                                    <div class="error-placeholder flex-1 text-sm" id="reservation_student_pickup_window_days-error-placeholder"></div>
                                    <input id="reservation_student_pickup_window_days" type="number" name="reservation_student_pickup_window_days" value="{{ $settings['reservation.student_pickup_window_days'] ?? '' }}" class="w-full bg-[#F2F2F2] border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent transition" min="1" max="14">
                                    <p class="mt-1 text-xs text-gray-500">Days allowed for student to pick up reserved book</p>
                                </div>
                                <!-- Max Pending Reservations (Student) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Pending Reservations</label>
                                    <div class="error-placeholder flex-1 text-sm" id="reservation_student_max_pending_reservations-error-placeholder"></div>
                                    <input id="reservation_student_max_pending_reservations" type="number" name="reservation_student_max_pending_reservations" value="{{ $settings['reservation.student_max_pending_reservations'] ?? '' }}" class="w-full bg-[#F2F2F2] border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent transition" min="1" max="20">
                                    <p class="mt-1 text-xs text-gray-500">Maximum pending reservations allowed for a student</p>
                                </div>
                            </div>
                        </div>
                        <!-- Teacher Reservation Config -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col gap-4">
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ asset('build/assets/icons/teacher.svg') }}" alt="Teacher Icon" class="w-5 h-5">
                                <h3 class="text-base font-semibold text-blue-700">Teacher Reservation Settings</h3>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Pickup Window (Teacher) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Window (Days)</label>
                                    <div class="error-placeholder flex-1 text-sm" id="reservation_teacher_pickup_window_days-error-placeholder"></div>
                                    <input id="reservation_teacher_pickup_window_days" type="number" name="reservation_teacher_pickup_window_days" value="{{ $settings['reservation.teacher_pickup_window_days'] ?? '' }}" class="w-full bg-[#F2F2F2] border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent transition" min="1" max="14">
                                    <p class="mt-1 text-xs text-gray-500">Days allowed for teacher to pick up reserved book</p>
                                </div>
                                <!-- Max Pending Reservations (Teacher) -->
                                <div class="field-container flex flex-col">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Pending Reservations</label>
                                    <div class="error-placeholder flex-1 text-sm" id="reservation_teacher_max_pending_reservations-error-placeholder"></div>
                                    <input id="reservation_teacher_max_pending_reservations" type="number" name="reservation_teacher_max_pending_reservations" value="{{ $settings['reservation.teacher_max_pending_reservations'] ?? '' }}" class="w-full bg-[#F2F2F2] border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent transition" min="1" max="20">
                                    <p class="mt-1 text-xs text-gray-500">Maximum pending reservations allowed for a teacher</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Queue Length -->
                    <div class="mt-6">
                        <div class="field-container flex flex-col max-w-md">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reservation Queue Max Length</label>
                            <div class="error-placeholder flex-1 text-sm" id="reservation_queue_max_length-error-placeholder"></div>
                            <input id="reservation_queue_max_length" type="number" name="reservation_queue_max_length" value="{{ $settings['reservation.queue_max_length'] ?? '' }}" class="w-full bg-[#F2F2F2] border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent transition" min="1" max="50">
                            <p class="mt-1 text-xs text-gray-500">Maximum number of reservations allowed in queue per book</p>
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
                        <div class="field-container flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Overdue Rate (₱/day)</label>
                            <div class="error-placeholder flex-1  text-sm" id="rate_per_day-error-placeholder"></div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                                <input id="rate_per_day" type="number" step="0.01" name="rate_per_day" value="{{ $settings['penalty.rate_per_day'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm pl-8 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="0" max="100">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Penalty charged per day overdue</p>
                        </div>

                        <!-- Max Penalty -->
                        <div class="field-container flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Penalty (₱)</label>
                            <div class="error-placeholder flex-1  text-sm" id="max_amount-error-placeholder"></div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                                <input id="max_amount" type="number" step="0.01" name="max_amount" value="{{ $settings['penalty.max_amount'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm pl-8 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="0">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maximum penalty amount cap</p>
                        </div>

                        <!-- Lost Book Multiplier -->
                        <div class="field-container flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lost Book Multiplier</label>
                            <div class="error-placeholder flex-1  text-sm" id="lost_fee_multiplier-error-placeholder"></div>
                            <div class="relative">
                                <input id="lost_fee_multiplier" type="number" step="0.01" name="lost_fee_multiplier" value="{{ $settings['penalty.lost_fee_multiplier'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="0" max="5">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">×</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Multiply book price for lost items</p>
                        </div>

                        <!-- Damaged Book Multiplier -->
                        <div class="field-container flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Damaged Book Multiplier</label>
                            <div class="error-placeholder flex-1  text-sm" id="damaged_fee_multiplier-error-placeholder"></div>
                            <div class="relative">
                                <input id="damaged_fee_multiplier" type="number" step="0.01" name="damaged_fee_multiplier" value="{{ $settings['penalty.damaged_fee_multiplier'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="0" max="5">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">×</span>
                            </div>
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
                            <div class="error-placeholder mb-1" id="reminder_days_before_due-error-placeholder"></div>
                            <input id="reminder_days_before_due" type="number" name="reminder_days_before_due" value="{{ $settings['notifications.reminder_days_before_due'] ?? '' }}" class="w-full bg-[#F2F2F2] font-extralight border border-[#B1B1B1] rounded-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" min="1" max="14">
                            <p class="mt-1 text-xs text-gray-500">Days before due date to send reminder</p>
                        </div>

                        <!-- Enable Borrower Notifications -->
                        <div class="field-container flex items-center h-full">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input id="enable_borrower_notifications" type="checkbox" name="enable_borrower_notifications" value="1" {{ ($settings['notifications.enable_borrower_notifications'] ?? false) ? 'checked' : '' }} class="sr-only peer">
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
