{{-- filepath: c:\Users\Angela\library_management_system\resources\views\components\sidebar.blade.php --}}
<aside class="bg-secondary text-white w-66 min-h-screen p-3 hidden md:block fixed text-sm overflow-y-auto">
    <nav aria-label="Sidebar navigation">
        <ul class="space-y-1">
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <img src="{{ asset('build/assets/icons/dashboard.svg') }}" alt="Dashboard Icon" class="inline-block w-5 h-5 mr-3">
                <a href="{{ asset('dashboard') }}" class="sidebar-link">Dashboard</a>
            </li>

            <!-- Book Management -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/book-management.svg') }}" alt="Book Management Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Book Management</span>
                </div>

                <ul class="mt-0.5 space-y-0">
                    <li class="hover:bg-secondary-light hover:text-white rounded py-1.5 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.books.index') }}" class="sidebar-link block ">Book Catalog</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-1.5 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.books.create') }}" class="sidebar-link block">Add New Book</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-1.5 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.category-management') }}" class="sidebar-link block">Categories/Genres</a>
                    </li>
                </ul>
            </li>

            <!-- Borrowing -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/borrowing.svg') }}" alt="Borrowing Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Circulation Records</span>
                </div>

                <ul class="mt-0.5 space-y-0">
                    <li class="hover:bg-secondary-light hover:text-white rounded py-1.5 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.section.borrowing-records') }}" class="sidebar-link block">Borrowing Records</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-1.5 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.section.reservation-records') }}" class="sidebar-link block">Reservation Records</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-1.5 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.section.penalty-records') }}" class="sidebar-link block">Penalty Records</a>
                    </li>
                </ul>
            </li>

            <!-- User Management -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/users.svg') }}" alt="User Management Icon" class="inline-block w-5 h-5 mr-3">
                    <span>User Management</span>
                </div>

                <ul class="mt-0.5 space-y-0">
                    <li class="hover:bg-secondary-light hover:text-white rounded py-1.5 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users') }}" class="sidebar-link block">Borrowers</a>
                    </li>
                    <li class="hover:bg-secondary-light hover:text-white rounded py-1.5 px-3 pl-11 transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users/staff') }}" class="sidebar-link block">Personnel Acounts</a>
                    </li>
                </ul>
            </li>

            {{-- Semester Management --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <img src="{{ asset('build/assets/icons/semester.svg') }}" alt="Semester Management Icon" class="inline-block w-5 h-5 mr-3">
                <a href="{{ asset('librarian/semester-management') }}" class="sidebar-link">Semester Management</a>
            </li>

            {{-- Activity Log --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <img src="{{ asset('build/assets/icons/activity-log.svg') }}" alt="Activity Log Icon" class="inline-block w-5 h-5 mr-3">
                <a href="{{ asset('librarian/activity-logs') }}" class="sidebar-link">Activity Log</a>
            </li>

            {{-- Settings --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <img src="{{ asset('build/assets/icons/settings.svg') }}" alt="Settings Icon" class="inline-block w-5 h-5 mr-3">
                <a href="{{ asset('librarian/settings') }}" class="sidebar-link">Settings</a>
            </li>
        </ul>
    </nav>
</aside>

@vite('resources/js/pages/librarian/sidebarNavigation.js')
