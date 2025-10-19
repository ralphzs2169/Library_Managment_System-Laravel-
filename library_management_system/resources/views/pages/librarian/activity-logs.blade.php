<x-layout>
    <section class="md:pl-78 p-6 pt-4 min-h-screen bg-background">
      <!-- Header -->
        <div class="flex justify-between items-center mb-4 bg-white shadow-sm rounded-xl px-6 py-2">
            <div class="flex items-center">
                <img src="{{ asset('build/assets/icons/activity-log-black.svg') }}" alt="Category Icon" class="inline-block w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold  rounded-xl">Activity Logs</h1>
            </div>
    
            <div class="flex flex-col">
                <span class="text-xs text-secondary font-bold">Total Activity Logs</span>
                <span class="ml-auto text-accent font-bold text-xl">{{ $activity_logs->total() }}</span>
            </div>
        </div>
    
    <div class="bg-white shadow-sm rounded-xl px-6 py-4 min-h-screen">
    
      <!-- 🔍 Search, Date, Sort Row -->
      <form method="get" id="filterForm" class="flex flex-col gap-2 w-full mb-4">
        <input type="hidden" name="route" value="">
        <input type="hidden" name="page" value="1">
    
        <div class="flex flex-col md:flex-row gap-3 w-full">
          <!-- Search by user fullname -->
          <div class="flex items-center border border-gray-300 rounded-full px-4 py-2 flex-grow shadow-sm">
            <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="w-5 h-5 text-gray-500">
            <input
                type="text"
                placeholder="Search users by their name..."
                class="flex-grow px-3 py-1 text-sm outline-none placeholder-gray-400"
                value=""
                name="search"
            >
          </div>
          <!-- Single date picker -->
          <input type="date" name="date" value="" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700">
          
          <!-- Sort Order -->
          <select id="filter-sort" name="sort" class="border border-gray-300 rounded-full px-4 py-2 text-sm text-gray-700 cursor-pointer shadow-sm w-full md:w-48 outline-none focus:ring-0">
            <option value="">Sort By</option>
            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Descending</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Ascending</option>
            <option value="action_az" {{ request('sort') === 'action_az' ? 'selected' : '' }}>Action A → Z</option>
            <option value="action_za" {{ request('sort') === 'action_za' ? 'selected' : '' }}>Action Z → A</option>
            <option value="entity_az" {{ request('sort') === 'entity_az' ? 'selected' : '' }}>Entity A → Z</option>
            <option value="entity_za" {{ request('sort') === 'entity_za' ? 'selected' : '' }}>Entity Z → A</option>
            <option value="user_az" {{ request('sort') === 'user_az' ? 'selected' : '' }}>User A → Z</option>
            <option value="user_za" {{ request('sort') === 'user_za' ? 'selected' : '' }}>User Z → A</option>
        </select>

        </div>
    
        <div class="flex flex-col md:flex-row gap-3 w-full items-end">
          <!-- Action Filter -->
          <select id="filter-action" name="action" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 cursor-pointer shadow-sm outline-none focus:ring-0">
            <option value="">All Actions</option>
            <option value="created">Created</option>
            <option value="updated">Updated</option>
            <option value="deleted">Deleted</option>
    
          </select>
          <!-- Entity Filter -->
          <select name="entity" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 cursor-pointer shadow-sm outline-none focus:ring-0">
            <option value="">All Entities</option>
            <option value="book">Books</option>
            <option value="category">Categories</option>
            <option value="genre">Genres</option>
            </select>
          <!-- User Filter -->
          <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 cursor-pointer shadow-sm outline-none focus:ring-0">
            <option value="">All Users</option>
            <option value="librarian">Librarian</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="staff">Staff</option>
          </select>
          <!-- Reset Filters Button at the right -->
          <button type="button"
    
            class="ml-auto px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm w-fit block md:w-auto">
            Reset Filters
          </button>
        </div>
      </form>
    
      <!--  Table -->
      <div id="activity-logs-container" class="overflow-x-auto overflow-y-auto border border-gray-200 rounded-xl shadow-sm" style="max-height: 450px; min-height: 360px;">
        @include('pages.librarian.activity-logs-table', ['activity_logs' => $activity_logs])
      </div>
      
    
      <!-- 📄 Pagination -->
    <div class="pagination mt-2">
      {{ $activity_logs->links('pagination::tailwind') }}
   </div>
    
    </div>
    </section>
  </x-layout>

  @vite('resources/js/api/activityLogHandler.js')