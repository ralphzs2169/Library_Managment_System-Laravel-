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
                <label for="category_name" class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                <div id="category_name-error-placeholder" class="error-placeholder"></div>
                <input type="text" id="category_name" name="category_name" required 
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
                <label for="updated_category_name" class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                <div id="updated_category_name-error-placeholder" class="error-placeholder"></div>
                <input type="text" id="updated_category_name" name="updated_category_name" required 
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
                <label for="genre_name" class="block text-sm font-medium text-gray-700 mb-2">Genre Name</label>
                <div id="genre_name-error-placeholder" class="error-placeholder"></div>
                <input type="text" id="genre_name" name="genre_name" required 
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
                <label for="updated_genre_name" class="block text-sm font-medium text-gray-700 mb-2">Genre Name</label>
                <div id="updated_genre_name-error-placeholder" class="error-placeholder"></div>
                <input type="text" id="updated_genre_name" name="name" required 
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