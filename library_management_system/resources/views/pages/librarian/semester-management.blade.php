<x-layout>
    <section class="md:pl-78 p-6 pt-4 min-h-screen bg-background">

        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('build/assets/icons/semester-black.svg') }}" alt="Settings Icon" class="inline-block w-10 h-10 mr-2">
                    <h1 class="text-2xl font-bold rounded-xl">Semester Management</h1>
                </div>
                <button class="flex items-center px-4 py-1.5 cursor-pointer shadow-sm bg-accent rounded-lg hover-scale-sm" id="open-add-semester-modal">
                    <img src="{{ asset('build/assets/icons/plus-white.svg') }}" alt="Add Semester Icon" class="inline-block w-7 h-7 mr-1">
                    <span class="text-white">Add Semester</span>
                </button>
            </div>
        </div>

        <!-- Current Active Semester Highlight -->
        @if($activeSemester)
        <div class="bg-accent/80 rounded-2xl shadow-lg p-6 mt-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-white">
                        <p class="text-sm font-medium opacity-90 mb-1">Current Active Semester</p>
                        <h2 class="text-2xl font-bold">{{ $activeSemester->name }}</h2>
                    </div>
                </div>
                <div class="text-right text-white">
                    <p class="text-sm opacity-90">Period</p>
                    <p class="text-lg font-semibold">{{ \Carbon\Carbon::parse($activeSemester->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($activeSemester->end_date)->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border-1 border-yellow-400 rounded-xl p-6 mt-5">
            <div class="flex items-center gap-3">
                <div>
                    <p class="font-semibold text-yellow-800">No Active Semester</p>
                    <p class="text-sm text-yellow-700">Please activate a semester to continue operations.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content Area -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mt-5">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-semibold text-gray-800">All Semesters</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-sm">Total Semesters</span>
                    <span class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $semesters->count() }}</span>
                </div>
            </div>

            <!-- Search and Sort -->
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                        <input type="text" id="semester-search" placeholder="Search by semester name..." value="{{ request('search') }}" class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <select id="semester-sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Sort: Newest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Sort: Oldest First</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Sort: Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Sort: Name (Z-A)</option>
                    </select>
                    <select id="semester-status-filter" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Status: All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Status: Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Status: Inactive</option>
                        <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Status: Ended</option>
                    </select>
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
@vite('resources/js/pages/tablControls.js')
