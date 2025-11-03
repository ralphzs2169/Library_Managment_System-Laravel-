import { fetchAllBooks } from '../../api/bookHandler.js';
import { closeBorrowerModal, initializeBorrowerProfileModal } from './borrowers.js';
import { openConfirmBorrowModal } from './confirmBorrow.js';

export function showBorrowBookContent(modal, borrower) {
    const modalContent = modal.querySelector('#borrower-profile-content');
    
    // Store original content for back navigation
    if (!modal.dataset.originalContent) {
        modal.dataset.originalContent = modalContent.innerHTML;
    }
    
    // Replace content with borrow book interface
    modalContent.innerHTML = `
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="back-to-profile-button" class="cursor-pointer text-gray-600 hover:text-gray-800 transition">
                    <img src="${window.location.origin}/build/assets/icons/back-gray.svg" alt="Back" class="w-6 h-6">
                </button>
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <img src="${window.location.origin}/build/assets/icons/borrow-book-accent.svg" alt="Borrow Book Icon" class="w-8 h-8">
                    Borrow Book for ${borrower.full_name}
                </h2>
            </div>
            <button id="close-borrower-modal" class="cursor-pointer text-gray-500 hover:text-gray-700 hover:scale-110 transition">
                <img src="${window.location.origin}/build/assets/icons/close-gray.svg" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Search and Filter Section -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex gap-3">
                <!-- Search Bar with Filter -->
                <div class="relative flex-1">
                    <img src="${window.location.origin}/build/assets/icons/search.svg" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                    <input id="searchInput" type="text" placeholder="Search books by title, author, or ISBN…" class="w-full pl-12 pr-24 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition" />
                    
                    <div class="absolute right-2 top-1/2 -translate-y-1/2">
                        <button id="searchFilterButton" class="flex items-center gap-1 bg-accent hover:bg-accent/60 text-white text-sm font-medium px-3 py-1.5 rounded-lg transition">
                            <span id="searchSelectedFilter">Title</span>
                            <img src="${window.location.origin}/build/assets/icons/dropdown-white.svg" alt="Filter" class="w-4 h-4">
                        </button>
                        
                        <div id="searchFilterMenu" class="hidden absolute right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg w-32 z-[9999]">
                            <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg search-filter-option" data-filter="Title">Title</button>
                            <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 search-filter-option" data-filter="Author">Author</button>
                            <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg search-filter-option" data-filter="ISBN">ISBN</button>
                        </div>
                    </div>
                </div>

                <!-- Sort Filter -->
                <div class="relative">
                    <select id="sortSelect" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition bg-white appearance-none pr-10 min-w-[160px]">
                        <option value="title_asc">Title (A-Z)</option>
                        <option value="title_desc">Title (Z-A)</option>
                        <option value="year_desc">Year (Newest)</option>
                        <option value="year_asc">Year (Oldest)</option>
                        <option value="author_asc">Author (A-Z)</option>
                        <option value="author_desc">Author (Z-A)</option>
                    </select>
                    <img src="${window.location.origin}/build/assets/icons/dropdown-black.svg" alt="Sort" class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none">
                </div>
            </div>
        </div>

        <!-- Books Section with Fixed Height -->
        <div class="px-6 py-6 overflow-y-auto flex-1" style="min-height: 500px; max-height: calc(90vh - 200px);">
            <div class="flex items-center gap-2 mb-4">
                <img src="${window.location.origin}/build/assets/icons/available-books.svg" alt="Books" class="w-7 h-7">
                <h3 class="text-gray-800 font-semibold text-md">Available Books</h3>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200" style="min-height: 400px;">
                <table class="min-w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Cover</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Title & ISBN</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Author</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Year</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Copies</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider text-xs">Action</th>
                        </tr>
                    </thead>
                    <tbody id="books-table-body" class="bg-white divide-y divide-gray-100">
                        <!-- Empty state with fixed height -->
                        <tr class="empty-state">
                            <td colspan="6" class="py-20 text-center">
                                
                                <p class="text-gray-500 text-sm">Loading available books...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <button class="text-sm text-teal-600 font-medium hover:underline disabled:opacity-50 disabled:cursor-not-allowed" disabled>Prev</button>
                <div class="flex items-center gap-1">
                    <button class="px-3 py-1.5 rounded-lg bg-teal-500 text-white text-sm font-medium shadow-sm">1</button>
                    <button class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">2</button>
                    <button class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">3</button>
                </div>
                <button class="text-sm text-teal-600 font-medium hover:underline">Next</button>
            </div>
        </div>
    `;
    
    // Re-attach event listeners for the new content
    const backButton = modal.querySelector('#back-to-profile-button');
    if (backButton) {
        backButton.onclick = () => restoreProfileContent(modal, borrower);
    }
    
    const closeBtn = modal.querySelector('#close-borrower-modal');
    if (closeBtn) {
        closeBtn.onclick = (e) => {
            e.preventDefault();
            closeBorrowerModal();
        };
    }
    
    // Re-initialize search filter dropdown for borrow book interface
    initializeSearchFilter();
    
    // Load available books (this will replace the empty state)
    loadAvailableBooks(borrower);
}

// Load available books into the table
async function loadAvailableBooks(borrower) {
    const books = await fetchAllBooks();

    const tbody = document.getElementById('books-table-body');
    if (!tbody) return;

    // Populate with actual books
    tbody.innerHTML = books.map((book) => `
        <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-3">
                    <img src="${book.cover_image ? '/storage/' + book.cover_image : '/images/no-cover.png'}" alt="${book.title || 'Book'}" class="w-12 h-16 rounded-md object-cover shadow-sm">
                </td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">${book.title || 'Untitled'}</p>
                    <p class="text-xs text-gray-500 font-mono mt-1">ISBN: ${book.isbn || 'N/A'}</p>
                </td>
                <td class="px-4 py-3 text-gray-700">${(book.author?.firstname || '') + ' ' + (book.author?.lastname || '')}</td>
                <td class="px-4 py-3 text-gray-700">${book.publication_year || 'N/A'}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 text-green-600 font-medium text-xs">
                        
                        ${book.copies_available || 0} Available
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <button class="borrow-book-btn  cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 bg-accent hover:bg-accent/80 text-white rounded-lg text-xs font-medium transition-all shadow-sm hover:shadow" data-book="${encodeURIComponent(JSON.stringify(book))}">
                        <img src="${window.location.origin}/build/assets/icons/add-white.svg" alt="Borrow" class="w-4 h-4">
                        Borrow
                    </button>
                </td>
            </tr>
        `).join('');

        // Attach borrow handlers
        tbody.querySelectorAll('.borrow-book-btn').forEach(btn => {
            btn.addEventListener('click', function() {
               const book = JSON.parse(decodeURIComponent(this.dataset.book));

                closeBorrowerModal();
                openConfirmBorrowModal(borrower, book);
            });
        });
        
}



export function restoreProfileContent(modal, borrower) {
    const modalContent = modal.querySelector('#borrower-profile-content');
    
    // Restore original content
    if (modal.dataset.originalContent) {
        modalContent.innerHTML = modal.dataset.originalContent;
        
        // Re-initialize the profile with borrower data
        initializeBorrowerProfileModal(modal, borrower);
        
        // Re-attach close button
        const closeBtn = modal.querySelector('#close-borrower-modal');
        if (closeBtn) {
            closeBtn.onclick = (e) => {
                e.preventDefault();
                closeBorrowerModal();
            };
        }
    }
}

function initializeSearchFilter() {
    const dropdownButton = document.getElementById('searchFilterButton');
    const dropdownMenu = document.getElementById('searchFilterMenu');
    const filterOptions = document.querySelectorAll('.search-filter-option');
    const selectedFilter = document.getElementById('searchSelectedFilter');
    const searchInput = document.getElementById('searchInput');

    if (!dropdownButton || !dropdownMenu || !selectedFilter || !searchInput) return;

    dropdownMenu.style.zIndex = 9999;

    dropdownButton.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownMenu.classList.toggle('hidden');
    });

    filterOptions.forEach(option => {
        option.addEventListener('click', (ev) => {
            ev.stopPropagation();
            const filter = option.dataset.filter;
            selectedFilter.textContent = filter;
            dropdownMenu.classList.add('hidden');
            searchInput.placeholder = `Search books by ${filter.toLowerCase()}…`;
            searchInput.focus();
        });
    });

    // Close dropdown when clicking outside (use capturing phase to prevent conflicts)
    const closeDropdown = (e) => {
        if (dropdownMenu && !dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.add('hidden');
        }
    };
    document.addEventListener('click', closeDropdown);

    // Close on Escape
    const handleEscape = (e) => {
        if (e.key === 'Escape' && dropdownMenu) {
            dropdownMenu.classList.add('hidden');
        }
    };
    document.addEventListener('keydown', handleEscape);
}


