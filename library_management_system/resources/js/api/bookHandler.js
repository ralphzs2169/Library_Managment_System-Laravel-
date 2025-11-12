import { API_ROUTES, VALIDATION_ERROR } from "../config.js";
import { displayInputErrors, scrollToFirstError } from "../helpers.js";
import { showSuccessWithRedirect, showWarning, showConfirmation, showError, showInfo } from "../utils.js";
import { BOOKS_ROUTES, BOOK_CATALOG_SEARCH_COLUMN_INDEXES } from "../config.js";
import { highlightSearchMatches } from "../tableControls.js";

export async function addBookHandler(bookData, form) {
    // Step 1: Validate only
    bookData.append('validate_only', 1);
    let response = await fetch(API_ROUTES.ADD_BOOK, {
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
    response = await fetch(API_ROUTES.ADD_BOOK, {
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

export async function fetchAllBooks({ search = '' , sort = 'title_asc', page = 1 } = {}) {
    try {

         const params = new URLSearchParams({
            search,
            sort,
            page
        });
        
        const response = await fetch(`/staff/books/available?${params.toString()}`, {
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

    let response = await fetch(`${API_ROUTES.UPDATE_BOOK}/${bookDetails.get('book_id')}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: bookDetails
    });

    const result = await response.json();

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
    response = await fetch(`${API_ROUTES.UPDATE_BOOK}/${bookDetails.get('book_id')}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: bookDetails
    });


    if (!response.ok) {
        showWarning('Something went wrong', 'Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Book updated successfully!', window.location.href);
}

export async function fetchBookDetails(book) {
    try {
        const response = await fetch(`/librarian/books/${book.id}/edit`, {
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

export async function loadBooks(page = 1) {
    try {
        const searchInput = document.querySelector('#books-search');
        const sortSelect = document.querySelector('#books-sort');
        const categoryFilter = document.querySelector('#books-category-filter');
        const statusFilter = document.querySelector('#books-status-filter');

        const params = new URLSearchParams({
            page,
            search: searchInput?.value || '',
            sort: sortSelect?.value || 'newest',
            category: categoryFilter?.value || '',
            status: statusFilter?.value || 'all'
        });

        const response = await fetch(`${BOOKS_ROUTES.INDEX}?${params.toString()}`, {
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
            highlightSearchMatches(searchTerm, '#books-table-container', BOOK_CATALOG_SEARCH_COLUMN_INDEXES);
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        showError('Something went wrong', error.message || 'Failed to load books.');
    }
}