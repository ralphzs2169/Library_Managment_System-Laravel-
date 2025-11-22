<x-layout>
    <section class="md:pl-72 p-6 pt-4 min-h-screen bg-background">

        <!-- Book Summary Header (matches edit page) -->
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <img src="{{ asset('build/assets/icons/add-book-white.svg ')}}" alt="Add Book Icon" class="w-10 h-10">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Add New Book</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Add new titles to your library's book collection</p>
                    </div>
                </div>

            </div>
        </div>


        <!-- Main Content Area -->
        <div class="bg-white shadow-sm rounded-xl p-6 mt-5">
            <form id="add-book-form" action="{{ route('librarian.books.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <!-- Book Information Card -->
                <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
                    <h2 class="flex items-center gap-2 font-bold text-lg mb-5 text-gray-900 pb-3 border-b border-gray-200">
                        <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('/build/assets/icons/add-book-information.svg') }}" alt="Book Info Icon" class="w-5 h-5">
                        </div>
                        <span>Book Information</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Book Fields -->
                        <div class="md:col-span-2 flex flex-col gap-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Title Field -->
                                <div class="field-container flex flex-col">
                                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Book Title</label>
                                    <input type="text" id="title" name="title" placeholder="Enter book title" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                                    <div class="error-placeholder text-xs mt-1" id="title-error-placeholder"></div>
                                </div>
                                <!-- ISBN Field -->
                                <div class="field-container flex flex-col">
                                    <label for="isbn" class="block text-sm font-semibold text-gray-700 mb-2">ISBN</label>
                                    <input type="text" id="isbn" name="isbn" placeholder="Enter ISBN number" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                                    <div class="error-placeholder text-xs mt-1" id="isbn-error-placeholder"></div>
                                </div>
                            </div>
                            <!-- Description -->
                            <div class="field-container">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Book Description</label>
                                <textarea id="description" name="description" placeholder="Provide a short summary of the book" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 h-24 resize-none focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm"></textarea>
                                <div class="error-placeholder text-xs mt-1" id="description-error-placeholder"></div>
                            </div>
                            <!-- Price and Language Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="field-container">
                                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Book Price</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">₱</span>
                                        <input type="number" step="0.01" id="price" name="price" placeholder="Enter book price" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 pl-8 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                                    </div>
                                    <div class="error-placeholder text-xs mt-1" id="price-error-placeholder"></div>
                                </div>
                                <div class="field-container flex flex-col">
                                    <label for="language" class="block text-sm font-semibold text-gray-700 mb-2">Language</label>
                                    <select id="language" name="language" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
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

                        <!-- Cover Upload -->
                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-accent/30 rounded-xl bg-accent/5 hover:bg-accent/10 transition relative group">
                            <input type="file" id="cover-input" name="cover" accept="image/*" class="hidden">
                            <label for="cover-input" id="cover-drop-area" class="w-full h-full flex flex-col items-center justify-center cursor-pointer p-6 min-h-[280px]">
                                <img id="cover-preview" src="" alt="Cover preview" class="w-36 h-48 object-cover rounded-lg mb-3 shadow-md border-2 border-white hidden">
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
                    </div>
                </div>

                <!-- Category and Genre Card -->
                <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm mt-6">
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
                            <select id="category" name="category" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm" data-endpoint="{{ route('librarian.genres.by-category') }}">
                                <option value="">Select Category...</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="error-placeholder text-xs mt-1" id="category-error-placeholder"></div>
                        </div>
                        <div class="field-container flex flex-col">
                            <label for="genre" class="block text-sm font-semibold text-gray-700 mb-2">Genre</label>
                            <div class="relative">
                                <select id="genre" name="genre" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm" disabled>
                                    <option value="">Select Genre...</option>
                                    @foreach ($genres as $genre)
                                    <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                                    @endforeach
                                </select>
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

                <!-- Author Information Card -->
                <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm mt-6">
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
                            <input type="text" id="author_firstname" name="author_firstname" placeholder="Enter author's first name" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                            <div class="error-placeholder text-xs mt-1" id="author_firstname-error-placeholder"></div>
                        </div>
                        <div class="field-container flex flex-col">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                            <input type="text" id="author_lastname" name="author_lastname" placeholder="Enter author's last name" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                            <div class="error-placeholder text-xs mt-1" id="author_lastname-error-placeholder"></div>
                        </div>
                        <div class="field-container flex flex-col">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Middle Initial</label>
                            <input type="text" id="author_middle_initial" name="author_middle_initial" placeholder="Enter author's middle initial" maxlength="1" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                            <div class="error-placeholder text-xs mt-1" id="author_middle_initial-error-placeholder"></div>
                        </div>
                    </div>
                </div>

                <!-- Publication Details Card -->
                <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm mt-6">
                    <h2 class="flex items-center gap-2 font-bold text-lg py-3 text-gray-900 border-t border-gray-200">
                        <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span>Publication Details</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="field-container flex flex-col">
                            <label for="publisher" class="block text-sm font-semibold text-gray-700 mb-2">Publisher</label>
                            <input type="text" id="publisher" name="publisher" placeholder="Enter publisher name" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                            <div class="error-placeholder text-xs mt-1" id="publisher-error-placeholder"></div>
                        </div>
                        <div class="field-container flex flex-col">
                            <label for="publication_year" class="block text-sm font-semibold text-gray-700 mb-2">Publication Year</label>
                            <input type="number" id="publication_year" name="publication_year" placeholder="Enter publication year" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                            <div class="error-placeholder text-xs mt-1" id="publication_year-error-placeholder"></div>
                        </div>
                        <div class="field-container flex flex-col">
                            <label for="copies_available" class="block text-sm font-semibold text-gray-700 mb-2">No. of Initial Copies</label>
                            <input type="number" id="copies_available" name="copies_available" placeholder="Number of Copies" class="w-full bg-white border border-[#B1B1B1] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition text-sm">
                            <div class="error-placeholder text-xs mt-1" id="copies_available-error-placeholder"></div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <button type="reset" class="px-6 py-3 border-2 border-accent text-accent rounded-lg font-semibold hover:bg-accent hover:text-white transition shadow-sm">
                        Clear Form
                    </button>
                    <button type="submit" class="px-6 py-3 bg-accent text-white rounded-lg font-semibold hover:bg-accent/90 transition shadow-md hover:shadow-lg flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Book
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-layout>

@vite('resources/js/pages/librarian/addNewBook.js')
