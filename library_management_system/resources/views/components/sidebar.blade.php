<aside class="bg-secondary text-white w-72 min-h-screen p-6 hidden md:block fixed text-sm">
    <nav aria-label="Sidebar navigation">
        <ul class="space-y-2">
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light p-2">
                <img src="{{ asset('build/assets/icons/dashboard.svg') }}" alt="Dashboard Icon" class="inline-block w-7 h-7 mr-5">
                <a href="{{ asset('dashboard') }}" class="sidebar-link">Dashboard</a>
            </li>

            <!-- Book Management -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light p-2 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/book-management.svg') }}" alt="Book Management Icon" class="inline-block w-7 h-7 mr-5">
                    <span>Book Management</span>
                    <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" alt="Expand Icon" class="inline-block w-6 h-6 ml-auto dropdown-icon transition-transform duration-300">
                </div>

                <ul class="mt-2 space-y-1 collapsible-content overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0">
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.books.index') }}" class="sidebar-link block ">All Books</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.books.create') }}" class="sidebar-link block">Add New Book</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.category-management') }}" class="sidebar-link block">Categories/Genres</a>
                    </li>
                </ul>
            </li>

            <!-- Borrowing -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light p-2 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/borrowing.svg') }}" alt="Borrowing Icon" class="inline-block w-7 h-7 mr-5">
                    <span>Borrowing</span>
                    <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" alt="Expand Icon" class="inline-block w-6 h-6 ml-auto dropdown-icon transition-transform duration-300">
                </div>

                <ul class="mt-2 space-y-1 collapsible-content overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0">
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=borrowings') }}" class="sidebar-link block">All Borrowings</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=borrowings/create') }}" class="sidebar-link block">Reservations</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=returns') }}" class="sidebar-link block">Overdues/Fines</a>
                    </li>
                </ul>
            </li>

            <!-- User Management -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light p-2 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/users.svg') }}" alt="User Management Icon" class="inline-block w-7 h-7 mr-5">
                    <span>User Management</span>
                    <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" alt="Expand Icon" class="inline-block w-6 h-6 ml-auto dropdown-icon transition-transform duration-300">
                </div>

                <ul class="mt-2 space-y-1 collapsible-content overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0">
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users') }}" class="sidebar-link block">Students</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users/teachers') }}" class="sidebar-link block">Teachers</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users/staff') }}" class="sidebar-link block">Staff</a>
                    </li>
                </ul>
            </li>

            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light p-2">
                <img src="{{ asset('build/assets/icons/activity-log.svg') }}" alt="Activity Log Icon" class="inline-block w-7 h-7 mr-5">
                <a href="{{ asset('librarian/activity-logs') }}" class="sidebar-link">Activity Log</a>
            </li>
        </ul>
    </nav>
</aside>

@vite('resources/js/pages/librarian/sidebarNavigation.js')
