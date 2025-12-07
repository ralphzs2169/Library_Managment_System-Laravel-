{{-- filepath: c:\Users\Angela\library_management_system\resources\views\components\sidebar.blade.php --}}
<aside class="bg-secondary text-white w-66 min-h-screen p-3 hidden md:block fixed text-sm overflow-y-auto">
    <nav aria-label="Sidebar navigation">
        <ul class="space-y-1">
            {{-- Dashboard --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.dashboard') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/dashboard.svg') }}" alt="Dashboard Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Divider after Dashboard --}}
            <li class="my-2">
                <hr class="border-t border-[#8593A6]/30">
            </li>

            {{-- Book Catalog --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.books.index') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/book-management.svg') }}" alt="Book Catalog Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Book Inventory</span>
                </a>
            </li>

            {{-- Add New Book --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.books.create') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/add-book-white.svg') }}" alt="Add New Book Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Add New Book</span>
                </a>
            </li>

            {{-- Categories/Genres --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.category-management') }}" class="sidebar-link flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span>Categories/Genres</span>
                </a>
            </li>

            {{-- Divider after Book Management Section --}}
            <li class="my-2">
                <hr class="border-t border-[#8593A6]/30">
            </li>

            {{-- Borrowing Records --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.borrowing-records') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/borrowing.svg') }}" alt="Borrowing Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Borrowing Management</span>
                </a>
            </li>

            {{-- Reservation Records --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.reservation-records') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/reservation-white.svg') }}" alt="Reservation Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Reservation Management</span>
                </a>
            </li>

            {{-- Penalty Records --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.penalty-records') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/unpaid-white.svg') }}" alt="Penalty Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Penalty Management</span>
                </a>
            </li>

            {{-- Divider after Circulation Records Section --}}
            <li class="my-2">
                <hr class="border-t border-[#8593A6]/30">
            </li>

            {{-- Borrowers --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.borrowers') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/users.svg') }}" alt="Borrowers Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Borrowers</span>
                </a>
            </li>

            {{-- Personnel Accounts --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.personnel-accounts') }}" class="sidebar-link flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Personnel Accounts</span>
                </a>
            </li>

            {{-- Divider after User Management Section --}}
            <li class="my-2">
                <hr class="border-t border-[#8593A6]/30">
            </li>

            {{-- Clearance Management --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.clearance-management') }}" class="sidebar-link flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Clearance Management</span>
                </a>
            </li>

            {{-- Semester Management --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.semester-management') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/semester.svg') }}" alt="Semester Management Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Semester Management</span>
                </a>
            </li>

            {{-- Divider before System Section --}}
            <li class="my-2">
                <hr class="border-t border-[#8593A6]/30">
            </li>

            {{-- Activity Log --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.activity-logs') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/activity-log.svg') }}" alt="Activity Log Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Activity Log</span>
                </a>
            </li>

            {{-- Settings --}}
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-secondary-light py-2 px-3">
                <a href="{{ route('librarian.section.settings') }}" class="sidebar-link flex items-center">
                    <img src="{{ asset('build/assets/icons/settings.svg') }}" alt="Settings Icon" class="inline-block w-5 h-5 mr-3">
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

@vite('resources/js/pages/librarian/sidebarNavigation.js')
