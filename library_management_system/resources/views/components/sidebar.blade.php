<aside class="bg-secondary text-white w-72 min-h-screen p-6 hidden md:block fixed text-sm">
    <nav aria-label="Sidebar navigation">
        <ul class="space-y-2">
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2.5 px-3">
                <img src="{{ asset('build/assets/icons/dashboard.svg') }}" alt="Dashboard Icon" class="inline-block w-5 h-5 mr-3">
                <a href="{{ asset('dashboard') }}" class="sidebar-link">Dashboard</a>
            </li>

            <!-- Book Management -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2.5 px-3 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/book-management.svg') }}" alt="Book Management Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Book Management</span>
                </div>

                <ul class="mt-1 space-y-0.5">
                    <li class="hover:bg-secondary-light hover:text-white rounded py-2 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.books.index') }}" class="sidebar-link block ">All Books</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-2 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.books.create') }}" class="sidebar-link block">Add New Book</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-2 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.category-management') }}" class="sidebar-link block">Categories/Genres</a>
                    </li>
                </ul>
            </li>

            <!-- Borrowing -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2.5 px-3 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/borrowing.svg') }}" alt="Borrowing Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Borrowing</span>
                </div>

                <ul class="mt-1 space-y-0.5">
                    <li class="hover:bg-secondary-light hover:text-white rounded py-2 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=borrowings') }}" class="sidebar-link block">All Borrowings</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-2 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=borrowings/create') }}" class="sidebar-link block">Reservations</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-2 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=returns') }}" class="sidebar-link block">Overdues/Fines</a>
                    </li>
                </ul>
            </li>

            <!-- User Management -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2.5 px-3 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/users.svg') }}" alt="User Management Icon" class="inline-block w-5 h-5 mr-3">
                    <span>User Management</span>
                    <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" alt="Expand Icon" class="inline-block w-5 h-5 ml-auto dropdown-icon transition-transform duration-300">
                </div>

                <ul class="mt-1 space-y-0.5">
                    <li class="hover:bg-secondary-light hover:text-white rounded py-2 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users') }}" class="sidebar-link block">Borrowers</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-2 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users/staff') }}" class="sidebar-link block">Staff</a>
                    </li>
                </ul>
            </li>

            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2.5 px-3">
                <img src="{{ asset('build/assets/icons/activity-log.svg') }}" alt="Activity Log Icon" class="inline-block w-5 h-5 mr-3">
                <a href="{{ asset('librarian/activity-logs') }}" class="sidebar-link">Activity Log</a>
            </li>

            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2.5 px-3">
                <img src="{{ asset('build/assets/icons/semester.svg') }}" alt="Settings Icon" class="inline-block w-5 h-5 mr-3">
                <a href="{{ asset('librarian/semester-management') }}" class="sidebar-link">Semester Management</a>
            </li>
        </ul>
    </nav>
</aside>

@vite('resources/js/pages/librarian/sidebarNavigation.js')
