<aside class="bg-secondary text-white w-72 min-h-screen p-6 hidden md:block fixed text-sm">
    <nav aria-label="Sidebar navigation">
        <ul class="space-y-2">
            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-accent p-2">
                <img src="{{ asset('build/assets/icons/dashboard.svg') }}" alt="Dashboard Icon" class="inline-block w-7 h-7 mr-5">
                <a href="{{ asset('dashboard') }}" class="sidebar-link">Dashboard</a>
            </li>

            <!-- Book Management -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-accent p-2 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/book-management.svg') }}" alt="Book Management Icon" class="inline-block w-7 h-7 mr-5">
                    <span>Book Management</span>
                    <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" alt="Expand Icon" class="inline-block w-6 h-6 ml-auto dropdown-icon transition-transform duration-300">
                </div>
                
                <ul class="mt-2 space-y-1 collapsible-content overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0">
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=books') }}" class="sidebar-link block">All Books</a>
                    </li>
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=books/create') }}" class="sidebar-link block">Add New Book</a>
                    </li>
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ route('librarian.categories-genres') }}" class="sidebar-link block">Categories/Genres</a>
                    </li>
                </ul>
            </li>

            <!-- Borrowing -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-accent p-2 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/borrowing.svg') }}" alt="Borrowing Icon" class="inline-block w-7 h-7 mr-5">
                    <span>Borrowing</span>
                    <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" alt="Expand Icon" class="inline-block w-6 h-6 ml-auto dropdown-icon transition-transform duration-300">
                </div>
                
                <ul class="mt-2 space-y-1 collapsible-content overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0">
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=borrowings') }}" class="sidebar-link block">All Borrowings</a>
                    </li>
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=borrowings/create') }}" class="sidebar-link block">Reservations</a>
                    </li>
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=returns') }}" class="sidebar-link block">Overdues/Fines</a>
                    </li>
                </ul>
            </li>

            <!-- User Management -->
            <li>
                <div class="flex items-center rounded-md cursor-pointer transition-colors duration-100 hover:bg-accent p-2 collapsible-trigger">
                    <img src="{{ asset('build/assets/icons/users.svg') }}" alt="User Management Icon" class="inline-block w-7 h-7 mr-5">
                    <span>User Management</span>
                    <img src="{{ asset('build/assets/icons/dropdown-white.svg') }}" alt="Expand Icon" class="inline-block w-6 h-6 ml-auto dropdown-icon transition-transform duration-300">
                </div>
                
                <ul class="mt-2 space-y-1 collapsible-content overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0">
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users') }}" class="sidebar-link block">Students</a>
                    </li>
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users/teachers') }}" class="sidebar-link block">Teachers</a>
                    </li>
                    <li class="hover:bg-accent hover:text-white rounded p-2 pl-[3.75rem] transition-colors duration-100 text-[#8593A6]">
                        <a href="{{ asset('index.php?route=users/staff') }}" class="sidebar-link block">Staff</a>
                    </li>
                </ul>
            </li>

            <li class="rounded-md cursor-pointer transition-colors duration-100 hover:bg-accent p-2">
                <img src="{{ asset('build/assets/icons/activity-log.svg') }}" alt="Activity Log Icon" class="inline-block w-7 h-7 mr-5">
                <a href="{{ asset('index.php?route=activity-logs') }}" class="sidebar-link">Activity Log</a>
            </li>
        </ul>
    </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggers = document.querySelectorAll('.collapsible-trigger');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.dropdown-icon');
            const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';
            
            // Close all other sections
            triggers.forEach(otherTrigger => {
                if (otherTrigger !== this) {
                    const otherContent = otherTrigger.nextElementSibling;
                    const otherIcon = otherTrigger.querySelector('.dropdown-icon');
                    
                    otherContent.style.maxHeight = '0px';
                    otherContent.classList.remove('opacity-100');
                    otherContent.classList.add('opacity-0');
                    otherIcon.classList.remove('rotate-180');
                }
            });
            
            // Toggle current section
            if (isOpen) {
                content.style.maxHeight = '0px';
                content.classList.remove('opacity-100');
                content.classList.add('opacity-0');
                icon.classList.remove('rotate-180');
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.classList.remove('opacity-0');
                content.classList.add('opacity-100');
                icon.classList.add('rotate-180');
            }
        });
    });
});
</script>
