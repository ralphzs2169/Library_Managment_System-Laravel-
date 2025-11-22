<!-- Main Content Area -->
<div class="bg-white shadow-sm rounded-xl p-6 mt-5">
    {{-- Book Summary Header with Back Button --}}
    <div id="edit-summary-header" class="mb-6 border border-gray-200 rounded-lg p-3 flex items-center justify-between bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-12 h-16 rounded-md overflow-hidden border border-gray-200 shadow-sm bg-white flex-shrink-0">
                <img id="edit-summary-cover" src="" alt="Cover" class="w-full h-full object-cover hidden">
                <div id="edit-summary-cover-placeholder" class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] text-center px-1">No Cover</div>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Editing</p>
                <p id="edit-summary-title" class="font-bold text-gray-900">—</p>
                <p class="text-xs text-gray-600 mt-0.5">
                    <span class="font-medium">ISBN:</span> <span id="edit-summary-isbn">—</span>
                    <span class="mx-2 text-gray-400">•</span>
                    <span class="font-medium">Author:</span> <span id="edit-summary-author">—</span>
                    <span class="mx-2 text-gray-400">•</span>
                    <span class="font-medium">Price:</span>
                    <span id="edit-summary-price">
                        {{ isset($book->price) ? number_format($book->price, 2) : '—' }}
                    </span>
                </p>
            </div>
        </div>

        <button type="button" id="cancel-edit-book" class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent/80 text-white rounded-lg transition text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to Catalog</span>
        </button>
    </div>

    <div id="pending-issue-banner" class="hidden mb-4 rounded-lg border-2 border-amber-400 bg-amber-50 px-4 py-3 flex items-start justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="text-sm text-amber-800 leading-snug">
                <p class="font-semibold">Pending Issue Review</p>
                <p>At least one copy of this book requires review approval. Please check the copies section below to resolve pending reports.</p>
            </div>
        </div>
        <div class="relative inline-block">
            <button type="button" id="scroll-to-copies-btn" class="flex items-center justify-center w-10 h-10 bg-amber-500 hover:bg-amber-400 cursor-pointer text-white rounded-full shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <!-- Line shaft -->
                    <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    <!-- Arrowhead -->
                    <polyline points="5,12 12,19 19,12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

            </button>
            <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                Review Copies
                <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
            </div>
        </div>

    </div>

    <form class="edit-book-form" enctype="multipart/form-data" data-book-id="{{ $book->id }}" novalidate>
        @csrf
        @method('PUT')

        {{-- Book id for AJAX updates --}}
        <input type="hidden" id="edit-book-id" name="book_id" value="{{ $book->id }}">

        {{-- Book Information Card --}}
        <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
            <h2 class="flex items-center gap-2 font-bold text-lg mb-5 text-gray-900 pb-3 border-b border-gray-200">
                <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('/build/assets/icons/add-book-information.svg') }}" alt="Book Info Icon" class="w-5 h-5">
                </div>
                <span>Book Information</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Cover Upload --}}
                <div class="flex flex-col items-center justify-center border-2 border-dashed border-accent/30 rounded-xl bg-accent/5 hover:bg-accent/10 transition relative group">
                    <input type="file" id="cover-input" name="cover" accept="image/*" class="hidden">
                    <label for="cover-input" id="cover-drop-area" class="w-full h-full flex flex-col items-center justify-center cursor-pointer p-6 min-h-[280px]">
                        <img id="cover-preview" alt="Cover preview" class="w-36 h-48 object-cover rounded-lg mb-3 shadow-md border-2 border-white">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span id="cover-placeholder" class="text-sm text-gray-600 font-medium">Click to upload cover</span>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 2MB</p>
                        </div>
                    </label>
                    <div id="cover-error-placeholder" class="error-placeholder absolute left-4 bottom-2"></div>
                </div>

                {{-- Book Fields --}}
                <div class="md:col-span-2 flex flex-col gap-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Title Field --}}
                        <div class="field-container flex flex-col">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Book Title</label>
                            <input type="text" id="title" name="title" placeholder="Enter book title" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                            <div class="error-placeholder text-xs mt-1" id="title-error-placeholder"></div>
                        </div>
                        {{-- ISBN Field --}}
                        <div class="field-container flex flex-col">
                            <label for="isbn" class="block text-sm font-semibold text-gray-700 mb-2">ISBN</label>
                            <input type="text" id="isbn" name="isbn" placeholder="Enter ISBN number" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm font-mono disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                            <div class="error-placeholder text-xs mt-1" id="isbn-error-placeholder"></div>
                        </div>
                    </div>
                    {{-- Description --}}
                    <div class="field-container">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Book Description</label>
                        <textarea id="description" placeholder="Provide a short summary of the book" name=" description" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 h-24 resize-none focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed"></textarea>
                        <div class="error-placeholder text-xs mt-1" id="description-error-placeholder"></div>
                    </div>
                    {{-- Price and Language Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="field-container">
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Book Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">₱</span>
                                <input type="number" step="0.01" id="price" placeholder="Enter book price" name="price" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 pl-8 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed" value="{{ isset($book->price) ? number_format($book->price, 2, '.', '') : '' }}">
                            </div>
                            <div class="error-placeholder text-xs mt-1" id="price-error-placeholder"></div>
                        </div>

                        <div class="field-container flex flex-col">
                            <label for="language" class="block text-sm font-semibold text-gray-700 mb-2">Language</label>
                            <select id="language" name="language" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                                <option value="">Select Language...</option>
                                <option value="English">English</option>
                                <option value="Filipino">Filipino</option>
                                <option value="Spanish">Spanish</option>
                                <option value="Chinese">Chinese</option>
                                <option value="Others">Others</option>
                            </select>
                            <div class="error-placeholder text-xs mt-1" id="language-error-placeholder"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category and Genre Card --}}
            <div class="mt-6">
                <h2 class="flex items-center gap-2 font-bold text-lg pt-3 text-gray-900 pb-3 border-t border-gray-200">
                    <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <span>Category & Genre</span>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field-container flex flex-col">
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                        <select id="category" name="category" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed" data-endpoint="{{ route('librarian.genres.by-category') }}">
                            <option disabled value="">Select Category...</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="error-placeholder text-xs mt-1" id="category-error-placeholder"></div>
                    </div>
                    <div class="field-container flex flex-col">
                        <label for="genre" class="block text-sm font-semibold text-gray-700 mb-2">Genre</label>
                        <div class="relative">
                            <select id="genre" name="genre" class="w-full bg-white border border-[#B1B1B1] disabled rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed" {{ $book->genre_id ? '' : 'disabled' }}></select>
                            <span id="genre-loading" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                                <svg class="animate-spin h-5 w-5 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="error-placeholder text-xs mt-1" id="genre-error-placeholder"></div>
                    </div>
                </div>
            </div>

            {{-- Author Information Card --}}
            <div class="mt-6">
                <h2 class="flex items-center gap-2 font-bold text-lg pt-3 text-gray-900 pb-3 border-t border-gray-200">
                    <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span>Author Information</span>
                </h2>

                <div class="author-group grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="field-container flex flex-col">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                        <input type="text" id="author_firstname" placeholder="Enter author's first name" name="author_firstname" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                        <div class="error-placeholder text-xs mt-1" id="author_firstname-error-placeholder"></div>
                    </div>
                    <div class="field-container flex flex-col">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="author_lastname" placeholder="Enter author's last name" name="author_lastname" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                        <div class="error-placeholder text-xs mt-1" id="author_lastname-error-placeholder"></div>
                    </div>
                    <div class="field-container flex flex-col">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Middle Initial</label>
                        <input type="text" id="author_middle_initial" placeholder="Enter author's middle initial" name="author_middle_initial" maxlength="1" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                        <div class="error-placeholder text-xs mt-1" id="author_middle_initial-error-placeholder"></div>
                    </div>
                </div>
            </div>

            {{-- Publication Details Card --}}
            <div class="mt-6">
                <h2 class="flex items-center gap-2 font-bold text-lg py-3 text-gray-900 border-t border-gray-200">
                    <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span>Publication Details</span>
                </h2>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Publisher Field --}}
                    <div class="field-container flex flex-col">
                        <label for="publisher" class="block text-sm font-semibold text-gray-700 mb-2">Publisher</label>
                        <input type="text" id="publisher" placeholder="Enter publisher name" name="publisher" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                        <div class="error-placeholder text-xs mt-1" id="publisher-error-placeholder"></div>
                    </div>
                    {{-- Publication Year Field --}}
                    <div class="field-container flex flex-col">
                        <label for="publication_year" class="block text-sm font-semibold text-gray-700 mb-2">Publication Year</label>
                        <input type="number" id="publication_year" placeholder="Enter publication year" name="publication_year" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed">
                        <div class="error-placeholder text-xs mt-1" id="publication_year-error-placeholder"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Classification Card --}}
        {{-- <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm"> --}}



        {{-- Copies Management Card --}}
        <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-5 pb-3 border-b border-gray-200 gap-4">
                <h2 class="flex items-center gap-2 font-bold text-lg text-gray-900">
                    <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span>Book Copies Management</span>
                    <span id="copies-count-badge" class="ml-2 bg-accent/10 text-accent font-bold px-3 py-1 rounded-full text-sm">

                    </span>
                </h2>

                <button type="button" id="add-copy-btn" class="px-4 py-2 text-xs bg-accent hover:bg-accent/80 cursor-pointer text-white rounded-lg transition shadow-sm font-semibold flex items-center gap-1.5 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Copy
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm rounded-lg ">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Copy No.</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Current Status</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Change Status</th>
                            <th class="py-3 px-4 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="copies-table-body" class="bg-white divide-y divide-gray-100">
                        <tr class="copies-empty">
                            <td colspan="4" class="py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 text-sm">Loading copies…</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Hidden input toggles to true when at least one issue has been resolved --}}
            <input type="hidden" name="pending_issue_resolved" id="pending_issue_resolved" value="false">

            {{-- Pagination Container --}}
            <div id="copies-pagination" class="hidden"></div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
            <button id="edit-form-reset-button" type="reset" class="px-6 py-3 border-2 border-accent text-accent rounded-lg font-semibold hover:bg-accent hover:text-white transition shadow-sm">
                Reset Changes
            </button>
            <button type="submit" class="px-6 py-3 bg-accent text-white rounded-lg font-semibold hover:bg-accent/90 transition shadow-md hover:shadow-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Changes
            </button>
        </div>
    </form>

    {{-- Scroll to Top helper --}}
    {{-- <div class="mt-6 flex justify-center">
        <button id="edit-form-scroll-top" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition text-sm shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
            <span>Back to top</span>
        </button>
    </div> --}}
</div>

@include('modals.issue-details')
