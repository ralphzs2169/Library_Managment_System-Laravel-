{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\users\book-catalog.blade.php --}}
<x-layout>
    <div class="flex">
        <x-user-sidebar />

        <div class="flex-1 md:ml-60 p-6 bg-background min-h-screen">
            <!-- Header -->
            <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Book Catalog</h1>
                            <p class="text-sm text-gray-500 mt-0.5">Browse and discover books in our collection</p>
                        </div>
                    </div>

                    <!-- Search Bar in Header -->
                    <div class="w-full md:w-96">
                        <div class="relative">
                            <input type="text" id="book-search" placeholder="Search by title, author, or ISBN..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent text-sm">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto">
                {{-- Filters Section --}}
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">

                    <div class="mb-4 pb-2 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800">Filter Options</h3>
                        <p class="text-xs text-white font-medium bg-accent px-3 py-1 rounded-full border border-gray-100">
                            Click 'Apply Filters' to update the results
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        {{-- Availability Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Availability Status</label>
                            {{-- ADJUSTMENT HERE: Use flex-wrap to flow horizontally, with horizontal and vertical gaps --}}
                            <div class="flex flex-wrap gap-x-6 gap-y-2">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="available" class="availability-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="flex items-center gap-1.5 text-sm text-gray-700 group-hover:text-accent transition">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        Available
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="borrowed" class="availability-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="flex items-center gap-1.5 text-sm text-gray-700 group-hover:text-accent transition">
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                        Borrowed
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="archived" class="availability-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="flex items-center gap-1.5 text-sm text-gray-700 group-hover:text-accent transition">
                                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                        Archived
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="reserved" class="availability-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="flex items-center gap-1.5 text-sm text-gray-700 group-hover:text-accent transition">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                        Reserved
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Publication Year --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Publication Year</label>
                            <div class="flex gap-2 mb-2">
                                <button type="button" class="year-preset-btn px-3 py-1.5 text-xs border border-gray-300 rounded-md hover:bg-gray-50 transition" data-years="5">Last 5 Years</button>
                                <button type="button" class="year-preset-btn px-3 py-1.5 text-xs border border-gray-300 rounded-md hover:bg-gray-50 transition" data-years="10">Last 10 Years</button>
                                <button type="button" class="year-preset-btn px-3 py-1.5 text-xs border border-gray-300 rounded-md hover:bg-gray-50 transition" data-years="all">Classic</button>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">From</label>
                                    <input type="number" id="year-from" placeholder="1900" min="1900" max="{{ date('Y') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">To</label>
                                    <input type="number" id="year-to" placeholder="{{ date('Y') }}" min="1900" max="{{ date('Y') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- Language --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Language</label>
                            {{-- ADJUSTMENT HERE: Use flex-wrap to flow horizontally, with horizontal and vertical gaps --}}
                            <div class="flex flex-wrap gap-x-6 gap-y-2">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="English" class="language-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="text-sm text-gray-700 group-hover:text-accent transition">English</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="Filipino" class="language-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="text-sm text-gray-700 group-hover:text-accent transition">Filipino</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="Spanish" class="language-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="text-sm text-gray-700 group-hover:text-accent transition">Spanish</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="Chinese" class="language-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="text-sm text-gray-700 group-hover:text-accent transition">Chinese</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" value="Others" class="language-checkbox w-4 h-4 text-accent border-gray-300 rounded focus:ring-2 focus:ring-accent">
                                    <span class="text-sm text-gray-700 group-hover:text-accent transition">Others</span>
                                </label>
                            </div>
                        </div>

                        {{-- Sort By --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Sort By</label>
                            <select id="book-sort" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent text-sm">
                                <option value="title_asc">Title (A - Z)</option>
                                <option value="title_desc">Title (Z - A)</option>
                                <option value="year_asc">Year (Oldest)</option>
                                <option value="year_desc">Year (Newest)</option>
                            </select>


                        </div>


                    </div>

                    {{-- Active Filters Display --}}
                    <div id="active-filters" class="mt-4 pt-4 border-t border-gray-200 hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-medium text-gray-700">Active Filters:</span>
                                <div id="filter-tags" class="flex gap-2 flex-wrap"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-6 flex justify-between items-center">
                        <button id="clear-filters-btn" class="inline-flex cursor-pointer items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Clear Filters
                        </button>
                        <button id="apply-filters-btn" class="inline-flex cursor-pointer items-center gap-2 px-6 py-2 bg-accent text-white rounded-lg hover:bg-accent/90 transition text-sm font-semibold shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </div>

                {{-- Books Grid --}}
                <div id="books-container">
                    @include('partials.borrowers.book-grid', ['books' => $books])
                </div>

                {{-- Load More Button --}}
                <div id="load-more-container" class="mt-8 flex flex-col items-center gap-3 {{ $books->hasMorePages() ? '' : 'hidden' }}">
                    <button id="load-more-btn" class="inline-flex items-center gap-2 px-8 py-3 bg-accent cursor-pointer text-white rounded-lg hover:bg-accent/90 transition-all duration-200 text-sm font-semibold shadow-md hover:shadow-lg transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <span id="load-more-text">Load More Books</span>
                        <svg id="load-more-spinner" class="hidden animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </button>
                    <p id="books-count-info" class="text-sm text-gray-600">
                        Showing <span id="current-count">{{ $books->count() }}</span> of <span id="total-count">{{ $books->total() }}</span> books
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layout>

@vite('resources/js/pages/borrower/userSidebar.js')
@vite('resources/js/pages/borrower/bookCatalog.js')
