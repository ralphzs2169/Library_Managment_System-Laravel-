import { showError, showConfirmation, showSuccessWithRedirect, showWarning, showToast } from "../utils.js";
import {  VALIDATION_ERROR, BUSINESS_RULE_VIOLATION, NOT_FOUND } from "../config.js";
import { displayInputErrors, scrollToFirstErrorInModal } from "../helpers.js";
import { TRANSACTION_ROUTES } from "../config.js";
import { loadBorrowers } from "./borrowerHandler.js";
import { closeConfirmBorrowModal } from "../pages/staff/confirmBorrow.js";
import { closeConfirmRenewModal } from "../pages/staff/confirmRenew.js";

export async function borrowBook(borrowData) {
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    borrowData.append('_token', csrfToken);

    // Step 1: Validate only
    borrowData.append('validate_only', 1);
    let response = await fetch(TRANSACTION_ROUTES.BORROW, {
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
    response = await fetch(TRANSACTION_ROUTES.BORROW, {
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
    loadBorrowers(undefined, false);
    closeConfirmBorrowModal();
    showToast('Book Borrowed Successfully!', 'success');
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
    const response = await fetch(TRANSACTION_ROUTES.RETURN, {
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
    loadBorrowers(undefined, false);
    return true;
}

export async function renewBook(renewData) {
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    renewData.append('_token', csrfToken);

    // Step 1: Validate 
    let response = await fetch(TRANSACTION_ROUTES.VALIDATE_RENEWAL, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: renewData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) {
            displayInputErrors(result.data.errors, 'confirm-renew-modal', false);
            scrollToFirstErrorInModal();
            return false;
        } else if (response.status === BUSINESS_RULE_VIOLATION) {
            showWarning('Renewal Not Allowed', result.message);
            closeConfirmRenewModal();
            return false;
        } else if (response.status === NOT_FOUND) {
            showWarning('Not Found', result.message);
            closeConfirmRenewModal();
            return false;
        }
        showError('Something went wrong', result.message || 'Please try again Later.');
        return false;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Confirm Renewal?',
        `You are about to renew this book for ${result.data.renewer_fullname}. Proceed?`,
        'Yes, confirm'
    );
    
    if (!isConfirmed) return false;

    // Step 3: Submit borrow
    response = await fetch(TRANSACTION_ROUTES.PERFORM_RENEWAL, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: renewData
    });

    const data = await response.json(); 

    if (!response.ok) {
        showError('Something went wrong', data.message || 'Please try again.');
        return false;
    }

    // Close modal first
    loadBorrowers(undefined, false);
    showToast('Rewewal Transaction Successful!', 'success');
    return true;
}