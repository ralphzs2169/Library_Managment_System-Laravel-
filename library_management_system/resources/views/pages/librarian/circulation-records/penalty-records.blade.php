{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\books-list.blade.php --}}
<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <img src="{{ asset('build/assets/icons/unpaid-white.svg ')}}" alt="Book Catalog Icon" class="w-8 h-8">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Penalty Records</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Audit and track all overdue fines, book replacement fees, and payment history.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-sm">Total Records (Filtered)</span>
                    <span id="header-total-penalty-records" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $totalPenaltyRecords }}</span>
                </div>
            </div>
        </div>

        {{-- <!-- Main Content --2 --}}
        <div id="books-filters" class="bg-white shadow-sm rounded-xl p-6">
            {{-- Table Controls Layout --}}
            <div class="flex flex-col gap-3 mb-6">
                {{-- Top Row: Search (left) and Sort (right) --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                            <input id="penalty-records-search" type="text" placeholder="Search by Borrower name, Book title, or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <select id="penalty-records-sort-filter" name="sort" class="cursor-pointer w-full sm:w-auto px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                            <option value="" {{ request('sort') === '' ? 'selected' : '' }}>Sort By: Priority (Unpaid First)</option>
                            <option value="priority_partial" {{ request('sort') === 'priority_partial' ? 'selected' : '' }}>Sort By: Priority (Partially Paid First)</option>
                            <option value="amount_desc" {{ request('sort') === 'amount_desc' ? 'selected' : '' }}>Sort By: Amount (High to Low)</option>
                            <option value="amount_asc" {{ request('sort') === 'amount_asc' ? 'selected' : '' }}>Sort By: Amount (Low to High)</option>
                            <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Sort By: Issued Date (Newest)</option>
                            <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Sort By: Issued Date (Oldest)</option>
                        </select>
                    </div>
                </div>
                {{-- Second Row: Filters --}}
                <div class="flex flex-wrap gap-3">
                    {{-- 1. Role Filter --}}
                    {{-- Filtering by borrower role (Student/Teacher) --}}
                    <select id="penalty-records-role-filter" name="role" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Role: All</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
                    </select>

                    {{-- 2. Penalty Type Filter --}}
                    {{-- Filter by the reason the penalty was issued (Late, Lost, Damaged) --}}
                    <select id="penalty-records-type-filter" name="penalty_type" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Reason: All</option>
                        <option value="late_return" {{ request('penalty_type') === 'late_return' ? 'selected' : '' }}>Reason: Late Return</option>
                        <option value="lost_book" {{ request('penalty_type') === 'lost_book' ? 'selected' : '' }}>Reason: Lost Book</option>
                        <option value="damaged_book" {{ request('penalty_type') === 'damaged_book' ? 'selected' : '' }}>Reason: Damaged Book</option>
                    </select>

                    {{-- 3. Payment Status Filter --}}
                    {{-- filtering by UNPAID, PARTIALLY PAID, PAID, CANCELLED, DUE --}}
                    <select id="penalty-records-status-filter" name="status" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Status: All</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Status: Unpaid</option>
                        <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Status: Partially Paid</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Status: Paid</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Status: Cancelled</option>
                        <option value="due" {{ request('status') === 'due' ? 'selected' : '' }}>Status: Due (Unsettled)</option>
                    </select>

                    {{-- 4. Semester Filter --}}
                    {{-- Filter penalty records by semester --}}
                    <select id="penalty-records-semester-filter" name="semester" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="all">Semester: All</option>
                        @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ request('semester') == $semester->id ? 'selected' : '' }} {{ $semester->id == $activeSemesterId ? 'selected' : '' }}>
                            {{ $semester->name }}{{ $semester->id == $activeSemesterId ? ' (active)' : '' }}
                        </option>
                        @endforeach
                    </select>
                    {{-- Reset Button --}}
                    <button type="button" id="reset-penalty-records-filters" class="cursor-pointer ml-auto px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
                        Reset Filters
                    </button>
                </div>
            </div>

            <div id="penalty-records-container">
                @include('partials.librarian.circulation-records.penalty-records-table', ['penalties' => $penalties, 'semesters' => $semesters ])
            </div>
            @include('modals.librarian.penalty-record-details')
            @include('modals.confirm-payment')

        </div>

        @vite('resources/js/pages/librarian/utils/popupDetailsModal.js')
        @vite('resources/js/pages/manage-record-details/penaltyRecordDetails.js')
        @vite('resources/js/ajax/librarianSectionsHandler.js')
        @vite('resources/js/pages/librarian/penaltyRecords/penaltyRecordsTableControls.js')
    </section>
</x-layout>
