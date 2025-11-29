import { showError, showConfirmation, showWarning, showToast, showDangerConfirmation} from "../utils/alerts.js";
import {  INVALID_INPUT, BUSINESS_RULE_VIOLATION, NOT_FOUND } from "../config.js";
import { displayInputErrors, scrollToFirstErrorInModal } from "../helpers.js";
import { TRANSACTION_ROUTES } from "../config.js";
import { loadBorrowers } from "./borrowerHandler.js";
import { closeConfirmBorrowModal } from "../pages/staff/confirmBorrow.js";
import { closeConfirmRenewModal } from "../pages/staff/confirmRenew.js";
import { closeConfirmReturnModal } from "../pages/staff/confirmReturn.js";

export async function borrowBook(borrowData) {
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    borrowData.append('_token', csrfToken);

    // Step 1: Validate only
    let response = await fetch(TRANSACTION_ROUTES.VALIDATE_BORROW, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: borrowData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === INVALID_INPUT) {
            displayInputErrors(result.data.errors, 'confirm-borrow-modal', false);
            scrollToFirstErrorInModal();
            return false;
        } else if (response.status === BUSINESS_RULE_VIOLATION) {
            showWarning('Borrowing Not Allowed', result.message);
            closeConfirmBorrowModal();
            return false;
        } else if (response.status === NOT_FOUND) {
            showWarning('Not Found', result.message);
            closeConfirmBorrowModal();
            return false;
        }
        showError('Something went wrong', result.message || 'Please try again Later.');
        return false;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Confirm Borrowing?',
         `You are about to lend this book for ${result.data.borrower_fullname}. Proceed?`,
        'Yes, confirm'
    );
    
    if (!isConfirmed) return;

    // Step 3: Submit borrow
    response = await fetch(TRANSACTION_ROUTES.PERFORM_BORROW, {
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

export async function returnBook(returnData) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    returnData.append('_token', csrfToken);

    // Step 1: Validate only
    // formData.append('validate_only', 1);
    let response = await fetch(TRANSACTION_ROUTES.VALIDATE_RETURN, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: returnData
    });

    let result = await response.json();

    if (!response.ok) {
        if (response.status === BUSINESS_RULE_VIOLATION) {
            showWarning('Return Not Allowed', result.message);
            closeConfirmReturnModal();
            return false;
        } else if (response.status === NOT_FOUND) {
            showWarning('Not Found', result.message);
            closeConfirmReturnModal();
            return false;
        }
        showError('Something went wrong', result.message || 'Please try again Later.');
        return false;
    }

      // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Confirm Return?',
        `You are about to return this book for ${result.data.returner_fullname}. Proceed?`,
        'Yes, confirm'
    );
    
    if (!isConfirmed) return false;

    // Step 3: Submit borrow
    response = await fetch(TRANSACTION_ROUTES.PERFORM_RETURN, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: returnData
    });

    const data = await response.json(); 

    if (!response.ok) {
        showError('Something went wrong', data.message || 'Please try again.');
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
        if (response.status === INVALID_INPUT) {
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
    showToast('Renewal Successful!', 'success');
    return true;
}

export async function addReservation(reservationData) {
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    reservationData.append('_token', csrfToken);

    // Step 1: Validate only
    let response = await fetch(TRANSACTION_ROUTES.VALIDATE_RESERVATION, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: reservationData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === BUSINESS_RULE_VIOLATION) {
            showWarning('Reservation Not Allowed', result.message);
            closeConfirmBorrowModal();
            return false;
        } else if (response.status === NOT_FOUND) {
            showWarning('Not Found', result.message);
            closeConfirmBorrowModal();
            return false;
        }
        showError('Something went wrong', result.message || 'Please try again Later.');
        return false;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Confirm Reservation?',
         `You are about to reserve this book for ${result.data.reserver_fullname}. Proceed?`,
        'Yes, confirm'
    );
    
    if (!isConfirmed) return false;

    // Step 3: Submit borrow
    response = await fetch(TRANSACTION_ROUTES.PERFORM_RESERVATION, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: reservationData
    });

    const data = await response.json(); 

    if (!response.ok) {
        showError('Something went wrong', data.message || 'Please try again.');
        return false;
    }

    loadBorrowers(undefined, false);
    showToast('Reservation Successful!', 'success');
    return true;
}

export async function cancelReservation(reservationId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const isConfirmed = await showDangerConfirmation(
        'Cancel Reservation?',
        'Are you sure you want to cancel this reservation?',
        'Yes, cancel'
    );      

    if (!isConfirmed) return false;

    const response = await fetch(TRANSACTION_ROUTES.CANCEL_RESERVATION(reservationId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });

    const result = await response.json();

    if (!response.ok) {
        showError('Something went wrong', result.message || 'Please try again later.');
        return false;
    }

    loadBorrowers(undefined, false);
    showToast('Reservation Cancelled!', 'success');
    return true;
}
