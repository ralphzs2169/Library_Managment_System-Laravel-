{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\semester-management.blade.php --}}
<x-layout>
    <section class="md:pl-72 p-4 sm:p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-4 sm:p-5 mb-2 sm:mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Semester Management</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Create and manage academic semesters</p>
                    </div>
                </div>
                <button class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg transition shadow-sm cursor-pointer" id="open-add-semester-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Semester</span>
                </button>
            </div>
        </div>

        <!-- Current Active Semester Banner -->
        @if($activeSemester)
        <div class="bg-accent rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-accent-100 text-xs sm:text-sm font-extralight uppercase  text-white tracking-wide mb-1">Currently Active Semester</p>
                    <h2 class="text-xl sm:text-2xl font-bold text-white">{{ $activeSemester->name }}</h2>
                </div>
                <div class="text-right text-white">
                    <p class="text-xs sm:text-sm opacity-90 font-medium">Duration</p>
                    <p class="text-sm sm:text-base font-semibold">{{ \Carbon\Carbon::parse($activeSemester->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($activeSemester->end_date)->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-semibold text-amber-900">No Active Semester</h3>
                    <p class="text-xs sm:text-sm text-amber-800 mt-1">Please activate a semester to continue library operations</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content Area -->
        <div class="bg-white shadow-sm rounded-xl p-4 sm:p-6">
            <!-- Table Header with Controls -->
            <div class="flex flex-col gap-4 mb-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">All Semesters</h2>
                    <span class="inline-flex items-center gap-2 text-sm text-gray-600">
                        <span>Total:</span>
                        <span class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full">{{ $semesters->total() }}</span>
                    </span>
                </div>

                <!-- Search and Filters -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="semester-search" placeholder="Search by semester name..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition">
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <select id="semester-status-filter" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition bg-white cursor-pointer">
                            <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>Status: All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Status: Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Status: Inactive</option>
                            <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Status: Ended</option>
                        </select>
                        <select id="semester-sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition bg-white cursor-pointer">
                            <option value="newest" {{ request('sort') === 'newest' || !request('sort') ? 'selected' : '' }}>Sort: Newest First</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Sort: Oldest First</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Sort: Name (A-Z)</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Sort: Name (Z-A)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div id="semesters-table-container">
                @include('partials.librarian.semesters-table', ['semesters' => $semesters, 'activeSemester' => $activeSemester])
            </div>
        </div>
    </section>

    @include('modals.semester-management')
</x-layout>

@vite('resources/js/pages/librarian/semesterManagement.js')
