<x-layout>
    <div class="max-w-7xl bg-background mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Smart Library Web System</h1>
            <p class="text-base text-gray-600">Search our collection of books. Log in to borrow, reserve, or manage.</p>
        </div>

        <!-- Search Bar -->
        <div class="mb-12 relative z-10">
            <div class="relative group">
                <div class="flex max-w-2xl mx-auto bg-white rounded-full shadow-md overflow-hidden">
                    <div class="flex-1 flex items-center px-8 gap-3 rounded-l-full shadow-md  border-2 border-transparent transition-all duration-300 hover:border-accent hover:shadow-lg focus-within:border-accent focus-within:shadow-lg">

                        <input type="text" id="public-book-search" class="flex-1 border-0 py-4 text-base outline-none focus:ring-0" placeholder="Search books by title, author, or ISBN..." autocomplete="off">
                        <div id="search-loading" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </div>
                    </div>
                    <button class="bg-accent hover:bg-accent/90 text-white px-6 py-4 text-base font-medium flex items-center gap-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>

                    </button>
                </div>

                <!-- Search Results Dropdown -->
                <div id="search-results-container" class="absolute top-full left-0 right-0 mx-auto max-w-2xl mt-2 bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100 hidden transition-all duration-200">
                    <div id="search-results-list" class="max-h-[400px] overflow-y-auto">
                        <!-- Results will be injected here -->
                    </div>
                    <div id="search-no-results" class="hidden p-4 text-center text-gray-500">
                        No books found matching your search.
                    </div>
                </div>
            </div>

            <!-- View Catalog Button -->
            <div class="flex justify-center mt-6">
                <a href="{{ route('borrowers.book-catalog')}}" class="inline-flex items-center gap-2 px-6 py-3 bg-accent hover:bg-accent/90 cursor-pointer text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    View Full Catalog
                </a>
            </div>
        </div>

        <!-- New Arrivals Section -->
        <div class="bg-white p-8 rounded-xl shadow-sm relative z-0">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">New Arrivals</h2>

            @if($newArrivals->count() > 0)
            <div class="relative flex items-center gap-4">
                <button class="bg-accent hover:bg-accent/90 border-0 w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 transition-all hover:scale-110 z-10" id="prevBtn">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div class="overflow-hidden flex-1">
                    <div class="flex gap-6 transition-transform py-2 duration-400 ease-in-out" id="carouselTrack">
                        @foreach($newArrivals as $book)
                        <a href="{{ route('borrowers.book-info', $book->id) }}" class="min-w-[220px] bg-gray-50 p-4 z-10 cursor-pointer flex flex-col transition-all hover:-translate-y-1 hover:shadow-md hover:shadow-accent border border-gray-300 ">
                            <div class="w-full aspect-[2/3] mb-4 overflow-hidden bg-gray-200">
                                @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-cyan-400 to-blue-500 text-white text-center p-4">
                                    <span class="font-semibold text-sm">{{ Str::limit($book->title, 30) }}</span>
                                </div>
                                @endif
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 mb-2 leading-snug min-h-[2.8rem]" title="{{ $book->title }}">
                                {{ Str::limit($book->title, 50) }}
                            </h3>

                            <!-- Category & Genre Badges -->
                            <div class="flex flex-wrap gap-1 mb-2">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    {{ $book->genre->category->name ?? 'N/A' }}
                                </span>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                    {{ $book->genre->name ?? 'N/A' }}
                                </span>
                            </div>

                            <p class="text-xs text-gray-600 mb-1">
                                by {{ $book->author->firstname }} {{ $book->author->lastname }}
                            </p>
                            <p class="text-xs text-gray-500 mb-2">
                                {{ $book->publication_year ?? 'N/A' }}
                            </p>
                            @php
                            $availableCopies = $book->copies->where('status', \App\Enums\BookCopyStatus::AVAILABLE)->count();
                            $totalCopies = $book->copies->count();
                            @endphp
                            @if($availableCopies > 0)
                            <span class="inline-block px-3 py-1.5 rounded bg-green-200 text-green-800 text-xs font-semibold mt-auto text-center">
                                Available ({{ $availableCopies }}/{{ $totalCopies }})
                            </span>
                            @else
                            <span class="inline-block px-3 py-1.5 rounded bg-gray-200 text-gray-800 text-xs font-semibold mt-auto text-center">
                                Unavailable
                            </span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>

                <button class="bg-accent hover:bg-accent/90 border-0 w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 transition-all hover:scale-110 z-10" id="nextBtn">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No new arrivals</h3>
                <p class="mt-1 text-sm text-gray-500">No books have been added recently.</p>
            </div>
            @endif
        </div>
    </div>
</x-layout>

@vite('resources/js/pages/borrower/searchBook.js')
@vite('resources/js/pages/borrower/carousel.js')
