<x-layout>
    <div class="px-12 py-6 min-h-screen bg-background">
        <!-- Top Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-4 px-20 mb-16">
            {{-- Total Books --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Books</p>
                    <div class="bg-blue-50 p-2 rounded-lg">
                        {{-- Assuming 'total-books.svg' exists in your assets folder --}}
                        <img src="{{ asset('build/assets/icons/total-books.svg') }}" alt="Total Books" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($totalBooks) }}</p>
                <div class="w-full h-1.5 bg-blue-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: 100%"></div>
                </div>
            </div>

            {{-- Active Borrowers --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Active Borrowers</p>
                    <div class="bg-green-50 p-2 rounded-lg">
                        {{-- Assuming 'active-borrowers.svg' exists in your assets folder --}}
                        <img src="{{ asset('build/assets/icons/active-borrowers.svg') }}" alt="Active Borrowers" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($totalActiveBorrowers) }}</p>
                <div class="w-full h-1.5 bg-green-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full transition-all duration-500" style="width: {{ $totalActiveBorrowers > 0 ? min(($totalActiveBorrowers / 100) * 100, 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Books Borrowed --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Books Borrowed</p>
                    <div class="bg-orange-50 p-2 rounded-lg">
                        <svg class="w-5 h-5" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.004 512.004" xml:space="preserve" fill="#000000">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <g>
                                    <path style="fill:#EDEBDA;" d="M256,393.144L256,393.144c-72.037-28.809-149.294-39.643-219.429-9.143h-9.143V45.716h9.143 c68.27-24.384,151.159-24.384,219.429,0V393.144z"></path>
                                    <g>
                                        <path style="fill:#CEC9AE;" d="M475.429,45.716c-71.141-24.384-148.663-24.384-219.429,0v347.429 c24.302-9.28,48.859-16.64,73.143-21.403V261.972V29.46c24.283-2.624,48.814-2.615,73.143,0.027V256.44V366.21 c25.582,1.518,50.213,7.067,73.143,17.792h8.997V45.716H475.429z"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,118.859h-82.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h82.286c5.047,0,9.143,4.087,9.143,9.143C210.286,114.772,206.19,118.859,201.143,118.859"></path>
                                        <path style="fill:#CEC9AE;" d="M82.286,118.859c-2.469,0-4.754-0.923-6.491-2.651c-1.737-1.737-2.651-4.023-2.651-6.491 c0-2.377,1.006-4.763,2.651-6.491c3.474-3.383,9.509-3.383,12.983,0c1.646,1.728,2.651,4.114,2.651,6.491 c0,1.189-0.183,2.377-0.731,3.474c-0.457,1.097-1.097,2.094-1.92,3.017c-0.914,0.823-1.92,1.463-3.017,1.92 C84.663,118.584,83.474,118.859,82.286,118.859"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,164.573H82.286c-5.047,0-9.143-4.087-9.143-9.143s4.096-9.143,9.143-9.143h118.857 c5.047,0,9.143,4.087,9.143,9.143S206.19,164.573,201.143,164.573"></path>
                                        <path style="fill:#CEC9AE;" d="M164.571,210.287H82.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143h82.286 c5.047,0,9.143,4.087,9.143,9.143C173.714,206.2,169.618,210.287,164.571,210.287"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,210.287c-2.469,0-4.754-0.923-6.491-2.651c-0.823-0.923-1.463-1.92-1.92-3.017 c-0.457-1.097-0.731-2.286-0.731-3.474c0-2.377,1.006-4.763,2.651-6.491c3.383-3.383,9.509-3.383,12.983,0 c1.646,1.728,2.651,4.114,2.651,6.491c0,1.189-0.274,2.377-0.731,3.474c-0.457,1.097-1.097,2.094-1.92,3.017 c-0.914,0.823-1.92,1.463-3.017,1.92C203.52,210.013,202.331,210.287,201.143,210.287"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,256.002h-82.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h82.286c5.047,0,9.143,4.087,9.143,9.143C210.286,251.915,206.19,256.002,201.143,256.002"></path>
                                        <path style="fill:#CEC9AE;" d="M82.286,256.002c-1.189,0-2.377-0.274-3.474-0.731c-1.097-0.457-2.103-1.097-3.017-1.92 c-1.737-1.737-2.651-4.023-2.651-6.491c0-1.189,0.274-2.377,0.731-3.474c0.457-1.189,1.097-2.103,1.92-3.017 c2.56-2.469,6.583-3.383,9.966-1.92c1.189,0.457,2.103,1.097,3.017,1.92c0.823,0.914,1.463,1.92,1.92,3.017 c0.457,1.097,0.731,2.286,0.731,3.474c0,2.469-0.914,4.754-2.651,6.491C87.04,255.078,84.754,256.002,82.286,256.002"></path>
                                        <path style="fill:#CEC9AE;" d="M201.143,301.716H82.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h118.857c5.047,0,9.143,4.087,9.143,9.143C210.286,297.629,206.19,301.716,201.143,301.716"></path>
                                    </g>
                                    <g>
                                        <path style="fill:#EDEBDA;" d="M329.143,164.573h-18.286c-5.047,0-9.143-4.087-9.143-9.143s4.096-9.143,9.143-9.143h18.286 c5.047,0,9.143,4.087,9.143,9.143S334.19,164.573,329.143,164.573"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,164.573c-2.478,0-4.763-0.923-6.491-2.651c-1.737-1.737-2.651-4.023-2.651-6.491 c0-2.377,0.997-4.763,2.651-6.491c3.383-3.383,9.6-3.383,12.891,0c1.737,1.728,2.743,4.114,2.743,6.491 c0,1.189-0.274,2.377-0.731,3.474s-1.097,2.094-2.011,3.017C443.611,163.65,441.326,164.573,438.857,164.573"></path>
                                        <path style="fill:#EDEBDA;" d="M329.143,118.859h-18.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h18.286c5.047,0,9.143,4.087,9.143,9.143C338.286,114.772,334.19,118.859,329.143,118.859"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,118.859h-36.571c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h36.571c5.056,0,9.143,4.087,9.143,9.143C448,114.772,443.913,118.859,438.857,118.859"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,301.716h-36.571c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h36.571c5.056,0,9.143,4.087,9.143,9.143C448,297.629,443.913,301.716,438.857,301.716"></path>
                                        <path style="fill:#EDEBDA;" d="M310.857,301.716c-2.469,0-4.754-0.923-6.491-2.651c-1.737-1.737-2.651-4.023-2.651-6.491 c0-1.189,0.274-2.377,0.731-3.474c0.457-1.189,1.097-2.103,1.92-3.017c0.914-0.823,1.92-1.463,3.017-1.92 c2.194-1.006,4.754-1.006,6.949,0c1.097,0.457,2.103,1.097,3.017,1.92c0.823,0.914,1.463,1.829,1.92,3.017 c0.549,1.097,0.731,2.286,0.731,3.474c0,2.377-0.914,4.754-2.651,6.491C315.611,300.792,313.326,301.716,310.857,301.716"></path>
                                        <path style="fill:#EDEBDA;" d="M329.143,256.002h-18.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h18.286c5.047,0,9.143,4.087,9.143,9.143C338.286,251.915,334.19,256.002,329.143,256.002"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,256.002c-1.189,0-2.377-0.274-3.474-0.731c-1.097-0.457-2.103-1.097-3.017-1.92 c-1.737-1.737-2.651-4.023-2.651-6.491c0-1.189,0.183-2.377,0.731-3.474c0.457-1.097,1.097-2.103,1.92-3.017 c3.383-3.383,9.6-3.383,12.983,0c0.823,0.914,1.463,1.92,1.92,3.017c0.457,1.097,0.731,2.286,0.731,3.474 c0,2.377-0.923,4.754-2.651,6.491C443.611,255.078,441.326,256.002,438.857,256.002"></path>
                                        <path style="fill:#EDEBDA;" d="M329.143,210.287h-18.286c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h18.286c5.047,0,9.143,4.087,9.143,9.143C338.286,206.2,334.19,210.287,329.143,210.287"></path>
                                        <path style="fill:#EDEBDA;" d="M438.857,210.287h-36.571c-5.047,0-9.143-4.087-9.143-9.143c0-5.056,4.096-9.143,9.143-9.143 h36.571c5.056,0,9.143,4.087,9.143,9.143C448,206.2,443.913,210.287,438.857,210.287"></path>
                                    </g>
                                    <g>
                                        <path style="fill:#f97316;" d="M329.143,371.74c-24.283,4.763-48.841,12.123-73.143,21.403 c-72.037-28.809-149.294-39.643-219.429-9.143h-9.143V45.715H0v356.571h329.143V371.74z"></path>
                                        <path style="fill:#f97316;" d="M484.429,45.716v338.286h-8.997c-22.93-10.734-47.561-16.274-73.143-17.792v36.078h109.714V45.716 H484.429z"></path>
                                    </g>
                                    <g>
                                        <path style="fill:#f97316;" d="M0,429.716h329.143v-27.429H0V429.716z"></path>
                                        <path style="fill:#f97316;" d="M402.286,429.716H512v-27.429H402.286V429.716z"></path>
                                    </g>
                                    <path style="fill:#DD342E;" d="M329.143,29.463v232.503v109.778v112.832l36.571-27.429l36.571,27.429V366.203V256.434V29.49 C377.957,26.839,353.426,26.829,329.143,29.463"></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($totalBorrowedCopies) }}</p>
                <div class="w-full h-1.5 bg-orange-100 rounded-full overflow-hidden">
                    <div class="h-full bg-orange-500 rounded-full transition-all duration-500" style="width: {{ $totalCopies > 0 ? min(($totalBorrowedCopies / $totalCopies) * 100, 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Total Fines --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Fines</p>
                    <div class="bg-red-50 p-2 rounded-lg">
                        {{-- Assuming 'total-fines.svg' exists in your assets folder --}}
                        <img src="{{ asset('build/assets/icons/total-fines.svg') }}" alt="Total Fines" class="w-5 h-5">
                    </div>
                </div>
                {{-- Displaying fines in red and formatting the currency (₱) and decimals --}}
                <p class="text-3xl font-bold text-red-600 mb-3">
                    ₱{{ number_format((int) $totalUnpaidFines, 0) }}
                    <span class="text-xl">.{{ str_pad(round(($totalUnpaidFines - floor($totalUnpaidFines)) * 100), 2, '0', STR_PAD_LEFT) }}</span>
                </p>
                <div class="w-full h-1.5 bg-red-100 rounded-full overflow-hidden">
                    <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: {{ $totalUnpaidFines > 0 ? min(($totalUnpaidFines / 10000) * 100, 100) : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Borrowers Table Section -->
        {{-- Tabbed Container  --}}
        <div class="relative">

            {{-- Tabbed Headers --}}
            <div class="flex w-fit items-start absolute z-20 top-[-42px] left-[-1.02px]">

                {{-- Members Table Tab --}}
                <button type="button" id="tab-members-btn" class="dashboard-tab-btn relative cursor-pointer bg-background border-y border-1 border-gray-300 px-12 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                               
                                                px-3 py-2">
                    <img id="tab-members-icon" src="{{ asset('build/assets/icons/members-list-black.svg') }}" alt="Members List Icon" class="w-5 h-5 tab-icon">
                    <span class="tab-title truncate max-w-[50px] sm:max-w-none font-light tracking-wide text-md">Borrower Management</span>
                </button>

                {{-- Active Borrows Tab --}}
                <button type="button" id="tab-active-borrows-btn" class="dashboard-tab-btn relative cursor-pointer bg-background border-y border-1 border-gray-300 px-12 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                               
                                                px-3 py-2">
                    <img id="tab-active-borrows-icon" src="{{ asset('build/assets/icons/currently-borrowed.svg') }}" alt="Active Borrows Icon" class="w-5 h-5 tab-icon">
                    <span class="tab-title truncate max-w-[50px] sm:max-w-none  font-light tracking-wide text-md">Active Borrows</span>
                </button>

                {{-- Unpaid Penalties Tab --}}
                <button type="button" id="tab-unpaid-penalties-btn" class="dashboard-tab-btn relative cursor-pointer bg-background border-y border-1 border-gray-300 px-12 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                                
                                                px-3 py-2">
                    <img id="tab-unpaid-penalties-icon" src="{{ asset('build/assets/icons/unpaid.svg') }}" alt="Unpaid Penalties Icon" class="w-5 h-5 tab-icon">
                    <span class="tab-title truncate max-w-[50px] sm:max-w-none  font-medium tracking-wide text-md">Unpaid Penalties</span>
                </button>

                {{-- Reservations Tab --}}
                <button type="button" id="tab-queue-reservations-btn" class="dashboard-tab-btn relative cursor-pointer bg-background border-y border-1 border-gray-300 px-12 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                              
                                                px-3 py-2">
                    <img id="tab-queue-reservations-icon" src="{{ asset('build/assets/icons/reservation.svg') }}" alt="Reservations Icon" class="w-5 h-5 tab-icon">
                    <span class="tab-title truncate max-w-[50px] sm:max-w-none  font-medium tracking-wide text-md">Queue Reservations</span>
                </button>
            </div>

            {{-- Tabbed Content --}}
            <div class="bg-white rounded-2xl rounded-tl-none shadow-lg px-6 py-6">
                <div id="tab-members-content">

                    {{-- Members List Table Header --}}
                    <div class="mb-8 flex items-center gap-3">
                        <div class="flex justify-between items-center w-full flex-wrap gap-y-3">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                    <img src="{{ asset('build/assets/icons/members-list-white.svg ')}}" alt="Member List Icon" class="w-8 h-8">
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">Borrower Management</h1>
                                    <p class="text-sm text-gray-500 mt-0.5">Central hub for all member profiles and transaction management</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-auto">
                                <span class="text-gray-500 text-sm">Total Members</span>
                                <span id="header-total-members" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">

                                </span>
                            </div>
                        </div>
                    </div>

                    @include ('partials.staff.members.table-controls')

                    <div id="members-table-container">

                    </div>
                </div>
                <div id="tab-active-borrows-content" class="hidden">

                    {{-- Active Borrows Table Header --}}
                    <div class="mb-8 flex items-center gap-3">
                        <div class="flex justify-between items-center w-full flex-wrap gap-y-3">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                    <img src="{{ asset('build/assets/icons/currently-borrowed-white.svg ')}}" alt="Active Borrows Icon" class="w-8 h-8">
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">Active Borrows</h1>
                                    <p class="text-sm text-gray-500 mt-0.5">Monitor all currently checked-out books, due dates, and overdue items</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-auto">
                                <span class="text-gray-500 text-sm">Total Active Borrows</span>
                                <span id="header-total-active-borrows" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">

                                </span>
                            </div>
                        </div>
                    </div>
                    @include ('partials.staff.active-borrows.table-controls')
                    <div id="active-borrows-table-container">

                        {{-- Table will be loaded via AJAX --}}
                    </div>
                </div>
                <div id="tab-unpaid-penalties-content" class="hidden">
                    {{-- Active Borrows Table Header --}}
                    <div class="mb-8 flex items-center gap-3">
                        <div class="flex justify-between items-center w-full flex-wrap gap-y-3">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                    <img src="{{ asset('build/assets/icons/unpaid-white.svg ')}}" alt="Active Borrows Icon" class="w-8 h-8">
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">Unpaid Penalties</h1>
                                    <p class="text-sm text-gray-500 mt-0.5">Track and process all outstanding fines and fees owed by members</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-auto">
                                <span class="text-gray-500 text-sm">Total Unpaid Penalties</span>
                                <span id="header-total-unpaid-penalties" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">

                                </span>
                            </div>
                        </div>
                    </div>
                    @include ('partials.staff.unpaid-penalties.table-controls')

                    <div id="unpaid-penalties-table-container">
                        {{-- Table will be loaded via AJAX --}}
                    </div>
                </div>

                <div id="tab-queue-reservations-content" class="hidden">
                    {{-- Queue Reservations Table Header --}}
                    <div class="mb-8 flex items-center gap-3">
                        <div class="flex justify-between items-center w-full flex-wrap gap-y-3">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                    <img src="{{ asset('build/assets/icons/reservation-white.svg ')}}" alt="Active Borrows Icon" class="w-8 h-8">
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">Queue Reservations</h1>
                                    <p class="text-sm text-gray-500 mt-0.5">Manage the waiting list and process books ready for member pickup</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-auto">
                                <span class="text-gray-500 text-sm">Total Queue Reservations</span>
                                <span id="header-total-queue-reservations" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">

                                </span>
                            </div>
                        </div>
                    </div>
                    @include ('partials.staff.queue-reservations.table-controls')

                    <div id="queue-reservations-table-container">
                        {{-- Table will be loaded via AJAX --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.confirm-reservation')
    @include('modals.borrower-profile')
    @include('modals.confirm-renew')
    @include('modals.confirm-borrow')
    @include('modals.confirm-return')
    @include('modals.confirm-payment')
</x-layout>

@vite('resources/js/pages/staff/borrower/borrowerProfileModal.js')
@vite('resources/js/pages/staff/bookSelection.js')
@vite('resources/js/pages/staff/confirmBorrow.js')
@vite('resources/js/pages/staff/confirmReturn.js')
@vite('resources/js/pages/staff/confirmRenew.js')
@vite('resources/js/pages/staff/confirmPayment.js')
@vite('resources/js/pages/staff/transactions/confirmReservation.js')
@vite('resources/js/pages/staff/dashboardTableControls.js')
@vite('resources/js/pages/staff/mainTabbedContainers.js')
