{{-- Unpaid Penalties Table Controls --}}
<div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
    <div class="flex-1 max-w-lg">
        <div class="relative">
            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
            <input id="unpaid-penalties-search" type="text" placeholder="Search by Borrower name, Book title, or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
        </div>
    </div>

    <div class="flex flex-wrap gap-3">

        {{-- 1. Role Filter --}}
        {{-- Filtering by borrower role (Student/Teacher) --}}
        <select id="unpaid-penalties-role-filter" name="role" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Role: All</option>
            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
            <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
        </select>

        {{-- 2. Penalty Type Filter --}}
        {{-- Filter by the reason the penalty was issued (Late, Lost, Damaged) --}}
        <select id="unpaid-penalties-type-filter" name="penalty_type" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Reason: All</option>
            <option value="late_return" {{ request('penalty_type') === 'late_return' ? 'selected' : '' }}>Reason: Late Return</option>
            <option value="lost_book" {{ request('penalty_type') === 'lost_book' ? 'selected' : '' }}>Reason: Lost Book</option>
            <option value="damaged_book" {{ request('penalty_type') === 'damaged_book' ? 'selected' : '' }}>Reason: Damaged Book</option>
        </select>

        {{-- 3. Payment Status Filter --}}
        {{-- filtering by UNPAID or PARTIALLY PAID --}}
        <select id="unpaid-penalties-status-filter" name="status" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Status: All Unpaid</option>
            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Status: Unpaid</option>
            <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Status: Partially Paid</option>
        </select>

        {{-- 4. Sort By Filter --}}
        <select id="unpaid-penalties-sort-filter" name="sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="amount_desc" {{ request('sort') === 'amount_desc' || !request('sort') ? 'selected' : '' }}>Sort By: Amount (High to Low)</option>
            <option value="amount_asc" {{ request('sort') === 'amount_asc' ? 'selected' : '' }}>Sort By: Amount (Low to High)</option>
            <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Sort By: Issued Date (Newest)</option>
            <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Sort By: Issued Date (Oldest)</option>
        </select>

        {{-- Reset Button --}}
        <button type="button" id="reset-unpaid-penalties-filters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
            Reset Filters
        </button>
    </div>
</div>
