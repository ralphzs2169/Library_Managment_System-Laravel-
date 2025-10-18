<x-layout >
    <section class="md:pl-78 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-6  ">
            <div class="flex justify-between items-center mb-10">
                <div class="flex items-center">
                    <img src="{{ asset('build/assets/icons/category.svg') }}" alt="Category Icon" class="inline-block w-10 h-10 mr-2">
                    <h1 class="text-2xl font-bold  rounded-xl">Categories & Genres</h1>
                </div>
                <button class="flex items-center  px-4 py-1.5 cursor-pointer shadow-sm bg-accent rounded-lg hover-scale-sm" id="open-add-category-modal">
                    <img src="{{ asset('build/assets/icons/plus-white.svg') }}" alt="Add Category Icon" class="inline-block w-7 h-7 mr-1">
                    <span class="text-white ">Add Category</span>
                </button>
            </div>
            
            <!-- Content Area (PLACEHOLDERS) -->
            <div class="grid gap-6">
                <!-- Placeholder Category Card 1 -->
                <div class="category-item text-gray-700 border border-[#B1B1B1] rounded-sm shadow-sm p-4 transition-all duration-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold">Placeholder Category 1</h2>
                            <div class="flex space-x-4 text-xs text-[#808080] mt-1">
                                <span><img src="{{ asset('build/assets/icons/genre.svg') }}" class="inline-block w-4 h-4 mr-1">0 genres</span>
                                <span><img src="{{ asset('build/assets/icons/category-book.svg') }}" class="inline-block w-4 h-4 mr-1">0 books</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="flex items-center text-sm px-4 py-1.5 bg-[#4285F4] text-white rounded-lg">
                                <img src="{{ asset('build/assets/icons/edit-white.svg') }}" class="inline-block w-4 h-4 mr-2">Edit
                            </button>
                            <button class="flex items-center text-sm px-4 py-1.5 bg-[#CF3030] text-white rounded-lg">
                                <img src="{{ asset('build/assets/icons/delete-white.svg') }}" class="inline-block w-4 h-4 mr-2">Delete
                            </button>
                            <button class="show-genres-btn flex items-center px-2 py-1 bg-[#808080] text-white rounded-lg" data-category-id="placeholder-1">
                                <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" class="inline-block w-6 h-6 dropdown-icon">
                            </button>
                        </div>
                    </div>
                    <!-- Static collapsed genres section (placeholder) -->
                    <div id="genres-placeholder-1" class="genres-section overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0 mt-3">
                        <div class="px-4 py-3 bg-gray-50 border border-[#B1B1B1] rounded">
                            <p class="text-gray-600">No genres yet. Use "Add Genre" to create one.</p>
                        </div>
                    </div>
                </div>
                <!-- Placeholder Category Card 2 -->
                <div class="category-item text-gray-700 border border-[#B1B1B1] rounded-sm shadow-sm p-4 transition-all duration-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold">Placeholder Category 2</h2>
                            <div class="flex space-x-4 text-xs text-[#808080] mt-1">
                                <span><img src="{{ asset('build/assets/icons/genre.svg') }}" class="inline-block w-4 h-4 mr-1">2 genres</span>
                                <span><img src="{{ asset('build/assets/icons/category-book.svg') }}" class="inline-block w-4 h-4 mr-1">8 books</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="flex items-center text-sm px-4 py-1.5 bg-[#4285F4] text-white rounded-lg">
                                <img src="{{ asset('build/assets/icons/edit-white.svg') }}" class="inline-block w-4 h-4 mr-2">Edit
                            </button>
                            <button class="flex items-center text-sm px-4 py-1.5 bg-[#CF3030] text-white rounded-lg">
                                <img src="{{ asset('build/assets/icons/delete-white.svg') }}" class="inline-block w-4 h-4 mr-2">Delete
                            </button>
                            <button class="show-genres-btn flex items-center px-2 py-1 bg-[#808080] text-white rounded-lg" data-category-id="placeholder-2">
                                <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" class="inline-block w-6 h-6 dropdown-icon">
                            </button>
                        </div>
                    </div>
                    <div id="genres-placeholder-2" class="genres-section overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0 mt-3">
                        <div class="px-4 py-3 bg-gray-50 border border-[#B1B1B1] rounded">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 bg-white rounded-lg shadow-sm border">Example Genre A</div>
                                <div class="p-3 bg-white rounded-lg shadow-sm border">Example Genre B</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Placeholder Category Card 3 -->
                <div class="category-item text-gray-700 border border-[#B1B1B1] rounded-sm shadow-sm p-4 transition-all duration-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold">Placeholder Category 3</h2>
                            <div class="flex space-x-4 text-xs text-[#808080] mt-1">
                                <span><img src="{{ asset('build/assets/icons/genre.svg') }}" class="inline-block w-4 h-4 mr-1">1 genre</span>
                                <span><img src="{{ asset('build/assets/icons/category-book.svg') }}" class="inline-block w-4 h-4 mr-1">5 books</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="flex items-center text-sm px-4 py-1.5 bg-[#4285F4] text-white rounded-lg">
                                <img src="{{ asset('build/assets/icons/edit-white.svg') }}" class="inline-block w-4 h-4 mr-2">Edit
                            </button>
                            <button class="flex items-center text-sm px-4 py-1.5 bg-[#CF3030] text-white rounded-lg">
                                <img src="{{ asset('build/assets/icons/delete-white.svg') }}" class="inline-block w-4 h-4 mr-2">Delete
                            </button>
                            <button class="show-genres-btn flex items-center px-2 py-1 bg-[#808080] text-white rounded-lg" data-category-id="placeholder-3">
                                <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" class="inline-block w-6 h-6 dropdown-icon">
                            </button>
                        </div>
                    </div>
                    <div id="genres-placeholder-3" class="genres-section overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0 mt-3">
                        <div class="px-4 py-3 bg-gray-50 border border-[#B1B1B1] rounded">
                            <p class="text-gray-600">One example genre available.</p>
                        </div>
                    </div>
                </div>
            </div>
            
                <!-- Add Category Modal -->
                <div id="add-category-modal" class="fixed inset-0 bg-background-unfocused bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-96 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Add New Category</h2>
                    <button id="close-add-category-modal" class="cursor-pointer hover-scale-md">
                    <img src="{{ asset('build/assets/icons/close-thin.svg') }}" alt="Close Add Category Modal" class="w-6 h-6">
                    </button>
                </div>
            
                <form id="add-category-form" novalidate>
                    <div class="mb-4">
                        <label for="category-name" class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                        <div id="category-name-error-placeholder" class="error-placeholder"></div>
                        <input type="text" id="category-name" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                            placeholder="Enter category name">
                    </div>
            
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-add-category" class="hover-scale-sm cursor-pointer px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="hover-scale-sm cursor-pointer px-4 py-2 bg-accent text-white rounded-lg hover:bg-opacity-90">
                            Add Category
                        </button>
                    </div>
                </form>
            </div>
                </div>
            
                <!-- Edit Category Modal -->
                <div id="edit-category-modal" class="fixed inset-0 bg-background-unfocused bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-96 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Edit Category</h2>
                    <button id="close-edit-category-modal" class="cursor-pointer hover-scale-md">
                    <img src="{{ asset('build/assets/icons/close-thin.svg') }}" alt="Close Edit Category Modal" class="w-6 h-6">
                    </button>
                </div>
            
                <form id="edit-category-form" novalidate>
                    <input type="hidden" id="edit-category-id" name="id">
                    <div class="mb-4">
                        <label for="edit-category-name" class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                        <div id="edit-category-name-error-placeholder" class="error-placeholder"></div>
                        <input type="text" id="edit-category-name" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                            placeholder="Enter category name">
                    </div>
            
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-edit-category" class="hover-scale-sm cursor-pointer px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="hover-scale-sm cursor-pointer px-4 py-2 bg-accent text-white rounded-lg hover:bg-opacity-90">
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
                </div>
            
                <!-- Add Genre Modal -->
                <div id="add-genre-modal" class="fixed inset-0 bg-background-unfocused bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-96 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Add New Genre</h2>
                    <button id="close-add-genre-modal" class="cursor-pointer hover-scale-md">
                    <img src="{{ asset('build/assets/icons/close-thin.svg') }}" alt="Close Add Genre Modal" class="w-6 h-6">
                    </button>
                </div>
            
                <div class="mb-4 p-3 bg-gray-100 rounded-lg">
                    <p class="text-sm text-gray-600">Adding genre to category:</p>
                    <p class="text-lg font-semibold text-gray-800" id="add-genre-category-name"></p>
                </div>
            
                <form id="add-genre-form" novalidate>
                    <input type="hidden" id="add-genre-category-id" name="category_id">
                    <div class="mb-4">
                        <label for="genre-name" class="block text-sm font-medium text-gray-700 mb-2">Genre Name</label>
                        <div id="genre-name-error-placeholder" class="error-placeholder"></div>
                        <input type="text" id="genre-name" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                            placeholder="Enter genre name">
                    </div>
            
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-add-genre" class="hover-scale-sm cursor-pointer px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="hover-scale-sm cursor-pointer px-4 py-2 bg-accent text-white rounded-lg hover:bg-opacity-90">
                            Add Genre
                        </button>
                    </div>
                </form>
            </div>
                </div>
            
            
                <!-- Edit Genre Modal -->
                <div id="edit-genre-modal" class="fixed inset-0 bg-background-unfocused bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-96 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Edit Genre</h2>
                    <button id="close-edit-genre-modal" class="cursor-pointer hover-scale-md">
                    <img src="{{ asset('build/assets/icons/close-thin.svg') }}" alt="Close Edit Genre Modal" class="w-6 h-6">
                    </button>
                </div>
            
                <form id="edit-genre-form" novalidate>
                    <input type="hidden" id="edit-genre-id" name="id">
                    <div class="mb-4">
                        <label for="edit-genre-name" class="block text-sm font-medium text-gray-700 mb-2">Genre Name</label>
                        <div id="edit-genre-name-error-placeholder" class="error-placeholder"></div>
                        <input type="text" id="edit-genre-name" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                            placeholder="Enter genre name">
                    </div>
            
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-edit-genre" class="hover-scale-sm cursor-pointer px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="hover-scale-sm cursor-pointer px-4 py-2 bg-accent text-white rounded-lg hover:bg-opacity-90">
                            Update Genre
                        </button>
                    </div>
                </form>
            </div>
                </div>
        </div>

</section>
    <style>
        button[disabled]:hover + .delete-tooltip,
        button[data-has-genres="true"]:hover + .delete-tooltip {
            opacity: 1;
        }
    </style>

    @vite('resources/js/pages/category-genre.js')
</x-layout>