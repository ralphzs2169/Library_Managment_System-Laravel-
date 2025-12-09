<!-- Page Loader -->
{{-- <div id="pageLoader" class="hidden">
    <div class="text-center">
        <div class="loader loader-lg"></div>
        <p class="text-white mt-4 text-sm font-medium">Loading...</p>
    </div>
</div> --}}

<header class="bg-primary text-white shadow-lg sticky top-0 z-50 drop-shadow-custom">
    <div class="container mx-auto px-4 md:px-8 lg:px-6">
        <div class="flex justify-between items-center h-16">

            <!-- Logo Section -->
            <a href="" class="flex items-center space-x-3 hover:opacity-80 transition-opacity duration-300 cursor-pointer">
                <img src="{{ asset('build/assets/images/logo.png') }}" width="42" height="42" alt="Logo" class="rounded-full">
                <div class="leading-tight space-y-0">
                    <h1 class="text-md font-bold leading-tight">Smart Library</h1>
                    <p class="text-[0.75em] text-gray-300">Management System</p>
                </div>
            </a>


            {{-- @can('view-header-links')
            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-12">
                <a href="/" class="nav-link text-sm font-medium tracking-wide uppercase cursor-pointer">
                    Home
                </a>
                <a href="about.php" class="nav-link text-sm font-medium tracking-wide uppercase cursor-pointer">
                    About
                </a>
                <a href="catalog.php" class="nav-link text-sm font-medium tracking-wide uppercase cursor-pointer">
                    Catalog
                </a>
            </nav>
            @endcan --}}

            @guest
            <!-- Authentication Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                @if (isset($title) && $title == 'Login')
                <a href="/signup" class="hover-scale-md px-6 py-1.75 bg-accent text-primary text-sm font-bold rounded-lg shadow-md cursor-pointer">
                    Sign Up
                </a>
                @elseif (isset($title) && $title == 'Signup')
                <a href="/login" class="hover-scale-md px-6 py-1.75 bg-accent text-primary text-sm font-bold rounded-lg shadow-md cursor-pointer">
                    Login
                </a>
                @else
                <a href="/signup" class="hover-scale-md mx-6 py-2 text-sm font-medium rounded-lg cursor-pointer hover:text-accent transition-colors duration-300">
                    Sign Up
                </a>
                <a href="/login" class="hover-scale-md px-6 py-1.75 bg-accent text-primary text-sm font-bold rounded-lg shadow-md cursor-pointer">
                    Login
                </a>
                @endif
            </div>
            @endguest


            <!-- Profile Menu for Logged-in Users -->
            @auth
            <div class="flex items-center space-x-2 relative max-w-[300px]">
                <img src="{{ asset('build/assets/icons/notification.svg') }}" alt="Notification Icon" class="w-10 h-10 rounded-full shrink-0 bg-secondary p-1.5">

                <img src="{{ asset('build/assets/icons/avatar.svg') }}" alt="User Icon" class="w-10 h-10 rounded-full shrink-0 bg-secondary p-1.5">

                <div class="hidden md:flex flex-col min-w-0">
                    <span class="font-semibold text-sm truncate block">
                        {{ Auth::user()->getFullnameAttribute() }}
                    </span>
                    <span class="text-xs text-gray-400 capitalize truncate block">
                        {{ Auth::user()->role }}
                    </span>
                </div>

                <button id="profileDropdown" class="flex items-center space-x-1 focus:outline-none cursor-pointer shrink-0">
                    <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" alt="Profile Dropdown" class="hover-scale-lg w-5 h-5">
                </button>
            </div>


            <!-- Profile Menu Dropdown for Logged in users  -->
            <div class="absolute top-12 right-0 p-3 w-64 bg-white text-black text-sm rounded-md shadow-lg z-10 hidden" id="dropdownMenu">
                <h2 class="font-semibold text-xs text-gray-600 mb-2">1st Semester 2025-2026</h2>
                <hr class="my-2 border-gray-200">

                <div class="flex flex-col space-y-1">
                    <a href="#" class="flex items-center space-x-3 px-2 py-1 rounded-md hover:bg-gray-100 transition-colors">
                        <img src="{{ asset('build/assets/icons/avatar-black.svg') }}" class="w-5 h-5">
                        <span>My Profile</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-2 py-1 rounded-md hover:bg-gray-100 transition-colors">
                        <img src="{{ asset('build/assets/icons/edit-profile.svg') }}" class="w-5 h-5">
                        <span>Edit Profile</span>
                    </a>
                </div>

                <hr class="my-2 border-gray-200">

                <div class="flex flex-col space-y-1">
                    <a href="#" class="flex items-center space-x-3 px-2 py-1 rounded-md hover:bg-gray-100 transition-colors">
                        <img src="{{ asset('build/assets/icons/my-borrowed-books.svg') }}" class="w-5 h-5">
                        <span>My Borrowed Books</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-2 py-1 rounded-md hover:bg-gray-100 transition-colors">
                        <img src="{{ asset('build/assets/icons/reservation.svg') }}" class="w-5 h-5">
                        <span>My Reservations</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-2 py-1 rounded-md hover:bg-gray-100 transition-colors">
                        <img src="{{ asset('build/assets/icons/history.svg') }}" class="w-5 h-5">
                        <span>Borrowing History</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-2 py-1 rounded-md hover:bg-gray-100 transition-colors">
                        <img src="{{ asset('build/assets/icons/payment.svg') }}" class="w-5 h-5">
                        <span>Fines & Payments</span>
                    </a>
                </div>

                <hr class="my-2 border-gray-200">

                <form id="logoutForm">
                    <button type="submit" id="logoutBtn" class="flex items-center space-x-3 px-2 py-2 rounded-md hover:bg-red-50 text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
            @endauth



            <!-- Mobile Navigation Button -->
            <div class="md:hidden flex items-center space-x-2">
                <button id="mobile-menu-btn" class="p-2 rounded-md hover:bg-gray-700 transition-all duration-300 cursor-pointer" aria-label="Toggle menu">
                    <svg id="menu-icon" class="w-6 h-6 transition-transform duration-200 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg id="close-icon" class="w-6 h-6 hidden transition-transform duration-200 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- Mobile Menu -->


        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div id="mobile-menu" class="hidden lg:hidden bg-primary border-t border-white border-opacity-10 absolute left-0 right-0 shadow-xl">
        <div class="container mx-auto px-4 py-6 space-y-4">

            <a href="/" class="block py-3 px-4 text-center rounded-lg transition-all duration-300 hover:bg-gray-700 hover:text-accent cursor-pointer">
                Home
            </a>
            <a href="about.php" class="block py-3 px-4 text-center rounded-lg transition-all duration-300 hover:bg-gray-700 hover:text-accent cursor-pointer">
                About
            </a>
            <a href="catalog.php" class="block py-3 px-4 text-center rounded-lg transition-all duration-300 hover:bg-gray-700 hover:text-accent cursor-pointer">
                Catalog
            </a>


            {{-- IF --}}
            {{-- <div class="pt-4 space-y-3 border-t border-white border-opacity-30 mt-6">
        <!-- Removed the simple text, profile menu is now in header -->
      </div> --}}
            {{-- ELSE --}}
            <div class="pt-4 space-y-3 border-t border-white border-opacity-30 mt-6">
                <a href="/signup" class="transition-all duration-300 hover:bg-gray-700 hover:text-accent w-full py-3.5 px-4 text-center rounded-lg font-semibold tracking-wide shadow-sm block">
                    Sign Up
                </a>
                <a href="/login" class="hover-scale-sm w-full py-3.5 px-4 bg-accent text-black rounded-lg font-bold tracking-wide shadow-md block text-center">
                    Login
                </a>
            </div>


        </div>
</header>

@vite('resources/js/header.js')
