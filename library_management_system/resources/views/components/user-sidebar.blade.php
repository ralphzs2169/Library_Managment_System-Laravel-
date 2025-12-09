{{-- filepath: c:\Users\Angela\library_management_system\resources\views\components\user-sidebar.blade.php --}}
<aside class="bg-secondary text-white w-60 min-h-screen max-h-screen p-3 hidden md:block fixed text-sm overflow-y-auto">
    <nav aria-label="Sidebar navigation">
        <ul class="space-y-1">
            {{-- All Books --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 py-2 px-3 {{ request()->routeIs('borrowers.book-catalog') && !request()->has('genre') ? 'bg-accent' : 'hover:bg-secondary-light' }}">
                <a href="{{ route('borrowers.book-catalog') }}" class="sidebar-link flex items-center {{ request()->routeIs('borrowers.book-catalog') && !request()->has('genre') ? 'text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>All Books</span>
                </a>
            </li>

            {{-- Divider --}}
            <li class="my-2">
                <hr class="border-t border-[#8593A6]/30">
            </li>

            {{-- Categories Section --}}
            <li class="mb-2">
                <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Browse by Category
                </div>
            </li>

            @foreach($categories as $category)
            @php
            $hasActiveGenre = $category->genres->contains('id', request()->get('genre'));
            @endphp
            <li class="category-item">
                <button type="button" class="category-toggle w-full rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3 flex items-center justify-between group">
                    <div class="flex items-center">
                        <span>{{ $category->name }}</span>
                    </div>
                    <svg class="chevron w-4 h-4 transition-transform duration-200 transform {{ $hasActiveGenre ? 'rotate-180' : '' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Genres Submenu --}}
                <ul class="genres-list {{ $hasActiveGenre ? '' : 'hidden' }} mt-1 border-l border-[#8593A6]/30 ml-4 space-y-1">
                    @forelse($category->genres as $genre)
                    @php
                    $isActive = request()->get('genre') == $genre->id;
                    @endphp
                    <li class="rounded-md cursor-pointer transition-colors duration-100 py-1.5 px-3 pl-4 {{ $isActive ? 'bg-accent' : 'hover:bg-secondary-light' }}">
                        <a href="{{ route('borrowers.book-catalog', ['genre' => $genre->id]) }}" class="sidebar-link flex items-center {{ $isActive ? 'text-white font-semibold' : 'text-[#8593A6] hover:text-white' }}">
                            <span>{{ $genre->name }}</span>
                        </a>
                    </li>
                    @empty
                    <li class="py-1.5 px-3 pl-9 text-gray-400 text-xs italic">
                        No genres available
                    </li>
                    @endforelse
                </ul>
            </li>
            @endforeach
        </ul>
    </nav>
</aside>
