import { API_ROUTES, VALIDATION_ERROR } from "../config.js";
import { displayInputErrors } from "../helpers.js";
import { showSuccessWithRedirect, showWarning, showConfirmation, showError, showInfo } from "../utils.js";

export async function addBookHandler(bookDetails) {
    // Step 1: Validate only
    bookDetails.append('validate_only', 1);
    let response = await fetch(API_ROUTES.ADD_BOOK, {
        method: 'POST',
        body: bookDetails
    });
    
    const result = await response.json();

    if (!response.ok) {

        if (response.status === VALIDATION_ERROR) { 
            displayInputErrors(result.errors); 
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
        body: bookDetails
    });

    if (!response.ok) {
        showWarning('Something went wrong', 'Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Book added successfully!', window.location.href);
}

export async function editBookHandler(formData) {
    // Make sure validate_only is set after all other fields
    formData.set('validate_only', 1);

    let response = await fetch(`${API_ROUTES.UPDATE_BOOK}/${formData.get('book_id')}`, {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    console.log(result)
    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) {
            displayInputErrors(result.errors);
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
    formData.delete('validate_only');

    // Step 3: Submit update
   
    response = await fetch(`${API_ROUTES.UPDATE_BOOK}/${formData.get('book_id')}`, {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        showWarning('Something went wrong', 'Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Book updated successfully!', window.location.href);
}

