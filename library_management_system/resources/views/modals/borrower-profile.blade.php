<div id="borrower-profile-modal" class="fixed inset-0 flex items-center hidden justify-center bg-background-unfocused bg-opacity-0 z-50 transition-opacity duration-150">
    <div id="borrower-profile-content" class="bg-white rounded-2xl shadow-2xl w-[95%] max-w-6xl max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <img src="{{ asset("build/assets/icons/member-profile.svg")}}" alt="Borrower Profile Icon" class="w-6 h-6">
                Borrower Profile
            </h2>
            <button id="close-borrower-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset("build/assets/icons/close-gray.svg")}}" alt="Close Borrower Profile Modal" class="w-6 h-6">
            </button>
        </div>

        <!-- Scrollable Content -->
        <div class="overflow-y-auto flex-1 px-6 py-6">
            <!-- Profile Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <div class="flex items-start gap-6">
                    <!-- Avatar -->
                    <div class="w-24 h-24 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg flex-shrink-0">
                        RG
                    </div>

                    <!-- Info -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 id="borrower-name" class="text-2xl font-bold text-gray-900"></h3>
                                <p id="borrower-role" class="text-gray-600 flex items-center gap-2 mt-1">
                                    <span id="borrower-role-badge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        Student
                                    </span>
                                </p>
                            </div>
                            <span id="borrower-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-red-50 text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Penalty
                            </span>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-3 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">ID Number</p>
                                <p id="borrower-id-number" class="font-semibold text-gray-800 font-mono"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Email</p>
                                <p id="borrower-email" class="font-medium text-gray-800 truncate"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Year Level</p>
                                <p id="borrower-year-level" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Department</p>
                                <p id="borrower-department" class="font-semibold text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Contact</p>
                                <p id="borrower-contact" class="font-medium text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Member Since</p>
                                <p id="borrower-member-since" class="font-medium text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Penalties</p>
                                <p id="borrower-penalties" class="font-bold text-red-600"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Reservations</p>
                                <p id="borrower-reservations" class="font-semibold text-gray-800"></p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3 mt-5">
                            <button id="borrow-book-button" class="inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-teal-600 text-white rounded-lg text-sm font-medium transition-all shadow-sm hover:shadow">
                                <img src="{{ asset("build/assets/icons/add-white.svg")}}" alt="Borrow Icon" class="w-5 h-5">
                                Borrow a Book
                            </button>
                            <button id="approve-reservation-button" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-all shadow-sm hover:shadow">
                                {{-- <img src="{{ asset("build/assets/icons/check-white.svg")}}" alt="Approve Icon" class="w-5 h-5"> --}}
                                Approve Reservation
                            </button>
                            <button id="mark-as-cleared-button" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-all shadow-sm hover:shadow">
                                {{-- <img src="{{ asset("build/assets/icons/check-white.svg")}}" alt="Mark as Cleared Icon" class="w-5 h-5"> --}}
                                Mark as Cleared
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currently Borrowed Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <img src="{{ asset("build/assets/icons/currently-borrowed.svg")}}" alt="Currently Borrowed Icon" class="w-7 h-7">
                        Currently Borrowed
                        <span class="text-gray-500 text-sm font-normal ml-1">(3 / 3)</span>
                    </h3>
                    <button class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Borrowing History
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">No.</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Book</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Date Borrowed</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Due Date</th>
                                <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Penalties</th>
                                <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <!-- Row 1 - On Time -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-gray-600 font-medium">1</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://m.media-amazon.com/images/I/81WojUxbbFL._AC_UF1000,1000_QL80_.jpg" class="w-10 h-14 rounded-md object-cover shadow-sm flex-shrink-0">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 truncate">Educated: A Memoir</p>
                                            <p class="text-xs text-gray-500">Tara Westover</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-700 text-xs">Sept 1, 2025</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Sept 14, 2025
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-xs">—</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                                            Renew
                                        </button>
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                                            Return
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 2 - Due Soon -->
                            <tr class="hover:bg-gray-50 transition-colors bg-yellow-50/30">
                                <td class="py-3 px-4 text-gray-600 font-medium">2</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://m.media-amazon.com/images/I/51V6xTOZaCL.jpg" class="w-10 h-14 rounded-md object-cover shadow-sm flex-shrink-0">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 truncate">Atomic Habits</p>
                                            <p class="text-xs text-gray-500">James Clear</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-700 text-xs">Aug 28, 2025</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-yellow-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Sept 11, 2025 (2 days)
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-xs">—</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                                            Renew
                                        </button>
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                                            Return
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 3 - Overdue -->
                            <tr class="hover:bg-gray-50 transition-colors bg-red-50/30">
                                <td class="py-3 px-4 text-gray-600 font-medium">3</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://m.media-amazon.com/images/I/71g2ednj0JL._AC_UF1000,1000_QL80_.jpg" class="w-10 h-14 rounded-md object-cover shadow-sm flex-shrink-0">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 truncate">The 7 Habits of Highly Effective People</p>
                                            <p class="text-xs text-gray-500">Stephen Covey</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-700 text-xs">Aug 15, 2025</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Aug 29, 2025 (13 days overdue)
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-red-600 font-bold text-xs">₱43.12</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-medium transition-all shadow-sm">
                                            Return
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="pagination flex justify-between items-center mt-4"></div>


    </div>


</div>
