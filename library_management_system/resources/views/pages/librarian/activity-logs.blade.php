{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\activity-logs.blade.php --}}
<x-layout>
    <section class="md:pl-72 p-4 sm:p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-4 sm:p-5 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg viewBox="0 0 24 24" fill="none" class="w-10 h-10" xmlns="http://www.w3.org/2000/svg">

                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M4 5V19C4 19.5523 4.44772 20 5 20H19" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M18 9L13 13.9999L10.5 11.4998L7 14.9998" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </g>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Activity Logs</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Track all system activities and changes</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-sm">Total Logs</span>
                    <span class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $activityLogs->total() }}</span>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white shadow-sm rounded-xl p-4 sm:p-6">
            <!-- Filters and Search -->
            <div class="flex flex-col gap-4 mb-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="activity-search" placeholder="Search by user, action, or entity..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition">
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <select id="activity-action-filter" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition bg-white cursor-pointer">
                            <option value="">Action: All</option>
                            <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Action: Created</option>
                            <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Action: Updated</option>
                            <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Action: Deleted</option>
                        </select>
                        <select id="activity-entity-filter" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition bg-white cursor-pointer">
                            <option value="">Entity: All</option>
                            <option value="Book" {{ request('entity') === 'Book' ? 'selected' : '' }}>Entity: Book</option>
                            <option value="Category" {{ request('entity') === 'Category' ? 'selected' : '' }}>Entity: Category</option>
                            <option value="Genre" {{ request('entity') === 'Genre' ? 'selected' : '' }}>Entity: Genre</option>
                            <option value="User" {{ request('entity') === 'User' ? 'selected' : '' }}>Entity: User</option>
                        </select>
                        <select id="activity-sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition bg-white cursor-pointer">
                            <option value="newest" {{ request('sort') === 'newest' || !request('sort') ? 'selected' : '' }}>Sort: Newest First</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Sort: Oldest First</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div id="activity-logs-container">
                @include('partials.librarian.activity-logs-table', ['activityLogs' => $activityLogs])
            </div>
        </div>
    </section>

    @include('modals.librarian.activity-log-details')
</x-layout>

@vite('resources/js/pages/librarian/activityLogs.js')
