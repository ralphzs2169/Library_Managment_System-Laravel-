import { API_ROUTES, VALIDATION_ERROR } from "../config.js";
import { displayInputErrors, scrollToFirstError } from "../helpers.js";
import { showSuccessWithRedirect, showWarning, showConfirmation, showError, showInfo } from "../utils.js";

export async function addBookHandler(bookDetails, form) {
    // Step 1: Validate only
    bookDetails.append('validate_only', 1);
    let response = await fetch(API_ROUTES.ADD_BOOK, {
        method: 'POST',
        // Indicate we expect JSON so the server returns JSON errors instead of HTML redirects
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: bookDetails
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
    bookDetails.delete('validate_only');
    // Step 3: Add Bo
    response = await fetch(API_ROUTES.ADD_BOOK, {
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

    showSuccessWithRedirect('Success', 'Book added successfully!', window.location.href);
}

export async function fetchAllBooks() {
    try {
        const response = await fetch(`/staff/books/available`, {
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

        return result.books.data;
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
