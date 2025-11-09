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
                        <input type="text" id="semester-search" placeholder="Search by semester name..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <select id="semester-sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="newest">Sort: Newest First</option>
                        <option value="oldest">Sort: Oldest First</option>
                        <option value="name_asc">Sort: Name (A-Z)</option>
                        <option value="name_desc">Sort: Name (Z-A)</option>
                    </select>
                    <select id="semester-status-filter" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="all">Status: All</option>
                        <option value="active">Status: Active</option>
                        <option value="inactive">Status: Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="text-left py-4 px-4 uppercase font-semibold text-gray-700">No.</th>
                            <th class="text-left py-4 px-4 uppercase font-semibold text-gray-700">Name</th>
                            <th class="text-left py-4 px-4 uppercase font-semibold text-gray-700">Start Date</th>
                            <th class="text-left py-4 px-4 uppercase font-semibold text-gray-700">End Date</th>
                            <th class="text-center py-4 px-4 uppercase font-semibold text-gray-700">Status</th>
                            <th class="text-center py-4 px-4 uppercase font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="semesters-table-body">
                        @forelse($semesters as $index => $semester)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-gray-900">{{ $semester->name }}</div>
                            </td>
                            <td class="py-4 px-4 text-gray-600">{{ \Carbon\Carbon::parse($semester->start_date)->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ \Carbon\Carbon::parse($semester->end_date)->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-center">
                                @if($semester->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Active
                                </span>
                                @elseif($semester->status === 'inactive')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                                    Inactive
                                </span>
                                @elseif($semester->status === 'ended')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                                    Ended
                                </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($semester->status === 'inactive')
                                    <div class="relative inline-block">
                                        <button class="p-2 bg-blue-500 hover:bg-blue-600 rounded-lg transition-colors group edit-semester" data-semester-id="{{ $semester->id }}">
                                            <img src="{{ asset('build/assets/icons/edit.svg') }}" alt="Edit" class="w-5 h-5">
                                        </button>
                                        <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                            Edit
                                            <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="relative inline-block">
                                        <button class="p-2 bg-gray-400 rounded-lg transition-colors group opacity-50 cursor-not-allowed" disabled>
                                            <img src="{{ asset('build/assets/icons/edit.svg') }}" alt="Edit" class="w-5 h-5">
                                        </button>
                                        <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                            Cannot edit active semester
                                            <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($semester->status === 'active')
                                    <div class="relative inline-block">
                                        <button class="p-2 bg-gray-700 rounded-lg transition-colors group opacity-50 cursor-pointer">
                                            <img src="{{ asset('build/assets/icons/deactivate.svg') }}" alt="Deactivate" class="w-5 h-5">
                                        </button>
                                        <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                            Deactivate
                                            <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="relative inline-block">
                                        <button class="p-2 bg-green-500 hover:bg-green-600 rounded-lg transition-colors group activate-semester" data-semester-id="{{ $semester->id }}">
                                            <img src="{{ asset('build/assets/icons/activate.svg') }}" alt="Activate" class="w-5 h-5">
                                        </button>
                                        <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                            Activate
                                            <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 font-medium mb-1">No semesters yet</p>
                                <p class="text-gray-400 text-sm">Click "Add Semester" to create your first semester</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Empty State (hidden by default, shown when no results) -->
            <div id="no-results" class="hidden text-center py-12">
                <img src="{{ asset('build/assets/icons/no-results.svg') }}" alt="No Results" class="w-20 h-20 mx-auto mb-4 opacity-50">
                <p class="text-gray-500 text-lg">No semesters found</p>
                <p class="text-gray-400 text-sm mt-2">Try adjusting your search or filters</p>
            </div>
        </div>
    </section>
    @include('modals.semester-management')
</x-layout>

@vite('resources/js/pages/librarian/semesterManagement.js')
