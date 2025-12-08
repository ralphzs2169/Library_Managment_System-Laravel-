{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\category-management.blade.php --}}
<x-layout>
    <section class="md:pl-72 p-4 sm:p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-4 sm:p-5 mb-2 sm:mb-3">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Categories & Genres</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Manage book categories and their genres</p>
                    </div>
                </div>
                <button class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent/90 text-white rounded-lg transition shadow-sm cursor-pointer" id="open-add-category-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Category</span>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div id="categories-container" class="bg-white shadow-sm rounded-xl p-4 sm:p-6">
            @include('partials.librarian.category-management-content', ['categories' => $categories])
        </div>

        @include('modals.modal-category-management')
    </section>

    @vite('resources/js/pages/librarian/categoryGenre.js')
</x-layout>
