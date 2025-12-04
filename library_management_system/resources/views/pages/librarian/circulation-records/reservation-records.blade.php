{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\books-list.blade.php --}}
<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
            {{-- Main Container: Switch to flex-col on small screens, back to flex-row on medium screens and up --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-8">

                {{-- Left Side: Title and Description --}}
                {{-- This section remains horizontal on all screens --}}
                <div class="flex items-start gap-4 flex-shrink-0">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <img src="{{ asset('build/assets/icons/reservation-white.svg ')}}" alt="Book Catalog Icon" class="w-8 h-8">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Reservation Records</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Review the complete archive of reservation requests, current queues, and cancellation history.</p>
                    </div>
                </div>

                {{-- Right Side: Total Count Badge --}}
                {{-- This section is aligned to the right on large screens, but dropped below the title on small screens --}}
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-sm">Total Records (Filtered)</span>
                    <span id="header-total-reservation-records" class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $totalReservationRecords }}</span>
                </div>
            </div>
        </div>

        {{-- <!-- Main Content --2 --}}
        <div id="books-filters" class="bg-white shadow-sm rounded-xl p-6">
            {{-- Improved Table Controls Layout --}}
            <div class="flex flex-col gap-3 mb-6">
                {{-- Top Row: Search (left) and Sort (right) --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                            <input id="reservation-records-search" type="text" placeholder="Search by Borrower name, Book title, or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <select id="reservation-records-sort-filter" name="sort" class="cursor-pointer w-full sm:w-auto px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                            <option value="deadline_asc" {{ request('sort') === 'deadline_asc' || !request('sort') ? 'selected' : '' }}>Sort By: Pickup Deadline (Soonest)</option>
                            <option value="position_asc" {{ request('sort') === 'position_asc' ? 'selected' : '' }}>Sort By: Queue Position (1st)</option>
                            <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Sort By: Reserved Date (Newest)</option>
                            <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Sort By: Reserved Date (Oldest)</option>
                        </select>
                    </div>
                </div>
                {{-- Second Row: Filters --}}
                <div class="flex flex-wrap gap-3">
                    {{-- Role Filter --}}
                    <select id="reservation-records-role-filter" name="role" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Role: All</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
                    </select>
                    {{-- Status Filter --}}
                    <select id="reservation-records-status-filter" name="status" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Status: All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Status: Pending</option>
                        <option value="ready_for_pickup" {{ request('status') === 'ready_for_pickup' ? 'selected' : '' }}>Status: Ready for Pickup</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Status: Cancelled</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Status: Expired</option>
                    </select>
                    {{-- Semester Filter --}}
                    <select id="reservation-records-semester-filter" name="semester" class="cursor-pointer px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="all">Semester: All</option>
                        @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ request('semester') == $semester->id ? 'selected' : '' }} {{ $semester->id == $activeSemesterId ? 'selected' : '' }}>
                            {{ $semester->name }}{{ $semester->id == $activeSemesterId ? ' (active)' : '' }}
                        </option>
                        @endforeach
                    </select>
                    {{-- Reset Button --}}
                    <button type="button" id="reset-reservation-records-filters" class="cursor-pointer ml-auto px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
                        Reset Filters
                    </button>
                </div>
            </div>

            <div id="reservation-records-container">
                @include('partials.librarian.circulation-records.reservation-records-table', ['reservations' => $reservations, 'semesters' => $semesters])
            </div>

            @include('modals.librarian.manage-reservation-record')
            @include('modals.confirm-borrow')

        </div>

        @vite('resources/js/pages/librarian/reservationRecords/reservationRecordDetails.js')
        @vite('resources/js/pages/librarian/reservationRecords/reservationRecordTableControls.js')
    </section>
</x-layout>
