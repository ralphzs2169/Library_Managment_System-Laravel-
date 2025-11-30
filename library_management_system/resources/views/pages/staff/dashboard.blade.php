<x-layout>
    <div class="px-12 py-6 min-h-screen bg-background">
        <!-- Top Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-4 px-20 mb-16">
            {{-- Total Books --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Books</p>
                    <div class="bg-blue-50 p-2 rounded-lg">
                        <img src="{{ asset('build/assets/icons/total-books.svg') }}" alt="Total Books" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">1,128</p>
            </div>

            {{-- Active Borrowers --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Active Borrowers</p>
                    <div class="bg-green-50 p-2 rounded-lg">
                        <img src="{{ asset('build/assets/icons/active-borrowers.svg') }}" alt="Active Borrowers" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">256</p>
            </div>

            {{-- Books Borrowed --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Books Borrowed</p>
                    <div class="bg-orange-50 p-2 rounded-lg">
                        <img src="{{ asset('build/assets/icons/books-borrowed.svg') }}" alt="Books Borrowed" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">342</p>
            </div>

            {{-- Total Fines --}}
            <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Fines</p>
                    <div class="bg-red-50 p-2 rounded-lg">
                        <img src="{{ asset('build/assets/icons/total-fines.svg') }}" alt="Total Fines" class="w-5 h-5">
                    </div>
                </div>
                <p class="text-3xl font-bold text-red-600">₱4,215<span class="text-xl">.50</span></p>
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
                    <span class="truncate max-w-[50px] sm:max-w-none  font-bold tracking-wider text-md">Members List</span>
                </button>

                {{-- Active Borrows Tab --}}
                <button type="button" id="tab-active-borrows-btn" class="dashboard-tab-btn relative cursor-pointer bg-background border-y border-1 border-gray-300 px-12 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                               
                                                px-3 py-2">
                    <img id="tab-active-borrows-icon" src="{{ asset('build/assets/icons/currently-borrowed.svg') }}" alt="Active Borrows Icon" class="w-5 h-5 tab-icon">
                    <span class="truncate max-w-[50px] sm:max-w-none  font-light tracking-wide text-md">Active Borrows</span>
                </button>

                {{-- Unpaid Penalties Tab --}}
                <button type="button" id="tab-unpaid-penalties-btn" class="dashboard-tab-btn relative cursor-pointer bg-background border-y border-1 border-gray-300 px-12 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                                
                                                px-3 py-2">
                    <img id="tab-unpaid-penalties-icon" src="{{ asset('build/assets/icons/unpaid.svg') }}" alt="Unpaid Penalties Icon" class="w-5 h-5 tab-icon">
                    <span class="truncate max-w-[50px] sm:max-w-none  font-medium tracking-wide text-md">Unpaid Penalties</span>
                </button>

                {{-- Reservations Tab --}}
                <button type="button" id="tab-queue-reservations-btn" class="dashboard-tab-btn relative cursor-pointer bg-background border-y border-1 border-gray-300 px-12 py-2.5 flex items-center gap-2 text-sm font-medium rounded-t-xl hover:bg-gray-50 transition
                                              
                                                px-3 py-2">
                    <img id="tab-queuef-reservations-icon" src="{{ asset('build/assets/icons/reservation.svg') }}" alt="Reservations Icon" class="w-5 h-5 tab-icon">
                    <span class="truncate max-w-[50px] sm:max-w-none  font-medium tracking-wide text-md">Reservations</span>
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
                                    <h1 class="text-2xl font-bold text-gray-900">Member List</h1>
                                    <p class="text-sm text-gray-500 mt-0.5">Central hub for all member profiles and transaction management</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-auto">
                                <span class="text-gray-500 text-sm">Total Members</span>
                                <span id="header-total-members" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">
                                    {{ $users->total() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Members Table Controls --}}
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                                <input id="borrowers-search" type="text" placeholder="Search by name or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <select id="borrower-role-filter" name="role" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                                <option value="">Role: All</option>
                                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
                                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
                            </select>
                            <select id="borrower-status-filter" name="status" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                                <option value="">Status: All</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Status: Active</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Status: Suspended</option>
                                <option value="cleared" {{ request('status') === 'cleared' ? 'selected' : '' }}>Status: Cleared</option>
                            </select>
                            <button type="button" id="reset-borrower-filters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
                                Reset Filters
                            </button>
                        </div>
                    </div>

                    <div id="members-table-container">
                        @include('partials.staff.members-table', ['users' => $users])
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
                                    {{ $activeBorrowsCount ?? 0 }}
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
                                    {{ $unpaidPenaltiesCount ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @include ('partials.staff.active-borrows.table-controls')

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
                                    {{ $reservationsCount ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @include ('partials.staff.active-borrows.table-controls')

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
@vite('resources/js/pages/staff/kebabDropdown.js')
