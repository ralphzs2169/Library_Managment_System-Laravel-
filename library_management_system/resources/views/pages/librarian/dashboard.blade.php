{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\dashboard.blade.php --}}
<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        {{-- Header --}}
        <div class="bg-white shadow-sm rounded-xl p-5 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg version="1.0" class="w-9 h-9" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" fill="#ffffff">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <g>
                                    <path fill="#ffffff" d="M40,64V4c0-2.211-1.789-4-4-4h-8c-2.211,0-4,1.789-4,4v60H40z"></path>
                                    <path fill="#ffffff" d="M20,64V20c0-2.211-1.789-4-4-4H8c-2.211,0-4,1.789-4,4v44H20z"></path>
                                    <path fill="#ffffff" d="M60,36c0-2.211-1.789-4-4-4h-8c-2.211,0-4,1.789-4,4v28h16V36z"></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Welcome back, <span class="font-semibold">{{ auth()->user()->firstname }}</span> • {{ now()->format('F d, Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button class="px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-lg font-semibold hover:border-accent hover:text-accent transition shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="hidden sm:inline">Export Report</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-sm rounded-xl p-5 mb-6">
            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Books</p>
                        <span class="text-2xl">📚</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalBooks) }}</h3>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Active Borrowers</p>
                        <span class="text-2xl">👥</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalActiveBorrowers) }}</h3>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Borrowed</p>
                        <span class="text-2xl">📖</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalBorrowedCopies) }}</h3>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Fines</p>
                        <span class="text-xl">⚠️</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">₱{{ number_format($totalUnpaidFines, 2) }}</h3>
                </div>
            </div>
        </div>

        {{-- Quick Actions & Most Borrowed --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Quick Actions</h2>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <button class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Add New Book</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <button class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Process Return</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <button class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Register Borrower</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <button class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Process Clearance</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Most Borrowed --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Most Borrowed Books</h2>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        @foreach($topBooks as $book)
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer group">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.png') }}" alt="{{ $book->title }}" class="w-10 h-14 rounded object-cover shadow-sm border border-gray-200 flex-shrink-0">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm text-gray-900 truncate" title="{{ $book->title }}">{{ $book->title }}</p>
                                    <div class="flex flex-wrap gap-1 mt-1 mb-1">
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-700">
                                            {{ $book->genre->category->name ?? 'N/A' }}
                                        </span>
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-100 text-indigo-700">
                                            {{ $book->genre->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">by {{ $book->author->formal_name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 pl-2">
                                <span class="text-lg font-bold text-gray-900">{{ $book->borrow_transactions_count }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-accent transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <a href="{{ route('librarian.books.index') }}" class="w-full mt-4 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        View All Books
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Book Management & Fees --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- ...existing code... --}}
        </div>

        {{-- Recent Activity & Due Today --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Recent Activity --}}
            <div class="bg-white rounded-xl border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Recent Activity</h2>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">Book Returned</p>
                                <p class="text-xs text-gray-600 mt-0.5"><span class="font-medium">Maria Santos</span> returned <span class="font-medium">"The Great Gatsby"</span></p>
                                <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">New Book Added</p>
                                <p class="text-xs text-gray-600 mt-0.5">Added <span class="font-medium">"To Kill a Mockingbird"</span> to catalog</p>
                                <p class="text-xs text-gray-400 mt-1">15 minutes ago</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">Book Reserved</p>
                                <p class="text-xs text-gray-600 mt-0.5"><span class="font-medium">John Reyes</span> reserved <span class="font-medium">"1984"</span></p>
                                <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">Overdue Notice</p>
                                <p class="text-xs text-gray-600 mt-0.5"><span class="font-medium">"Pride and Prejudice"</span> is 3 days overdue</p>
                                <p class="text-xs text-gray-400 mt-1">3 hours ago</p>
                            </div>
                        </div>
                    </div>

                    <button class="w-full mt-4 py-2.5 border-2 border-gray-200 text-gray-700 hover:border-accent hover:text-accent rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        View All Activity
                    </button>
                </div>
            </div>

            {{-- Due Today --}}
            <div class="bg-white rounded-xl border border-gray-100">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Due Today</h2>
                    <span class="px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full">12 items</span>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 text-xs font-bold text-blue-600">MS</div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900">Maria Santos</p>
                                <p class="text-xs text-gray-600 truncate">The Catcher in the Rye</p>
                                <p class="text-xs text-red-600 font-medium mt-1">Due: Today, 5:00 PM</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0 text-xs font-bold text-purple-600">JR</div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900">John Reyes</p>
                                <p class="text-xs text-gray-600 truncate">Harry Potter</p>
                                <p class="text-xs text-red-600 font-medium mt-1">Due: Today, 5:00 PM</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0 text-xs font-bold text-green-600">AC</div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900">Anna Cruz</p>
                                <p class="text-xs text-gray-600 truncate">The Hobbit</p>
                                <p class="text-xs text-red-600 font-medium mt-1">Due: Today, 5:00 PM</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0 text-xs font-bold text-amber-600">PG</div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900">Peter Garcia</p>
                                <p class="text-xs text-gray-600 truncate">The Lord of the Rings</p>
                                <p class="text-xs text-red-600 font-medium mt-1">Due: Today, 5:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <button class="w-full mt-4 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        View All (12)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Library Statistics --}}
        <div class="bg-white rounded-xl border border-gray-100">
            <div class="p-5 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Library Statistics</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Total Copies</p>
                        <p class="text-2xl font-bold text-gray-900">4,521</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Available</p>
                        <p class="text-2xl font-bold text-green-600">3,273</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Reserved</p>
                        <p class="text-2xl font-bold text-purple-600">89</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-center">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Damaged</p>
                        <p class="text-2xl font-bold text-red-600">14</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
