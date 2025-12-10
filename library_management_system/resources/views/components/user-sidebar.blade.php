{{-- filepath: c:\Users\Angela\library_management_system\resources\views\components\user-sidebar.blade.php --}}
<aside class="bg-secondary text-white w-60 min-h-screen max-h-screen p-3 hidden md:block fixed text-sm overflow-y-auto">
    <nav aria-label="Sidebar navigation">
        <ul class="space-y-1">
            {{-- All Books --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 py-2 px-3 {{ request()->routeIs('borrowers.book-catalog') && !request()->has('genre') ? 'bg-accent' : 'hover:bg-secondary-light' }}">
                <a href="{{ route('borrowers.book-catalog') }}" class="sidebar-link flex items-center {{ request()->routeIs('borrowers.book-catalog') && !request()->has('genre') ? 'text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-3" viewBox="0 0 18 16">
                        <path fill="#ffffff" d="M3.5 2h-3c-.275 0-.5.225-.5.5v11c0 .275.225.5.5.5h3c.275 0 .5-.225.5-.5v-11c0-.275-.225-.5-.5-.5zM3 5H1V4h2v1zm5.5-3h-3c-.275 0-.5.225-.5.5v11c0 .275.225.5.5.5h3c.275 0 .5-.225.5-.5v-11c0-.275-.225-.5-.5-.5zM8 5H6V4h2v1z" />
                        <path fill="#ffffff" d="m11.954 2.773l-2.679 1.35a.502.502 0 0 0-.222.671l4.5 8.93a.502.502 0 0 0 .671.222l2.679-1.35a.502.502 0 0 0 .222-.671l-4.5-8.93a.502.502 0 0 0-.671-.222z" />
                        <path fill="#ffffff" d="M14.5 13.5a.5.5 0 1 1-1 0a.5.5 0 0 1 1 0z" /></svg>
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
