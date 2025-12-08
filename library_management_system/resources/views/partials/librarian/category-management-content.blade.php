{{-- filepath: c:\Users\Angela\library_management_system\resources\views\partials\librarian\category-management-content.blade.php --}}
@if ($categories->isEmpty())
<div class="text-center py-16 sm:py-20">
    <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 rounded-full mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 sm:w-8 sm:h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
    </div>
    <h2 class="text-base sm:text-lg font-semibold text-gray-600 mb-2">No Categories Available</h2>
    <p class="text-sm text-gray-500">Click "Add Category" to create your first category</p>
</div>
@else
<div class="space-y-3 sm:space-y-4">
    @foreach ($categories as $category)
    @php
    $categoryGenreCount = $category->genres->count();
    $hasGenres = $categoryGenreCount > 0;
    @endphp

    <div id="category-section-{{ $category->id }}" class="category-item bg-white border border-gray-200 rounded-xl shadow-sm transition-all duration-200 hover:shadow-md" data-category-id="{{ $category->id }}">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 sm:p-5 gap-4">
            <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 truncate">{{ $category->name }}</h2>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-1">
                        <div id="genre-count-{{ $category->id }}" class="flex items-center gap-1 sm:gap-1.5 text-xs sm:text-sm text-gray-600 hover:text-accent transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="font-medium">{{ $categoryGenreCount }}</span>
                            <span class="hidden sm:inline">{{ Str::plural('genre', $categoryGenreCount) }}</span>
                        </div>
                        <div class="flex items-center gap-1 sm:gap-1.5 text-xs sm:text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="font-medium">{{ $category->books_count }}</span>
                            <span class="hidden sm:inline">books</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button id="edit-category-{{ $category->id }}" class="flex-1 sm:flex-initial cursor-pointer flex items-center justify-center gap-1 sm:gap-1.5 px-3 sm:px-4 py-2 bg-secondary-light hover:bg-secondary-light/90 text-white rounded-lg text-xs sm:text-sm font-medium transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="hidden sm:inline">Edit</span>
                </button>

                <div class="relative group flex-1 sm:flex-initial">
                    <button id="delete-category-{{ $category->id }}" class="w-full flex items-center justify-center gap-1 sm:gap-1.5 px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition shadow-sm {{ $hasGenres ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-red-500 hover:bg-red-600 cursor-pointer' }} text-white" {{ $hasGenres ? 'disabled' : '' }} data-has-genres="{{ $hasGenres ? 'true' : 'false' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="hidden sm:inline">Delete</span>
                    </button>
                    @if ($hasGenres)
                    <div class="hidden sm:block absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg">
                        Cannot delete category with existing genres
                        <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                    </div>
                    @endif
                </div>

                <button class="show-genres-btn cursor-pointer p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition" data-category-id="{{ $category->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500 dropdown-icon transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Genres Collapsible Section -->
        <div id="genres-section-{{ $category->id }}" class="genres-section overflow-hidden transition-all duration-300 ease-in-out max-h-0" style="opacity: 0;">
            <div class="px-4 sm:px-6 py-4 sm:py-5 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        {{ $category->name }} Genres
                    </h3>
                    <button class="open-add-genre-modal w-full sm:w-auto flex items-center cursor-pointer justify-center gap-2 px-4 py-2 bg-accent hover:bg-accent/90 text-white rounded-lg text-xs sm:text-sm transition shadow-sm" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Genre
                    </button>
                </div>

                @if ($categoryGenreCount === 0)
                <div class="text-center py-10 sm:py-12 bg-white rounded-lg border border-gray-200">
                    <div class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-xs sm:text-sm">No genres available for this category</p>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                    @foreach ($category->genres as $genre)
                    @php
                    $genreBooksCount = $genre->books_count ?? 0;
                    $hasBooks = $genreBooksCount > 0;
                    @endphp
                    <div class="genre-item flex flex-col gap-2 p-3 sm:p-4 bg-white rounded-lg border border-gray-200 hover:border-accent/50 hover:shadow-sm transition" data-genre-id="{{ $genre->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <span class="font-medium text-sm sm:text-base text-gray-900 truncate block">{{ $genre->name }}</span>
                                <div class="flex items-center gap-1 text-xs text-gray-600 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span class="font-medium">{{ $genreBooksCount }}</span>
                                    <span class="hidden sm:inline">{{ Str::plural('book', $genreBooksCount) }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                                <button id="edit-genre-{{ $genre->id }}" class="p-1.5 sm:p-2 bg-secondary-light cursor-pointer hover:bg-secondary-light/90 text-white rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <div class="relative group">
                                    <button id="delete-genre-{{ $genre->id }}" class="p-1.5 sm:p-2 rounded-lg transition {{ $hasBooks ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-600/90 text-white cursor-pointer' }}" {{ $hasBooks ? 'disabled' : '' }} data-has-books="{{ $hasBooks ? 'true' : 'false' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    @if ($hasBooks)
                                    <div class="hidden sm:block absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg z-10">
                                        Cannot delete genre with existing books
                                        <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
