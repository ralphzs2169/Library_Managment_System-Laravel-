{{-- Active Borrows Table Controls --}}
<div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
    <div class="flex-1 max-w-lg">
        <div class="relative">
            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
            <input id="active-borrows-search" type="text" placeholder="Search by Borrower Name, Book Title, or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
        </div>
    </div>

    <div class="flex flex-wrap gap-3">

        {{-- 1. Role Filter --}}
        <select id="active-borrows-role-filter" name="role" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Role: All</option>
            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
            <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
        </select>

        {{-- 2. Status Filter (Updated with Due Soon) --}}
        <select id="active-borrows-status-filter" name="status" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Status: All Active</option>
            {{-- Filter 1: Overdue --}}
            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Status: Overdue</option>
            {{-- Filter 2: Due Soon (New suggestion, assuming backend handles a 'due_soon' logic) --}}
            <option value="due_soon" {{ request('status') === 'due_soon' ? 'selected' : '' }}>Status: Due Soon</option>
            {{-- Filter 3: On Time / Borrowed --}}
            <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Status: Borrowed (On Time)</option>
        </select>

        {{-- 3. Sort By Filter (NEW) --}}
        <select id="active-borrows-sort-filter" name="sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="due_asc" {{ request('sort') === 'due_asc' || !request('sort') ? 'selected' : '' }}>Sort By: Due Date (Asc)</option>
            <option value="due_desc" {{ request('sort') === 'due_desc' ? 'selected' : '' }}>Sort By: Due Date (Desc)</option>
            <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Sort By: Book Title (A-Z)</option>
            <option value="borrower_asc" {{ request('sort') === 'borrower_asc' ? 'selected' : '' }}>Sort By: Borrower Name (A-Z)</option>
        </select>

        {{-- Reset Button --}}
        <button type="button" id="reset-active-borrows-filters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
            Reset Filters
        </button>
    </div>
</div>
