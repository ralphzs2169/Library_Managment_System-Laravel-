<x-layout>
    <section class="md:pl-78 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('build/assets/icons/category.svg') }}" alt="Category Icon" class="inline-block w-10 h-10 mr-2">
                    <h1 class="text-2xl font-bold rounded-xl">Categories & Genres</h1>
                </div>
                <button class="flex items-center px-4 py-1.5 cursor-pointer shadow-sm bg-accent rounded-lg hover-scale-sm" id="open-add-category-modal">
                    <img src="{{ asset('build/assets/icons/plus-white.svg') }}" alt="Add Category Icon" class="inline-block w-7 h-7 mr-1">
                    <span class="text-white">Add Category</span>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white shadow-sm rounded-xl p-6 mt-5">
            {{-- <select name="action" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 cursor-pointer shadow-sm outline-none focus:ring-0">
            <option value="">All Actions</option>
            <option value="create" >Create</option>
            <option value="update" >Update</option>
            <option value="delete" >Delete</option>
        </select> --}}
            @if ($categories->isEmpty())
            <div class="text-center py-20">
                <h2 class="text-xl font-semibold text-gray-600">No Categories Available</h2>
                <p class="text-gray-500 mt-2">Please add a new category to get started.</p>
            </div>
            @else
            @foreach ($categories as $category)
            @php
            $categoryGenreCount = $category->genres->count();
            $hasGenres = $categoryGenreCount > 0;
            @endphp

            <div id="category-section-{{ $category->id }}" class="category-item text-gray-700 border border-[#B1B1B1] rounded-sm shadow-sm mb-6 transition-all duration-100" data-category-id="{{ $category->id }}">
                <div class="flex items-center justify-between px-4 py-2">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $category->name }}</h2>
                        <div class="flex space-x-4">
                            <div id="genre-count-{{ $category->id }}">
                                <img src="{{ asset('build/assets/icons/genre.svg') }}" class="inline-block w-4 h-4">
                                <span class="text-[#808080] text-xs hover:underline cursor-pointer">
                                    {{ $categoryGenreCount }} {{ Str::plural('genre', $categoryGenreCount) }}
                                </span>
                            </div>
                            <div>
                                <img src="{{ asset('build/assets/icons/category-book.svg') }}" class="inline-block w-4 h-4">
                                <span class="text-[#808080] text-xs hover:underline cursor-pointer">0 books</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-2 items-center">
                        <button id="edit-category-{{ $category->id }}" class="flex items-center text-white text-sm px-5 py-1.5 cursor-pointer shadow-sm bg-[#4285F4] rounded-lg hover-scale-sm">
                            <img src="{{ asset('build/assets/icons/edit-white.svg') }}" class="inline-block w-4 h-4 mr-1.5">Edit
                        </button>

                        <div class="relative inline-block">
                            <button id="delete-category-{{ $category->id }}" class="flex items-center px-5 py-1.5 shadow-sm rounded-lg text-sm transition-all {{ $hasGenres ? 'bg-[#CF3030] opacity-55 cursor-not-allowed' : 'bg-[#CF3030] cursor-pointer hover-scale-sm hover:bg-[#B82828]' }}" {{ $hasGenres ? 'disabled' : '' }} data-has-genres="{{ $hasGenres ? 'true' : 'false' }}">
                                <img src="{{ asset('build/assets/icons/delete-white.svg') }}" class="inline-block w-4 h-4 {{ $hasGenres ? 'opacity-70' : '' }}">
                                <span class="text-white ml-1.5">Delete</span>
                            </button>
                            @if ($category->genres()->exists())
                            <div class="delete-tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                                Cannot delete category<br>with existing genres
                                <div class="absolute top-full right-6 border-4 border-transparent border-t-white"></div>
                            </div>
                            @endif
                        </div>

                        <button class="show-genres-btn flex items-center px-1.5 py-1 cursor-pointer shadow-sm bg-[#808080] rounded-lg hover-scale-sm" data-category-id="{{ $category->id }}">
                            <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" class="inline-block w-6 h-6 dropdown-icon transition-transform duration-300">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Genres Collapsible Section -->
            <div id="genres-section-{{ $category->id }}" class="genres-section overflow-hidden transition-all duration-300 ease-in-out max-h-0" style="opacity: 0;">
                <div class="px-6 py-4 bg-gray-50 border-x border-b border-[#B1B1B1] mb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">{{ $category->name }} Genres</h3>
                        <button class="open-add-genre-modal flex items-center px-4 py-1.5 cursor-pointer shadow-sm bg-accent rounded-lg hover-scale-sm" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">
                            <img src="{{ asset('build/assets/icons/plus-white.svg') }}" alt="Add Genre Icon" class="inline-block w-5 h-5 mr-1">
                            <span class="text-white text-sm">Add Genre</span>
                        </button>
                    </div>

                    @if ($categoryGenreCount === 0)
                    <div class="text-center py-8">
                        <p class="text-gray-500">No genres available for this category.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($category->genres as $genre)
                        <div class="genre-item flex items-center justify-between p-3 bg-white rounded-lg shadow-sm border border-gray-200" data-genre-id="{{ $genre->id }}">
                            <span class="font-medium">{{ $genre->name }}</span>
                            <div class="flex space-x-2">
                                <button id="edit-genre-{{ $genre->id }}" class="px-3 py-1 text-sm bg-[#4285F4] cursor-pointer text-white rounded hover-scale-sm shadow-sm">
                                    <img src="{{ asset('build/assets/icons/edit-white.svg') }}" alt="Edit" class="inline-block w-3 h-3">
                                </button>
                                <button id="delete-genre-{{ $genre->id }}" class="px-3 py-1 text-sm bg-[#CF3030] cursor-pointer text-white rounded hover-scale-sm shadow-sm">
                                    <img src="{{ asset('build/assets/icons/delete-white.svg') }}" alt="Delete" class="inline-block w-3 h-3">
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>
            </div>

            @endforeach
            @endif
        </div>

        @include('modals.modal-category-management')
    </section>

    <style>
        button[disabled]:hover+.delete-tooltip,
        button[data-has-genres="true"]:hover+.delete-tooltip {
            opacity: 1;
        }

    </style>

    @vite('resources/js/pages/librarian/categoryGenre.js')
</x-layout>
