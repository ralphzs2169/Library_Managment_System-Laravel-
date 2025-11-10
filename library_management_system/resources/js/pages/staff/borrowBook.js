import { fetchAllBooks } from '../../api/bookHandler.js';
import { closeBorrowerModal, initializeBorrowerProfileModal } from './borrowers.js';
import { openConfirmBorrowModal } from './confirmBorrow.js';
import { debounce, highlightSearchMatches, formatLastNameFirst } from '../../utils.js';

// Store current state for navigation
let currentState = {
    search: '',
    sort: 'title_asc',
    page: 1
};

export function showBorrowBookContent(modal, borrower, restoreState = false) {
    const modalContent = modal.querySelector('#borrower-profile-content');
    
    // Store original content for back navigation
    if (!modal.dataset.originalContent) {
        modal.dataset.originalContent = modalContent.innerHTML;
    }

    // Reset state if not restoring
    if (!restoreState) {
        currentState = {
            search: '',
            sort: 'title_asc',
            page: 1
        };
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
                <!-- Search Bar -->
                <div class="relative flex-1">
                    <img src="${window.location.origin}/build/assets/icons/search.svg" alt="Search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                    <input id="searchInput" type="text" placeholder="Search books by title, author, or ISBN…" value="${currentState.search}" class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition" />
                </div>

                <!-- Sort Filter -->
                <div class="relative">
                    <select id="sortSelect" class="px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition bg-white appearance-none pr-10 min-w-[160px]">
                        <option value="title_asc" ${currentState.sort === 'title_asc' ? 'selected' : ''}>Title (A-Z)</option>
                        <option value="title_desc" ${currentState.sort === 'title_desc' ? 'selected' : ''}>Title (Z-A)</option>
                        <option value="year_desc" ${currentState.sort === 'year_desc' ? 'selected' : ''}>Year (Newest)</option>
                        <option value="year_asc" ${currentState.sort === 'year_asc' ? 'selected' : ''}>Year (Oldest)</option>
                        <option value="author_asc" ${currentState.sort === 'author_asc' ? 'selected' : ''}>Author (A-Z)</option>
                        <option value="author_desc" ${currentState.sort === 'author_desc' ? 'selected' : ''}>Author (Z-A)</option>
                    </select>
                    <img src="${window.location.origin}/build/assets/icons/dropdown-black.svg" alt="Sort" class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none">
                </div>
            </div>
        </div>

        <!-- Books Section -->
        <div class="px-6 py-6 overflow-y-auto flex-1" style="min-height: 500px; max-height: calc(90vh - 200px);">
            <div class="flex items-center gap-2 mb-4">
                <img src="${window.location.origin}/build/assets/icons/available-books.svg" alt="Books" class="w-7 h-7">
                <h3 class="text-gray-800 font-semibold text-md">Available Books</h3>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200" style="min-height: 400px;">
                <table class="min-w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">No.</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Cover</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Title & ISBN</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Author</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Year</th>
                            <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-xs">Copies</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider text-xs">Action</th>
                        </tr>
                    </thead>
                    <tbody id="books-table-body" class="bg-white divide-y divide-gray-100">
                        <tr>
                            <td colspan="7" class="py-20 text-center">
                                <p class="text-gray-500 text-sm">Loading available books...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center mt-4 pagination"></div>
        </div>
    `;
    
    const searchInput = modal.querySelector('#searchInput');
    const sortSelect = modal.querySelector('#sortSelect');

    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            currentState.search = e.target.value.trim();
            currentState.page = 1;
            loadAvailableBooks(borrower, currentState);
        }, 400));
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', (e) => {
            currentState.sort = e.target.value;
            currentState.page = 1;
            loadAvailableBooks(borrower, currentState);
        });
    }

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
    
    loadAvailableBooks(borrower, currentState);
}

async function loadAvailableBooks(borrower, { search = '', sort = 'title_asc', page = 1 } = {}) {
    const tbody = document.getElementById('books-table-body');
    const paginationContainer = document.querySelector('.pagination');
    if (!tbody || !paginationContainer) return;

    currentState = { search, sort, page };

    tbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-gray-500">Loading...</td></tr>`;
    paginationContainer.innerHTML = '';

    try {
        const { data: books, meta } = await fetchAllBooks({ search, sort, page });

        if (!books || books.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-gray-500">No books found</td></tr>`;
            return;
        }

        const startIndex = meta.from || (meta.per_page * (meta.current_page - 1)) + 1;

        tbody.innerHTML = books.map((book, index) => `
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-700">${startIndex + index}</td>
                <td class="px-4 py-3">
                    <img src="${book.cover_image ? '/storage/' + book.cover_image : '/images/no-cover.png'}" alt="${book.title || 'Book'}" class="w-12 h-16 rounded-md object-cover shadow-sm">
                </td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800">${book.title || 'Untitled'}</p>
                    <p class="text-xs text-gray-500 font-mono mt-1">ISBN: ${book.isbn || 'N/A'}</p>
                </td>
                <td class="px-4 py-3 text-gray-700">${formatLastNameFirst(book.author?.firstname, book.author?.lastname, book.author?.middle_initial)}</td>
                <td class="px-4 py-3 text-gray-700">${book.publication_year || 'N/A'}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 text-green-600 font-medium text-xs">
                        ${book.copies_available || 0} Available
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <button class="borrow-book-btn cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 bg-accent hover:bg-accent/80 text-white rounded-lg text-xs font-medium transition-all shadow-sm hover:shadow" data-book="${encodeURIComponent(JSON.stringify(book))}">
                        <img src="${window.location.origin}/build/assets/icons/add-white.svg" alt="Borrow" class="w-4 h-4">
                        Borrow
                    </button>
                </td>
            </tr>
        `).join('');

        tbody.querySelectorAll('.borrow-book-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                try {
                    const book = JSON.parse(decodeURIComponent(this.dataset.book));
                    const borrowerModal = document.getElementById('borrower-profile-modal');
                    if (borrowerModal) {
                        borrowerModal.classList.add('hidden');
                    }
                    // Pass borrower and book to confirm modal
                    openConfirmBorrowModal(borrower, book);
                } catch (error) {
                    showError('Something went wrong', 'Failed to load book information.');
                }
            });
        });
        
        if (search) {
            highlightSearchMatches(search, '#books-table-body', [2, 3]);
        }

        renderPagination(meta, borrower, { search, sort });
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-gray-500">Failed to load books</td></tr>`;
    }
}

function renderPagination(meta, borrower, params) {
    const paginationContainer = document.querySelector('.pagination');
    if (!paginationContainer) return;

    paginationContainer.innerHTML = `
        <button ${meta.current_page === 1 ? 'disabled' : ''} data-page="${meta.current_page - 1}"
            class="text-sm text-teal-600 font-medium hover:underline ${meta.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
            Prev
        </button>
        <span class="text-sm text-gray-700">Page ${meta.current_page} of ${meta.last_page}</span>
        <button ${meta.current_page === meta.last_page ? 'disabled' : ''} data-page="${meta.current_page + 1}"
            class="text-sm text-teal-600 font-medium hover:underline ${meta.current_page === meta.last_page ? 'opacity-50 cursor-not-allowed' : ''}">
            Next
        </button>
    `;

    paginationContainer.querySelectorAll('button[data-page]').forEach(btn => {
        btn.addEventListener('click', () => {
            const page = parseInt(btn.dataset.page);
            currentState.page = page;
            loadAvailableBooks(borrower, { ...params, page });
        });
    });
}

export function restoreProfileContent(modal, borrower) {
    const modalContent = modal.querySelector('#borrower-profile-content');
    
    if (modal.dataset.originalContent) {
        modalContent.innerHTML = modal.dataset.originalContent;
        initializeBorrowerProfileModal(modal, borrower);
        
        const closeBtn = modal.querySelector('#close-borrower-modal');
        if (closeBtn) {
            closeBtn.onclick = (e) => {
                e.preventDefault();
                closeBorrowerModal();
            };
        }
    }
}

