{{-- Queue Reservations Table Controls --}}
<div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
    <div class="flex-1 max-w-lg">
        <div class="relative">
            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
            <input id="queue-reservations-search" type="text" placeholder="Search by Borrower name, Book title, or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
        </div>
    </div>

    <div class="flex flex-wrap gap-3">

        {{-- 1. Role Filter --}}
        {{-- Filtering by borrower role (Student/Teacher) is relevant --}}
        <select id="queue-reservations-role-filter" name="role" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Role: All</option>
            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
            <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
        </select>

        {{-- 2. Reservation Status Filter --}}
        {{-- Filter by Pending, Ready for Pickup, Cancelled, Expired --}}
        <select id="queue-reservations-status-filter" name="status" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Status: All</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Status: Pending</option>
            <option value="ready_for_pickup" {{ request('status') === 'ready_for_pickup' ? 'selected' : '' }}>Status: Ready for Pickup</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Status: Cancelled</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Status: Expired</option>
        </select>

        {{-- 3. Sort By Filter --}}
        {{-- Sort by queue position (highest priority first) or date reserved --}}
        <select id="queue-reservations-sort-filter" name="sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="position_asc" {{ request('sort') === 'position_asc' || !request('sort') ? 'selected' : '' }}>Sort By: Queue Position (1st)</option>
            <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Sort By: Reserved Date (Newest)</option>
            <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Sort By: Reserved Date (Oldest)</option>
            <option value="deadline_asc" {{ request('sort') === 'deadline_asc' ? 'selected' : '' }}>Sort By: Pickup Deadline (Soonest)</option>
        </select>

        {{-- Reset Button --}}
        <button type="button" id="reset-queue-reservations-filters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
            Reset Filters
        </button>
    </div>
</div>
