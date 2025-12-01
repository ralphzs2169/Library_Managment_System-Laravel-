{{-- Members Table Controls --}}
<div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
    <div class="flex-1 max-w-md">
        <div class="relative">
            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
            {{-- ADDED 'name="search"' --}}
            <input id="borrowers-search" type="text" placeholder="Search by name or ID number..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        {{-- Role Filter --}}
        <select id="borrower-role-filter" name="role" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Role: All</option>
            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Role: Students</option>
            <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Role: Teachers</option>
        </select>

        {{-- Status Filter --}}
        <select id="borrower-status-filter" name="status" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Status: All</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Status: Active</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Status: Suspended</option>
            <option value="cleared" {{ request('status') === 'cleared' ? 'selected' : '' }}>Status: Cleared</option>
        </select>

        {{-- NEW: Sort Filter --}}
        <select id="borrower-sort-filter" name="sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
            <option value="">Sort By: Default</option>
            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
            <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Joined: Newest</option>
            <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Joined: Oldest</option>
        </select>

        <button type="button" id="reset-borrower-filters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
            Reset Filters
        </button>
    </div>
</div>
