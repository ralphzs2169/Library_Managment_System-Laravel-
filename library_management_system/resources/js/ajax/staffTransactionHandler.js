import { showError, showConfirmation, showWarning, showToast, showDangerConfirmation} from "../utils/alerts.js";
import {  INVALID_INPUT, BUSINESS_RULE_VIOLATION, NOT_FOUND } from "../config.js";
import { displayInputErrors, scrollToFirstErrorInModal } from "../helpers.js";
import { TRANSACTION_ROUTES } from "../config.js";
import { closeConfirmBorrowModal } from "../pages/staff/confirmBorrow.js";
import { closeConfirmRenewModal } from "../pages/staff/confirmRenew.js";
import { closeConfirmReturnModal } from "../pages/staff/confirmReturn.js";
import { reloadStaffDashboardData } from "./staffDashboardHandler.js";
import { loadReservationRecords } from "./librarianSectionsHandler.js";

export async function borrowBook(borrowData, isStaffAction = true) {
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    borrowData.append('_token', csrfToken);

    const validateRoute = isStaffAction ? TRANSACTION_ROUTES.STAFF_VALIDATE_BORROW : TRANSACTION_ROUTES.LIBRARIAN_VALIDATE_BORROW;
    // Step 1: Validate only
    let response = await fetch(validateRoute, {
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
    
    if (!isConfirmed) return false;

    const performRoute = isStaffAction ? TRANSACTION_ROUTES.STAFF_PERFORM_BORROW : TRANSACTION_ROUTES.LIBRARIAN_PERFORM_BORROW;
    // Step 3: Submit borrow
    response = await fetch(performRoute, {
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
        return false;
    }

    isStaffAction ? reloadStaffDashboardData() : loadReservationRecords(undefined, false);

    showToast(`Book ${borrowData.get('is_from_reservation') === 'true' ? 'Checked out' : 'Borrowed'} Successfully!`, 'success');
    return true;
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
      console.log(data);


    if (!response.ok) {
        showError('Something went wrong', data.message || 'Please try again.');
        return false;
    }

    showToast('Book Returned Successfully!', 'success');
    reloadStaffDashboardData();
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

    reloadStaffDashboardData();

    showToast('Renewal Successful!', 'success');
    return true;
}
