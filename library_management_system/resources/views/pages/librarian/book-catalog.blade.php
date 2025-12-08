{{-- filepath: c:\Users\Angela\library_management_system\resources\views\pages\librarian\books-list.blade.php --}}
<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <img src="{{ asset('build/assets/icons/book-management.svg')}}" alt="Book Catalog Icon" class="w-8 h-8">
                    </div>
                    <div>
                        <h1 clWass="text-2xl font-bold text-gray-900">Book Inventory</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage your library's book collection</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-sm">Total Books</span>
                    <span class="bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">{{ $books->total() }}</span>
                </div>
            </div>
        </div>

        {{-- <!-- Main Content --2 --}}
        <div id="books-filters" class="bg-white shadow-sm rounded-xl p-6">

            <!-- Search and Filters -->
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <img src="{{ asset('build/assets/icons/search.svg') }}" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                        <input type="text" id="books-search" placeholder="Search by title, author, or ISBN..." class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition" name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <select id="books-category-filter" name="category" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="">Category: All</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    <select id="books-sort" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Sort: Newest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Sort: Oldest First</option>
                        <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Sort: Title (A-Z)</option>
                        <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Sort: Title (Z-A)</option>
                        <option value="copies_asc" {{ request('sort') === 'copies_asc' ? 'selected' : '' }}>Sort: Fewest Copies First</option>
                        <option value="copies_desc" {{ request('sort') === 'copies_desc' ? 'selected' : '' }}>Sort: Most Copies First</option>
                    </select>
                    <select id="books-status-filter" name="status" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent transition bg-white">
                        <option value="all">Status: All</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Status: Available Copies</option>
                        <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Status: Borrowed Copies</option>
                        <option value="damaged" {{ request('status') === 'damaged' ? 'selected' : '' }}>Status: Damaged Copies</option>
                        <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Status: Lost Copies</option>
                        <option value="pending_issue_review" {{ request('status') === 'pending_issue_review' ? 'selected' : '' }}>Status: Pending Review Copies</option>
                    </select>
                    <button type="button" id="reset-books-filters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition whitespace-nowrap">
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div id="books-table-container">
                @include('partials.librarian.book-catalog-table', ['books' => $books])
            </div>
        </div>

        <div id="edit-book-form-container" data-book-id="" class="hidden">
            @include('pages.librarian.edit-book-form', [
            'book' => new \App\Models\Book,
            'categories' => $categories
            ])
        </div>

        @vite('resources/js/pages/librarian/bookCatalog.js')
    </section>
</x-layout>
