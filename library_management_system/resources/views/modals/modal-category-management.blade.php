{{-- filepath: c:\Users\Angela\library_management_system\resources\views\modals\modal-category-management.blade.php --}}

<!-- Add Category Modal -->
<div id="add-category-modal" class="fixed inset-0 bg-background-unfocused bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-200">
    <div id="add-category-modal-content" class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-200 scale-95 opacity-0">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                Add New Category
            </h2>
            <button id="close-add-category-modal" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="add-category-form" class="p-6" novalidate>
            <div class="mb-6">
                <label for="category_name" class="block text-sm font-semibold text-gray-700 mb-2">Category Name</label>
                <div id="category_name-error-placeholder" class="error-placeholder text-sm mb-1"></div>
                <input type="text" id="category_name" name="category_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" placeholder="e.g., Fiction, Non-Fiction">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-add-category" class="px-5 py-2.5 bg-secondary-light text-white cursor-pointer  rounded-lg hover:bg-secondary-light/90 transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg cursor-pointer transition shadow-sm">
                    Add Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="edit-category-modal" class="fixed inset-0 bg-background-unfocused bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-200">
    <div id="edit-category-modal-content" class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-200 scale-95 opacity-0">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                Edit Category
            </h2>
            <button id="close-edit-category-modal" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="edit-category-form" class="p-6" novalidate>
            <input type="hidden" id="edit-category-id" name="id">
            <div class="mb-6">
                <label for="updated_category_name" class="block text-sm font-semibold text-gray-700 mb-2">Category Name</label>
                <div id="updated_category_name-error-placeholder" class="error-placeholder text-sm mb-1"></div>
                <input type="text" id="updated_category_name" name="updated_category_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" placeholder="Enter category name">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-edit-category" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-secondary-light hover:bg-secondary-light/90 text-white rounded-lg  cursor-pointer transition shadow-sm">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Genre Modal -->
<div id="add-genre-modal" class="fixed inset-0 bg-background-unfocused bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-200">
    <div id="add-genre-modal-content" class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-200 scale-95 opacity-0">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                Add New Genre
            </h2>
            <button id="close-add-genre-modal" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="px-6 pt-6">
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-purple-700 font-medium mb-1">Adding genre to category:</p>
                <p class="text-lg font-bold text-purple-900" id="add-genre-category-name"></p>
            </div>
        </div>

        <form id="add-genre-form" class="px-6 pb-6" novalidate>
            <input type="hidden" id="add-genre-category-id" name="category_id">
            <div class="mb-6">
                <label for="genre_name" class="block text-sm font-semibold text-gray-700 mb-2">Genre Name</label>
                <div id="genre_name-error-placeholder" class="error-placeholder text-sm mb-1"></div>
                <input type="text" id="genre_name" name="genre_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" placeholder="e.g., Science Fiction, Biography">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-add-genre" class="px-5 py-2.5 bg-secondary-light text-white  cursor-pointer rounded-lg hover:bg-secondary-light/90 transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg cursor-pointer  transition shadow-sm">
                    Add Genre
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Genre Modal -->
<div id="edit-genre-modal" class="fixed inset-0 bg-background-unfocused bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-200">
    <div id="edit-genre-modal-content" class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-200 scale-95 opacity-0">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                Edit Genre
            </h2>
            <button id="close-edit-genre-modal" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="edit-genre-form" class="p-6" novalidate>
            <input type="hidden" id="edit-genre-id" name="id">
            <div class="mb-6">
                <label for="updated_genre_name" class="block text-sm font-semibold text-gray-700 mb-2">Genre Name</label>
                <div id="updated_genre_name-error-placeholder" class="error-placeholder text-sm mb-1"></div>
                <input type="text" id="updated_genre_name" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" placeholder="Enter genre name">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-edit-genre" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-all shadow-sm">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-secondary-light hover:bg-secondary-light/90 text-white rounded-lg cursor-pointer transition shadow-sm">
                    Update Genre
                </button>
            </div>
        </form>
    </div>
</div>
