import { VALIDATION_ERROR, BOOK_ROUTES } from "../config.js";
import { displayInputErrors } from "../helpers.js";
import { showSuccessWithRedirect, showWarning, showConfirmation, showError, showInfo, showToast } from "../utils.js";
import { highlightSearchMatches } from "../tableControls.js";
import { BOOK_FILTERS } from "../utils/tableFilters.js";
import { SEARCH_COLUMN_INDEXES } from "../utils/tableFilters.js";

export async function addBookHandler(bookData, form) {
    // Step 1: Validate only
    bookData.append('validate_only', 1);
    let response = await fetch(BOOK_ROUTES.STORE, {
        method: 'POST',
        // Indicate we expect JSON so the server returns JSON errors instead of HTML redirects
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: bookData
    });
    
    const result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) { 
            displayInputErrors(result.errors, form); 
            return;
        }
        showWarning('Something went wrong', 'Please try again.');
        return;
    }


    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Add Book?',
        `Are you sure you want to add this book?`,
        'Yes, add it!'
    );
    if (!isConfirmed) return;
    bookData.delete('validate_only');
    // Step 3: Add Book
    response = await fetch(BOOK_ROUTES.STORE, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: bookData
    });

    if (!response.ok) {
        showWarning('Something went wrong', 'Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Book added successfully!', window.location.href);
}

//
export async function fetchAllAvailableBooks({ search = '' , sort = 'title_asc', page = 1 } = {}) {
    try {

         const params = new URLSearchParams({
            search,
            sort,
            page
        });
        
        const response = await fetch(BOOK_ROUTES.AVAILABLE_BOOKS(params.toString()), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const result = await response.json();

        if (!response.ok) {
            showError('Something went wrong', 'Failed to load books.');
            return;
        }

         return { data: result.data, meta: result.meta };
    } catch (error) {
        showError('Network Error', 'Unable to load books. Please try again later.');
    }
}

export async function editBookHandler(bookDetails, form) {
    // Make sure validate_only is set after all other fields
    bookDetails.set('validate_only', 1);
    const bookId = bookDetails.get('book_id');
    
    // Add CSRF token and method spoofing for PUT request
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    bookDetails.append('_token', csrfToken);
    bookDetails.append('_method', 'PUT');

    let response = await fetch(BOOK_ROUTES.UPDATE(bookId), {
        method: 'POST', // Use POST but spoof to PUT with _method
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: bookDetails
    });

    let result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) {
            // Check for invalid status (business rule violation)
            if (result.status === 'invalid') {
                showWarning('Invalid Status Change', result.message);
                return;
            }
            
            displayInputErrors(result.errors, form);
            return;
        }
        showWarning('Something went wrong', 'Please try again.');
        return;
    }
    
    if (result.status === 'unchanged') {
        showInfo('No changes detected', 'You didn’t make any modifications to update.');
        return;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Save Changes?',
        'Are you sure you want to save changes to this book?',
        'Yes, save'
    );
    
    if (!isConfirmed) return;
    
    bookDetails.delete('validate_only');

    // Step 3: Submit update
    response = await fetch(BOOK_ROUTES.UPDATE(bookId), {
        method: 'POST', // Use POST but spoof to PUT with _method
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: bookDetails
    });
    result = await response.json();

    if (!response.ok) {
        showWarning('Something went wrong', 'Failed to update book. Please try again later.');
        return;
    }

    if (result.status === 'invalid' || result.status === 'error') {
        showWarning('Something went wrong', result.message || 'Failed to update book. Please try again later.');
        return;
    }
    
    loadBooks(undefined, false);
    showToast('Book updated successfully!', 'success');
}

export async function fetchBookDetails(book) {
    try {
        const response = await fetch(BOOK_ROUTES.EDIT(book.id), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();

        if (!response.ok) {
            showError('Something went wrong', 'Failed to load book details.');
            return;
        }

        return data.book;
    } catch (error) {
        showError('Network Error', 'Unable to load book details. Please try again later.');
    }
}

export async function loadBooks(page = BOOK_FILTERS.page, scrollUp = true) {
    try {
        BOOK_FILTERS.page = page; // Update current page in filters

        const searchInput = document.querySelector('#books-search');
        const sortSelect = document.querySelector('#books-sort');
        const categoryFilter = document.querySelector('#books-category-filter');
        const statusFilter = document.querySelector('#books-status-filter');

        if (searchInput) BOOK_FILTERS.search = searchInput?.value || '';
        if (sortSelect) BOOK_FILTERS.sort = sortSelect?.value || BOOK_FILTERS.sort;
        if (categoryFilter) BOOK_FILTERS.category = categoryFilter?.value || BOOK_FILTERS.category;
        if (statusFilter) BOOK_FILTERS.status = statusFilter?.value || BOOK_FILTERS.status;
        
        const params = new URLSearchParams(BOOK_FILTERS);
        
        const response = await fetch(`${BOOK_ROUTES.INDEX}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const html = await response.text();

        if (!response.ok) {
            showError('Something went wrong', 'Failed to load books.');
            return;
        }

        const container = document.getElementById('books-table-container');
        if (container) {
            container.innerHTML = html;
        }

        // Highlight search matches
        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#books-table-container', SEARCH_COLUMN_INDEXES.BOOK_CATALOG);
        }

        if( scrollUp ) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    } catch (error) {
        showError('Something went wrong', error.message || 'Failed to load books.');
    }
}