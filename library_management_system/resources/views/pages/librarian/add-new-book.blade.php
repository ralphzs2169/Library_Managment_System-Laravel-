<x-layout>
    <section class="md:pl-78 p-6 pt-4 min-h-screen bg-background">

        <!-- Header -->
        <div class="bg-white shadow-sm rounded-xl p-5">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('build/assets/icons/add-book-logo.svg') }}" alt="Category Icon" class="inline-block w-10 h-10 mr-2">
                    <h1 class="text-2xl font-bold rounded-xl">Add New Book</h1>
                </div>
                
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white shadow-sm rounded-xl p-6 mt-5">
            <form id="add-book-form" action="{{ route('librarian.books.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
            <!-- Book Information -->
            <div>
                <h2 class="flex items-center gap-2 font-semibold text-lg mb-3">
                <img src="{{ asset('/build/assets/icons/add-book-information.svg') }}" alt="Book Info Icon" class="w-8 h-8">
                 <span>Book Information</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2 flex flex-col gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Title Field --}}
                        <div class="field-container">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Book Title</label>
                            <div class="error-placeholder" id="title-error-placeholder"></div>
                            <input type="text" id="title" name="title" placeholder="Enter the complete book title"
                            class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                        </div>
                        {{-- ISBN Field --}}
                        <div class="field-container">
                            <label for="isbn" class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                            <div class="error-placeholder" id="isbn-error-placeholder"></div>
                            <input type="text" id="isbn" name="isbn" placeholder="123-4-567891-23-4"
                            class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                        </div>
                    </div>
                    {{-- Description and Price Fields --}}
                    <div class="field-container" >
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Book Description</label>
                        <div class="error-placeholder" id="description-error-placeholder"></div>
                        <textarea id="description" name="description" placeholder="Provide a brief description of the book content..."
                            class="w-full border rounded-lg p-2.5 h-24 resize-none focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
                    </div>
                     <div class="field-container">
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Book Price</label>
                        <div class="error-placeholder" id="price-error-placeholder"></div>
                        <input type="number" id="price" name="price" placeholder="Enter the book price"
                        class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                    </div>
                </div>


                <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg bg-[#E8FCFF] text-gray-600 hover:bg-[#DFF9FF] transition relative">
                    <input type="file" id="cover-input" name="cover" accept="image/*" class="hidden">
                    <label for="cover-input" id="cover-drop-area" class="w-full h-full flex flex-col items-center justify-center cursor-pointer p-6">
                        <img id="cover-preview" src="" alt="Cover preview" class="hidden w-32 h-40 object-cover rounded-md mb-2">
                        <span id="cover-placeholder" class="text-center">Click to upload cover</span>
                    </label>
                    <div id="cover-error-placeholder" class="error-placeholder absolute left-4 bottom-2"></div>
                </div>
                </div>
            </div>

        <!-- Author Information -->
         <h2 class="flex items-center gap-2 font-semibold text-lg mb-3">
            <span>Author Information</span>
            <button type="button" id="add-author-btn" class="px-2 py-1 border rounded text-accent hover:bg-accent hover:text-white transition">+</button>
        </h2>
        <div id="authors-container">
            <!-- First author row with full labels -->
            <div class="author-group grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="field-container"> 
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                     <div class="error-placeholder" id="authors[0][firstname]-error-placeholder"></div>
                    <input type="text" name="authors[0][firstname]" placeholder="Author's First Name" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="field-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                     <div class="error-placeholder" id="authors[0][lastname]-error-placeholder"></div>
                    <input type="text" name="authors[0][lastname]" placeholder="Author's Last Name" class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="field-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Initial</label>
                     <div class="error-placeholder" id="authors[0][middle_initial]-error-placeholder"></div>
                    <input type="text" name="authors[0][middle_initial]" placeholder="M.I." class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
            </div>
        </div>




        <!-- Publication Details -->
        <div class="mt-8">
            <h2 class="flex items-center gap-2 font-semibold text-lg mb-3">
            <span>Publication Details</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Publisher Field --}}
            <div class="field-container">
                <label for="publisher" class="block text-sm font-medium text-gray-700 mb-1">Publisher</label>
                <div class="error-placeholder" id="publisher-error-placeholder"></div>
                <input type="text" id="publisher" name="publisher" placeholder="Publishing Company Name"
                class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
            </div>
            {{-- Publication Year Field --}}
            <div class="field-container">
                <label for="publication_year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                  <div class="error-placeholder" id="publication_year-error-placeholder"></div>
                <input type="number" id="publication_year" name="publication_year" placeholder="2025"
                class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
            </div>
            {{-- Category Field --}}
            <div class="field-container">
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                  <div class="error-placeholder" id="category_id-error-placeholder"></div>
                <select id="category_id" name="category_id"
                    class="w-full border rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-accent"
                    data-endpoint="{{ route('librarian.genres.by-category') }}">
                    <option value="">Select Category...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            
            <div class="field-container">
                <label for="genre_id" class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                  <div class="error-placeholder" id="genre_id-error-placeholder"></div>
                <div class="relative">
                    <select id="genre_id" name="genre_id"
                        class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent"
                        disabled>
                        <option value="">Select Genre...</option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                        @endforeach
                    </select>
                    <span id="genre-loading" class="absolute right-3 top-3 hidden">
                        <svg class="animate-spin h-5 w-5 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </span>
                </div>
            </div>
            {{-- Language Field --}}
            <div class="field-container">
                <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                  <div class="error-placeholder" id="language-error-placeholder"></div>
                <select id="language" name="language"
                class="w-full border rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-accent">
                <option value="">Select Language...</option>
                <option value="English">English</option>
                <option value="Filipino">Filipino</option>
                <option value="Spanish">Spanish</option>
                <option value="Chinese">Chinese</option>
                <option value="Others">Others</option>
                </select>
            </div>
            {{-- Initail number of Copies Field --}}
            <div class="field-container">
                <label for="copies_available" class="block text-sm font-medium text-gray-700 mb-1">No. of Initial Copies</label>
                  <div class="error-placeholder" id="copies_available-error-placeholder"></div>
                <input type="number" id="copies_available"  name="copies_available" placeholder="Number of Copies"
                class="w-full border rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-accent">
            </div>
            </div>
        </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 mt-8">
                    <button type="reset"
                    class="border border-accent text-accent px-6 py-2.5 rounded-lg font-medium hover:bg-accent hover:text-white transition">
                    Clear Form
                    </button>
                    <button  type="submit"
                    class="bg-accent text-white px-6 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                    Save Book
                    </button>
                </div>
            </form>
        </div>

    </section>
 </x-layout>
 
 @vite('resources/js/pages/add-new-book.js')