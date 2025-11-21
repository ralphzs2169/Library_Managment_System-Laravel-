import { showError, showConfirmation, showSuccessWithRedirect, showWarning, showToast } from "../utils.js";
import {  VALIDATION_ERROR } from "../config.js";
import { displayInputErrors, scrollToFirstErrorInModal } from "../helpers.js";
import { BORROW_TRANSACTION_ROUTES } from "../config.js";
import { loadBorrowers } from "./usersHandler.js";
import { closeConfirmBorrowModal } from "../pages/staff/confirmBorrow.js";

export async function borrowBook(borrowData) {
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    borrowData.append('_token', csrfToken);

    // Step 1: Validate only
    borrowData.append('validate_only', 1);
    let response = await fetch(BORROW_TRANSACTION_ROUTES.BORROW, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: borrowData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) {
            if (result.status === 'invalid'){
                showWarning('Invalid Borrowing', result.message);
                closeConfirmBorrowModal();
            }
            displayInputErrors(result.errors, 'confirm-borrow-modal', false);
            
            // Scroll to first error within the modal
            scrollToFirstErrorInModal();
            return;
        }
        
        showError('Something went wrong', 'Please try again.');
        return;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Confirm Borrowing?',
        'Are you sure you want to lend this book?',
        'Yes, confirm'
    );
    
    if (!isConfirmed) return;

    borrowData.delete('validate_only');

    // Step 3: Submit borrow
    response = await fetch(BORROW_TRANSACTION_ROUTES.BORROW, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: borrowData
    });

    const data = await response.json(); 

    if (!response.ok) {
        showError('Something went wrong', data.message || 'Please try again.');
        return;
    }

    // Close modal first
    closeConfirmBorrowModal();
    showToast('Book Borrowed Successfully!', 'success');
    loadBorrowers(1, false);
}

export async function getAvailableCopies(bookId) {
    try {
        const response = await fetch(`/staff/books/${bookId}/available-copies`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            return [];
        }

        const result = await response.json();
        return result.copies || [];
    } catch (error) {
        console.error('Failed to fetch available copies:', error);
        return [];
    }
}

export async function returnBook(formData) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    formData.append('_token', csrfToken);

    // Step 1: Validate only
    // formData.append('validate_only', 1);
    const response = await fetch(BORROW_TRANSACTION_ROUTES.RETURN, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) {
            if (result.status === 'invalid') {
                showWarning('Invalid Return', result.message);
                // closeConfirmReturnModal();
            }
            return false;
        }
        showWarning('Something went wrong', 'Please try again.');
        return false;
    }

    showToast('Book Returned Successfully!', 'success');
    return true;
}
