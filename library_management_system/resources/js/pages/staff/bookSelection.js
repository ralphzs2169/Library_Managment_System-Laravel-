import { fetchBooksSelectionContent } from '../../ajax/bookHandler.js';
import { closeBorrowerModal } from './borrower/borrowerProfileModal.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js';
import { openConfirmBorrowModal } from './confirmBorrow.js';
import { debounce, formatLastNameFirst } from '../../utils.js';
import { highlightSearchMatches } from '../../tableControls.js';
import { showError } from '../../utils/alerts.js';
import { openConfirmReservationModal } from './transactions/confirmReservation.js';

// Store current state for navigation
let currentState = {
    search: '',
    sort: 'title_asc',
    page: 1
};

export function showBookSelectionContent(modal, member, transactionType, restoreState = false) {
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
    
    // Mark modal view state as "borrow"
    modal.dataset.view = transactionType;

    // Replace content with borrow book interface
    const modalIcon = transactionType === 'borrow' ? 'borrow-book-accent.svg' : 'reservation-accent.svg';
    const modalTitle = (transactionType === 'borrow' ? 'Borrow' : 'Reserve') + ` Book Selection`;
    modalContent.innerHTML = `
        <!-- Header -->
        <div class="bg-modal-header drop-shadow-md px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button id="back-to-profile-button" class="cursor-pointer  hover:scale-110 transition">
                    <img src="${window.location.origin}/build/assets/icons/back-gray.svg" alt="Back" class="w-6 h-6">
                </button>
                <h2 class="text-xl font-semibold text-black flex items-center gap-2">
                    ${modalTitle}
                </h2>
            </div>
            <button id="close-member-modal" class="cursor-pointer  hover:scale-110 transition">
                <img src="${window.location.origin}/build/assets/icons/close-gray.svg" alt="Close" class="w-6 h-6">
            </button>
        </div>

        <!-- Search and Filter Section -->
        <div class="px-6 py-4 bg-white border-b border-gray-200">
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
             loadBookSelectionContent(member, currentState, transactionType);
        }, 400));
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', (e) => {
            currentState.sort = e.target.value;
            currentState.page = 1;
             loadBookSelectionContent(member, currentState, transactionType);
        });
    }

    const backButton = modal.querySelector('#back-to-profile-button');
    if (backButton) {
        backButton.onclick = () => restoreProfileContent(modal, member);
    }
    
    const closeBtn = modal.querySelector('#close-member-modal');
    if (closeBtn) {
        closeBtn.onclick = (e) => {
            e.preventDefault();
            closeBorrowerModal();
        };
    }
    
    loadBookSelectionContent(member, currentState, transactionType);
}

function renderCopiesBadge(eligibleCopies, transactionType) {
    let badgeHTML = '';
    switch (transactionType) {
        case 'borrow':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    ${eligibleCopies} Available
                </span>
            `;
            break;
        case 'reservation':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-200 text-yellow-700 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    ${eligibleCopies} Reservable
                </span>
            `;
            break;
        default:
            badgeHTML = `
                <span class="inline-flex items-center gap-1 text-green-600 font-medium text-xs">
            `;
    }
    return badgeHTML;
}

async function loadBookSelectionContent(member, { search = '', sort = 'title_asc', page = 1 } = {}, transactionType) {
    const tbody = document.getElementById('books-table-body');
    const paginationContainer = document.querySelector('.pagination');
    if (!tbody || !paginationContainer) return;

    currentState = { search, sort, page };

    tbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-gray-500">Loading...</td></tr>`;
    paginationContainer.innerHTML = '';

    const buttonLabel = transactionType === 'borrow' ? 'Borrow' : 'Reserve';
    const buttonIcon = transactionType === 'borrow' ? 'plus-white' : 'reservation-white';

    try {

       // --- Client-side JavaScript Update ---

const { data, meta } = await fetchBooksSelectionContent({ search, sort, page }, transactionType, member.id);

// 1. Convert the 'data' object into an array of book objects
const books = Object.values(data); 

// 2. The rest of the logic can remain mostly the same (since the ineligible book check was already removed)

if (!books || books.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-gray-500">No books found</td></tr>`;
    return;
}

// meta.from handles the case where total number of filtered books is less than per_page
const startIndex = meta.from || (meta.per_page * (meta.current_page - 1)) + 1;

tbody.innerHTML = books
.map((book, index) => {
    // ... (Your mapping logic goes here, no need for the eligible_copies check)
    return `
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
                ${renderCopiesBadge(book.eligible_copies, transactionType)}
            </td>
            <td class="px-4 py-3 text-center">
                <button class="borrow-book-btn cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 bg-secondary hover:bg-secondary/90 text-white rounded-lg text-xs font-medium transition-all shadow-sm hover:shadow" 
                        data-book="${encodeURIComponent(JSON.stringify(book))}">
                    <img src="${window.location.origin}/build/assets/icons/${buttonIcon}.svg" alt="${buttonLabel}" class="w-4 h-4">
                    <span class="font-medium tracking-wider">${buttonLabel}</span>
                </button>
            </td>
        </tr>
    `;
})
.join('');

        tbody.querySelectorAll('.borrow-book-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                try {
                    const book = JSON.parse(decodeURIComponent(this.dataset.book));
                    const borrowerModal = document.getElementById('borrower-profile-modal');
                
                    if (borrowerModal) {
                        borrowerModal.classList.add('hidden');
                    }
                    
                    if (transactionType === 'borrow'){
                        openConfirmBorrowModal(member, book);
                    } else if (transactionType === 'reservation') {
                        openConfirmReservationModal(member, book);
                    }

                    closeBorrowerModal(false, false);
                } catch (error) {
                    showError('Something went wrong', error + 'Failed to load book information.');
                }
            });
        });
        
        if (search) {
            highlightSearchMatches(search, '#books-table-body', [2, 3]);
        }

        renderPagination(meta, member, { search, sort }, transactionType);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-gray-500">Failed to load books</td></tr>`;
    }
}

function renderPagination(meta, borrower, params, transactionType) {
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
            loadBookSelectionContent(borrower, { ...params, page }, transactionType);
        });
    });
}

// Guard to prevent multiple concurrent restores (debounce)
let isRestoringProfile = false;
let restoreTimer = null;

export function restoreProfileContent(modal, borrower, reloadProfileTabs = true) {
    if (modal?.dataset?.view === 'profile') {
        return;
    }

    console.log('Proceeding with restore...');
    console.log('Modal dataset view:', modal?.dataset?.view);
    console.log('Borrower data:', borrower);

    // Global single-flight guard across modules
    if (window.__restoreProfileInProgress) return;
    window.__restoreProfileInProgress = true;

    // Local guard to coalesce burst calls within this module
    if (isRestoringProfile) return;
    isRestoringProfile = true;
    clearTimeout(restoreTimer);
    
    const modalContent = modal.querySelector('#borrower-profile-content');

    if (modal.dataset.originalContent) {
        modalContent.innerHTML = modal.dataset.originalContent;
        // Re-bind borrower profile UI only once per restore
        initializeBorrowerProfileUI(modal, borrower, reloadProfileTabs);

        const closeBtn = modal.querySelector('#close-borrower-modal');
        if (closeBtn) {
            // Use assignment to avoid stacking listeners
            closeBtn.onclick = (e) => {
                e.preventDefault();
                closeBorrowerModal();
            };
        }
    }

    // Mark modal view state as "profile"
    modal.dataset.view = 'profile';

    // Release guards after a short tick
    restoreTimer = setTimeout(() => {
        isRestoringProfile = false;
        window.__restoreProfileInProgress = false;
    }, 200);
}


