<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <img src="{{ asset('build/assets/icons/users.svg') }}" alt="Borrowers Icon" class="inline-block w-8 h-8">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Borrowers</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Review the complete history of all book borrowing and return transactions.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-sm">Total Records (Filtered)</span>
                    <span id="header-total-borrowers" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $totalBorrowersCount }}</span>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="bg-white shadow-sm rounded-xl p-6">
            {{-- Improved Table Controls Layout --}}
            <div class="flex flex-col gap-3 mb-6">
                {{-- Top Row: Search (left) and Sort (right) --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                            <input id="borrowers-search" type="text" placeholder="Search by Borrower Name, Book Title, or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        {{-- UPDATED SORT FILTER --}}
                        <select id="borrowers-sort-filter" name="sort" class="cursor-pointer w-full sm:w-auto px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                            <option value="" {{ request('sort') === '' ? 'selected' : '' }}>Sort By: Default (ID Desc)</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Sort By: Borrower Name (A-Z)</option>
                            <option value="borrows_desc" {{ request('sort') === 'borrows_desc' ? 'selected' : '' }}>Sort By: Active Borrows (High to Low)</option>
                            <option value="reservations_desc" {{ request('sort') === 'reservations_desc' ? 'selected' : '' }}>Sort By: Reservations (High to Low)</option>
                            <option value="fines_desc" {{ request('sort') === 'fines_desc' ? 'selected' : '' }}>Sort By: Overdue Fines (High to Low)</option>
                            <option value="latest_activity" {{ request('sort') === 'latest_activity' ? 'selected' : '' }}>Sort By: Last Active (Newest)</option>
                        </select>
                    </div>
                </div>

                {{-- Second Row: FILTERS --}}
                <div class="flex flex-wrap gap-3">
                    {{-- Role Filter --}}
                    <select id="borrowers-role-filter" name="role" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Role: All</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
                    </select>

                    {{-- Library Status Filter (Now the main filter for suspension) --}}
                    <select id="borrowers-status-filter" name="library_status" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="" {{ request('library_status') === null || request('library_status') === '' ? 'selected' : '' }}>Status: All</option>
                        <option value="suspended" {{ request('library_status') === 'suspended' ? 'selected' : '' }}>Status: Suspended</option>
                        <option value="active" {{ request('library_status') === 'active' ? 'selected' : '' }}>Status: Active</option>
                        <option value="cleared" {{ request('library_status') === 'cleared' ? 'selected' : '' }}>Status: Cleared</option>
                    </select>

                    {{-- NEW: Active Borrowings Filter --}}
                    <select id="borrowers-active-borrows-filter" name="active_borrows" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Borrows: All</option>
                        <option value="has_borrows" {{ request('active_borrows') === 'has_borrows' ? 'selected' : '' }}>Borrows: Has Active</option>
                        <option value="no_borrows" {{ request('active_borrows') === 'no_borrows' ? 'selected' : '' }}>Borrows: None Active</option>
                    </select>

                    {{-- NEW: Reservation Status Filter --}}
                    <select id="borrowers-reservation-filter" name="reservation_status" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Reservations: All</option>
                        <option value="has_pending" {{ request('reservation_status') === 'has_pending' ? 'selected' : '' }}>Reservations: Has Pending</option>
                        <option value="ready_for_pickup" {{ request('reservation_status') === 'ready_for_pickup' ? 'selected' : '' }}>Reservations: Ready for Pickup</option>
                        <option value="no_reservations" {{ request('reservation_status') === 'no_reservations' ? 'selected' : '' }}>Reservations: None</option>
                    </select>

                    {{-- NEW: Fines Filter --}}
                    <select id="borrowers-fines-filter" name="fines" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Fines: All</option>
                        <option value="has_fines" {{ request('fines') === 'has_fines' ? 'selected' : '' }}>Fines: Has Overdue Fines</option>
                        <option value="no_fines" {{ request('fines') === 'no_fines' ? 'selected' : '' }}>Fines: Cleared/None</option>
                    </select>

                    {{-- Reset Button --}}
                    <button type="button" id="reset-borrowers-filters" class="ml-auto cursor-pointer px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
                        Reset Filters
                    </button>
                </div>
            </div>
            <div id="borrowers-container">
                @include('partials.librarian.user-management.borrowers-table', ['borrowers' => $borrowers])
            </div>


        </div>
    </section>

    @include('modals.confirm-reservation')
    @include('modals.borrower-profile')
    @include('modals.confirm-renew')
    @include('modals.confirm-borrow')
    @include('modals.confirm-return')
    @include('modals.confirm-payment')

    @include('modals.librarian.suspend-user')
    @include('modals.librarian.lift-suspension')

</x-layout>

@vite('resources/js/pages/staff/borrower/borrowerProfileModal.js')
@vite('resources/js/pages/staff/bookSelection.js')
@vite('resources/js/pages/staff/confirmBorrow.js')
@vite('resources/js/pages/staff/confirmReturn.js')
@vite('resources/js/pages/staff/confirmRenew.js')
@vite('resources/js/pages/staff/confirmPayment.js')
@vite('resources/js/pages/staff/transactions/confirmReservation.js')
@vite('resources/js/pages/librarian/tableControls/borrowersControls.js')
@vite('resources/js/pages/librarian/suspensionHelpers.js')
