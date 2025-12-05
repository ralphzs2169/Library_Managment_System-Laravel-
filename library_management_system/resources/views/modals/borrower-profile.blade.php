<div id="borrower-profile-modal" class="fixed inset-0 flex items-center hidden justify-center bg-background-unfocused bg-opacity-0 z-60 transition-opacity duration-150">
    <div id="borrower-profile-content" class="bg-gray-100 rounded-lg shadow-2xl w-[95%] max-w-7xl max-h-[90vh] overflow-hidden flex flex-col transform scale-95 opacity-0 transition-all duration-150">
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                <img src="{{ asset("build/assets/icons/member-profile-black.svg")}}" alt="Borrower Profile Icon" class="w-7 h-7">
                Member Profile
            </h2>
            <button id="close-borrower-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="{{ asset("build/assets/icons/close-gray.svg")}}" alt="Close Borrower Profile Modal" class="w-6 h-6">
            </button>
        </div>


        <!-- Scrollable Content -->
        <div id="borrower-profile-scrollable-content" class="overflow-y-auto flex-1">
            <!-- Profile Section -->
            <div id="borrower-profile-scrollable-content" class="overflow-y-auto flex-1">
                <!-- Skeleton Loader -->
                <div id="borrower-profile-skeleton" class="animate-pulse p-6 space-y-6">
                    <!-- Profile Section: Left avatar, Right info -->
                    <div class="flex items-start gap-6">
                        <!-- Avatar -->
                        <div class="w-24 h-24 rounded-full bg-gray-300 flex-shrink-0"></div>

                        <!-- Info -->
                        <div class="flex-1 space-y-4">
                            <!-- Name & Role -->
                            <div class="flex justify-between items-start">
                                <div class="space-y-2 flex-1">
                                    <div class="h-6 w-48 bg-gray-300 rounded"></div>
                                    <div class="h-4 w-32 bg-gray-300 rounded"></div>
                                </div>
                                <div class="h-6 w-20 bg-gray-300 rounded"></div> <!-- Status badge -->
                            </div>

                            <!-- Info Grid (8 items) -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-3">
                                <div class="h-4 bg-gray-300 rounded w-full"></div>
                                <div class="h-4 bg-gray-300 rounded w-full"></div>
                                <div class="h-4 bg-gray-300 rounded w-full"></div>
                                <div class="h-4 bg-gray-300 rounded w-full"></div>
                                <div class="h-4 bg-gray-300 rounded w-full"></div>
                                <div class="h-4 bg-gray-300 rounded w-full"></div>
                                <div class="h-4 bg-gray-300 rounded w-full"></div>
                                <div class="h-4 bg-gray-300 rounded w-full"></div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3 mt-2">
                                <div class="h-10 w-32 bg-gray-300 rounded-lg"></div>
                                <div class="h-10 w-32 bg-gray-300 rounded-lg"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Header -->
                    <div class="flex gap-4 mt-6">
                        <div class="h-8 w-40 bg-gray-300 rounded"></div>
                        <div class="h-8 w-40 bg-gray-300 rounded"></div>
                        <div class="h-8 w-40 bg-gray-300 rounded"></div>
                    </div>

                    <!-- Tabs Content -->
                    <div class="mt-4 space-y-4">
                        <!-- Borrowed Books Table Skeleton -->
                        <div class="space-y-2">
                            <div class="h-6 w-48 bg-gray-300 rounded"></div>
                            <div class="h-32 bg-gray-300 rounded w-full"></div>
                        </div>
                        <!-- Penalties Table Skeleton -->
                        <div class="space-y-2">
                            <div class="h-6 w-48 bg-gray-300 rounded"></div>
                            <div class="h-32 bg-gray-300 rounded w-full"></div>
                        </div>
                    </div>
                </div>

                <div id="borrower-profile-real" class="hidden">
                    <div class="bg-white border border-gray-200 p-6 mb-6 shadow-xs">
                        <div class="flex items-start gap-6">
                            <!-- Avatar -->
                            <div id="borrower-profile-picture" class="w-24 h-24 bg-gradient-to-br from-accent to-teal-600 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg flex-shrink-0">

                            </div>

                            <!-- Info -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 id="borrower-name" class="text-2xl font-bold text-gray-900"></h3>
                                        <p id="borrower-role" class="text-gray-600 flex items-center gap-2 mt-1">
                                            <span id="borrower-role-badge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">

                                            </span>
                                        </p>
                                    </div>
                                    <span id="borrower-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-red-50 text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>

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
                                    <div id="borrower-year-level-container" class="block">
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
                                        <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Total Unpaid Fine</p>
                                        <p id="borrower-total-fine-ampount" class="font-bold text-red-600"></p>
                                    </div>

                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap gap-3 mt-5">
                                    <button id="borrow-book-btn" class="inline-flex items-center gap-2 px-4 py-2 bg-secondary hover:bg-secondary/90 text-white rounded-lg text-sm font-medium transition-all shadow-sm hover:shadow">
                                        <img src="{{ asset("build/assets/icons/add-book-white.svg")}}" alt="Borrow Icon" class="w-5 h-5">
                                        Borrow a Book
                                    </button>
                                    <button id="add-reservation-btn" class="inline-flex items-center gap-2 px-4 py-2 bg-secondary hover:bg-secondary/90 cursor-pointer text-white rounded-lg text-sm font-medium transition-all shadow-sm hover:shadow">
                                        <img src="{{ asset("build/assets/icons/reservation-white.svg")}}" alt="Approve Icon" class="w-5 h-5">
                                        Add Reservation
                                    </button>
                                    <button id="mark-as-cleared-button" class="inline-flex items-center gap-2 px-4 py-2 bg-secondary hover:bg-secondary/90 text-white rounded-lg text-sm font-medium transition-all shadow-sm hover:shadow">
                                        {{-- <img src="{{ asset("build/assets/icons/check-white.svg")}}" alt="Mark as Cleared Icon" class="w-5 h-5"> --}}
                                        Mark as Cleared
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Tabbed Container for Borrowed Books, Penalties & Reservations -->

                    <div class="relative pt-10 px-6 pb-6">
                        <!-- Tabs Header -->
                        <div class="flex w-fit items-start absolute z-10 top-0 left-[23px]">
                            <button type="button" id="tab-borrowed-btn" class="borrower-tab-btn relative cursor-pointer bg-gray-100 border-y border-1 border-gray-200 px-6 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                            sm:px-6 sm:py-2.5
                                            px-3 py-2">
                                <img id="tab-borrowed-icon" src="{{ asset('build/assets/icons/currently-borrowed.svg') }}" alt="Currently Borrowed Icon" class="w-5 h-5 tab-icon">
                                <span class="truncate max-w-[50px] sm:max-w-none">Currently Borrowed</span>
                                <span id="badge-borrowed-count" class="absolute z-10 -top-1 -right-1 flex items-center justify-center w-5 h-5  bg-accent text-white text-xs font-semibold rounded-full shadow">
                                    <span id="borrowed-count" class="flex items-center justify-center w-full h-full"></span>
                                </span>
                            </button>
                            <button type="button" id="tab-penalties-btn" class="borrower-tab-btn relative cursor-pointer bg-gray-100 border-y border-1 border-gray-200 px-6 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                            sm:px-6 sm:py-2.5
                                            px-3 py-2">
                                <img id="tab-penalties-icon" src="{{ asset('build/assets/icons/unpaid.svg') }}" alt="Unpaid Penalties Icon" class="w-5 h-5 tab-icon">
                                <span class="truncate max-w-[50px] sm:max-w-none">Unpaid Penalties</span>
                                <span id="badge-penalties-count" class="absolute z-10 -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-accent text-white text-xs font-semibold rounded-full shadow">
                                    <span id="penalties-count" class="flex items-center justify-center w-full h-full"></span>
                                </span>

                            </button>
                            <button type="button" id="tab-reservations-btn" class="borrower-tab-btn relative cursor-pointer bg-gray-100 border-y border-1 border-gray-200 px-6 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                            sm:px-6 sm:py-2.5
                                            px-3 py-2">
                                <img id="tab-reservations-icon" src="{{ asset('build/assets/icons/reservation.svg') }}" alt="Reservations Icon" class="w-5 h-5 tab-icon">
                                <span class="truncate max-w-[50px] sm:max-w-none">Reservations</span>
                                <span id="badge-reservations-count" class="absolute z-10 -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-accent text-white text-xs font-semibold rounded-full shadow">
                                    <span id="reservations-count" class="flex items-center justify-center w-full h-full"></span>
                                </span>
                            </button>
                            <div>
                            </div>
                        </div>
                        <div class="bg-white shadow-sm border-t border-gray-200 p-6 rounded-tr-xl rounded-br-xl rounded-bl-xl">

                            <!-- Tabs Content -->

                            <div id="borrower-tab-content">
                                <!-- Borrowed Books Table (default visible) -->
                                <div id="tab-borrowed-content">
                                    <div class="mb-4 flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                                            <img src="{{ asset('build/assets/icons/currently-borrowed-white.svg')}}" alt="Currently Borrowed Icon" class="w-6 h-6">
                                        </div>
                                        <h2 class="text-lg font-semibold text-gray-900">Currently Borrowed Books</h2>
                                        <span class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">3 / 3</span>

                                    </div>
                                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">No.</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Book</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Date Borrowed</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Due Date</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs w-38 min-w-42 max-w-42 whitespace-nowrap">Status</th>
                                                    <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="currently-borrowed-tbody" class="bg-white divide-y divide-gray-100">
                                                <tr>
                                                    <td colspan="6" class="py-10 text-center text-gray-500 text-sm">
                                                        Loading borrowed books...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Penalties Table (hidden by default) -->
                                <div id="tab-penalties-content" class="hidden">
                                    <div class="mb-4 flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                                            <img src="{{ asset('build/assets/icons/unpaid-white.svg')}}" alt="Unpaid Fines/Penalties Icon" class="w-6 h-6">
                                        </div>
                                        <h2 class="text-lg font-semibold text-gray-900">Unpaid Fines/Penalties</h2>
                                        <span id="penalties-header-count" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">2</span>
                                    </div>
                                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                                        <table class="w-full text-sm table-fixed">
                                            <thead>
                                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-12 ">No.</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs truncate w-1/4">Book</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-30">Returned On</th>
                                                    <th class="py-3 px-2 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs  w-38 min-w-38 max-w-38 whitespace-nowrap">Reason</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-24">Amount</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs  whitespace-nowrap w-40">Status</th>
                                                    <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase truncate tracking-wider text-xs  w-46 min-w-46 max-w-46 whitespace-nowrap">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="active-penalties-tbody" class="bg-white divide-y divide-gray-100">
                                                <tr>
                                                    <td colspan="7" class="py-10 text-center text-gray-500 text-sm">
                                                        Loading unpaid fines/penalties...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Reservations Table (hidden by default) --}}
                                <div id="tab-reservations-content" class="hidden">
                                    <div class="mb-4 flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-accent to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                                            <img src="{{ asset('build/assets/icons/reservation-white.svg')}}" alt="Unpaid Fines/Penalties Icon" class="w-6 h-6">
                                        </div>
                                        <h2 class="text-lg font-semibold text-gray-900">Reservations</h2>
                                        <span id="reservations-header-count" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">2</span>
                                    </div>
                                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                                        <table class="w-full text-sm table-fixed">
                                            <thead>
                                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-8">No.</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs truncate w-1/4">Book</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-14">Queue</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-32">Status</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-26">Reserved at</th>
                                                    <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-26">Pikcup Deadline</th>
                                                    <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap w-40">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="active-reservations-tbody" class="bg-white divide-y divide-gray-100">
                                                <tr>
                                                    <td colspan="5" class="py-10 text-center text-gray-500 text-sm">
                                                        Loading reservations...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pagination flex justify-between items-center mt-4"></div>


    </div>


</div>
