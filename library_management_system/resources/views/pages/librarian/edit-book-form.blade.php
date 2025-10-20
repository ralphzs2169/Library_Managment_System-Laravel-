<!-- Main Content Area -->
<div class="bg-white shadow-sm rounded-xl p-6 mt-5">
    <div class="flex justify-end mb-4">
        <button type="button" id="cancel-edit-book" class="cursor-pointer flex items-center gap-2 px-3 py-1.5 bg-accent text-white rounded-lg shadow hover:bg-accent-dark transition text-sm font-semibold" title="Return to Books List">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Return</span>
        </button>
    </div>

    <form class="edit-book-form" enctype="multipart/form-data" data-book-id="{{ $book->id }}">
        @csrf
        @method('PUT')

        {{-- Book id for AJAX updates --}}
        <input type="hidden" id="edit-book-id" name="book_id" value="{{ $book->id }}">
        <!-- Book Information -->
        <div>
            <h2 class="flex items-center gap-2 font-semibold text-lg mb-3">
                <img src="{{ asset('/build/assets/icons/add-book-information.svg') }}" alt="Book Info Icon" class="w-8 h-8">
                <span>Edit Book Information</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg bg-[#E8FCFF] text-gray-600 hover:bg-[#DFF9FF] transition relative">
                    <input type="file" id="edit-cover-input" name="edit-cover" accept="image/*" class="hidden">
                    <label for="edit-cover-input" id="edit-cover-drop-area" class="w-full h-full flex flex-col items-center justify-center cursor-pointer p-6">
                        <img id="edit-cover-preview" src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : '' }}" alt="Cover preview" class="w-32 h-40 object-cover rounded-md mb-2 {{ $book->cover_image ? '' : 'hidden' }}">
                        <span id="edit-cover-placeholder" class="text-center {{ $book->cover_image ? 'hidden' : '' }}">Click to upload cover</span>
                    </label>
                    <div id="edit-cover-error-placeholder" class="error-placeholder absolute left-4 bottom-2"></div>
                </div>

                <div class="md:col-span-2 flex flex-col gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Title Field --}}
                        <div class="field-container flex flex-col">
                            <label for="edit-title" class="block text-sm font-medium text-gray-700 mb-1">Book Title</label>
                            <div class="error-placeholder flex-1 text-sm min-h-[1em] mb-1" id="edit-title-error-placeholder"></div>
                            <input type="text" id="edit-title" name="edit-title" value="{{ $book->title ?? '' }}" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                        </div>
                        {{-- ISBN Field --}}
                        <div class="field-container flex flex-col">
                            <label for="edit-isbn" class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                            <div class="error-placeholder flex-1 text-sm min-h-[1em] mb-1" id="edit-isbn-error-placeholder"></div>
                            <input type="text" id="edit-isbn" name="edit-isbn" value="{{ $book->isbn ?? '' }}" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                        </div>
                    </div>
                    {{-- Description and Price Fields --}}
                    <div class="field-container">
                        <label for="edit-description" class="block text-sm font-medium text-gray-700 mb-1">Book Description</label>
                        <div class="error-placeholder" id="edit-description-error-placeholder"></div>
                        <textarea id="edit-description" name="edit-description" class="w-full border rounded-lg p-2.5 h-24 resize-none focus:outline-none focus:ring-2 focus:ring-accent">{{ $book->description ?? '' }}</textarea>
                    </div>
                    <div class="field-container">
                        <label for="edit-price" class="block text-sm font-medium text-gray-700 mb-1">Book Price</label>
                        <div class="error-placeholder" id="edit-price-error-placeholder"></div>
                        <input type="number" id="edit-price" name="edit-price" value="{{ $book->price ?? '' }}" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                    </div>
                </div>



            </div>
        </div>

        <!-- Author Information -->
        <h2 class="flex items-center gap-2 font-semibold text-lg mb-3">
            <span>Edit Author Information</span>
            <button type="button" id="edit-add-author-btn" class="px-2 py-1 border rounded text-accent hover:bg-accent hover:text-white transition">+</button>
        </h2>
        <div id="edit-authors-container">
            <!-- First author row with full labels -->
            @foreach ($book->authors as $i => $author)
            <div class="author-group grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="field-container flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <div class="error-placeholder flex-1 min-h-[1em] text-sm mb-1" id="edit-authors[{{ $i }}][firstname]-error-placeholder"></div>
                    <input type="text" name="edit-authors[{{ $i }}][firstname]" value="{{ $author->firstname }}" placeholder="Author's First Name" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="field-container flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <div class="error-placeholder flex-1 min-h-[1em] text-sm mb-1" id="edit-authors[{{ $i }}][lastname]-error-placeholder"></div>
                    <input type="text" name="edit-authors[{{ $i }}][lastname]" value="{{ $author->lastname }}" placeholder="Author's Last Name" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="field-container flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Initial</label>
                    <div class="error-placeholder flex-1 min-h-[1em] text-sm mb-1" id="edit-authors[{{ $i }}][middle_initial]-error-placeholder"></div>
                    <input type="text" name="edit-authors[{{ $i }}][middle_initial]" value="{{ $author->middle_initial }}" placeholder="M.I." class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
            </div>
            @endforeach
        </div>

        <!-- Publication Details -->
        <div class="mt-8">
            <h2 class="flex items-center gap-2 font-semibold text-lg mb-3">
                <span>Edit Publication Details</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Publisher Field --}}
                <div class="field-container flex flex-col">
                    <label for="edit-publisher" class="block text-sm font-medium text-gray-700 mb-1">Publisher</label>
                    <div class="error-placeholder flex-1 text-sm min-h-[1em] mb-1" id="edit-publisher-error-placeholder"></div>
                    <input type="text" id="edit-publisher" name="edit-publisher" value="{{ $book->publisher ?? '' }}" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                {{-- Publication Year Field --}}
                <div class="field-container flex flex-col">
                    <label for="edit-publication_year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <div class="error-placeholder flex-1 text-sm min-h-[1em] mb-1" id="edit-publication_year-error-placeholder"></div>
                    <input type="number" id="edit-publication_year" name="edit-publication_year" value="{{ $book->publication_year ?? '' }}" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                {{-- Category Field --}}
                <div class="field-container flex flex-col">
                    <label for="edit-category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <div class="error-placeholder flex-1 text-sm min-h-[1em] mb-1" id="edit-category_id-error-placeholder"></div>
                    <select id="edit-category_id" name="edit-category_id" class="w-full border rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-accent" data-endpoint="{{ route('librarian.genres.by-category') }}">
                        <option value="">Select Category...</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @if($bookCategory->id == $category->id) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">

                <div class="field-container">
                    <label for="edit-genre_id" class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                    <div class="error-placeholder" id="edit-genre_id-error-placeholder"></div>
                    <div class="relative">
                        <select id="edit-genre_id" name="edit-genre_id" data-selected="{{ old('genre_id', $book->genre_id ?? '') }}" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent" {{ $book->genre_id ? '' : 'disabled' }}>
                            <option value="">Select Genre...</option>
                            @foreach ($genres as $genre)
                            <option value="{{ $genre->id }}" @if($book->genre_id == $genre->id) selected @endif>{{ $genre->name }}</option>
                            @endforeach
                        </select>
                        <span id="edit-genre-loading" class="absolute right-3 top-3 hidden">
                            <svg class="animate-spin h-5 w-5 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                {{-- Language Field --}}
                <div class="field-container">
                    <label for="edit-language" class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                    <div class="error-placeholder" id="edit-language-error-placeholder"></div>
                    <select id="edit-language" name="edit-language" class="w-full border rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-accent">
                        <option value="">Select Language...</option>
                        <option value="English" @if($book->language == 'English') selected @endif>English</option>
                        <option value="Filipino" @if($book->language == 'Filipino') selected @endif>Filipino</option>
                        <option value="Spanish" @if($book->language == 'Spanish') selected @endif>Spanish</option>
                        <option value="Chinese" @if($book->language == 'Chinese') selected @endif>Chinese</option>
                        <option value="Others" @if($book->language == 'Others') selected @endif>Others</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="mt-6">
            <h2 class="flex items-center gap-2 font-semibold text-lg mb-3">
                <span>Copies</span>
            </h2>

            <div class="bg-white border rounded-lg shadow-sm p-4">
                @if($book->copies && $book->copies->count())
                <div class="overflow-x-auto">
                    <table id="edit-copies-table" class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 px-3">Copy No.</th>
                                <th class="py-2 px-3">Status</th>
                                <th class="py-2 px-3">Date Added</th>
                                <th class="py-2 px-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($book->copies as $index => $copy)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-3 align-top">{{ $index + 1 }}</td>
                                <td class="py-2 px-3 align-top">{{ $copy->status ?? 'Unknown' }}</td>
                                <td class="py-2 px-3 align-top">{{ $copy->created_at ? $copy->created_at->format('M d, Y') : '' }}</td>
                                <td class="py-2 px-3 align-top text-right">
                                    <button type="button" class="archive-copy-btn inline-flex items-center gap-2 px-3 py-1.5 bg-red-600 text-white rounded-md text-sm hover:opacity-90 transition" data-copy-id="{{ $copy->id }}" title="Archive Copy">
                                        Archive
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-sm text-gray-600">No copies found for this book.</div>
                @endif
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 mt-8">
            <button type="reset" class="border border-accent text-accent px-6 py-2.5 rounded-lg font-medium hover:bg-accent hover:text-white transition">
                Clear Form
            </button>
            <button type="submit" class="bg-accent text-white px-6 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
