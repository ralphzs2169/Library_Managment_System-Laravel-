import { INVALID_INPUT, TRANSACTION_ROUTES } from "../config";
import { loadBorrowers } from "./borrowerHandler";
import { showWarning, showToast, showError, showConfirmation, showDangerConfirmation } from "../utils/alerts";
import { displayInputErrors } from "../helpers";

export async function updatePenalty(paymentDetails, form) {
    // Make sure validate_only is set after all other fields
    paymentDetails.set('validate_only', 1);
    const penaltyId = paymentDetails.get('penalty_id');
    
    // Add CSRF token and method spoofing for PUT request
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    paymentDetails.append('_token', csrfToken);
    paymentDetails.append('_method', 'PUT');

    let response = await fetch(TRANSACTION_ROUTES.UPDATE_PENALTY(penaltyId), {
        method: 'POST', 
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: paymentDetails
    });

    let result = await response.json();

    if (!response.ok) {
        if (response.status === INVALID_INPUT) {
            // Check for invalid status (business rule violation)
            displayInputErrors(result.errors, form, false);
            return false;
        }
        showWarning('Something went wrong', 'Please try again.');
        return false;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Confirm Payment',
        'Are you sure you want to proceed with this payment?',
        'Yes, Confirm'
    );
    
    if (!isConfirmed) return false;
    
    paymentDetails.delete('validate_only');

    // Step 3: Submit update
    response = await fetch(TRANSACTION_ROUTES.UPDATE_PENALTY(penaltyId), {
        method: 'POST', 
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: paymentDetails
    });
    result = await response.json();

    if (!response.ok) {
        showWarning('Something went wrong', 'Failed to update book. Please try again later.');
        return false;
    }

    if (result.status === 'invalid' || result.status === 'error') {
        showWarning('Something went wrong', result.message || 'Failed to update book. Please try again later.');
        return false;
    }
    
    showToast('Payment Successful!', 'success');
    loadBorrowers(undefined, false);
    return true;
        // showSuccessWithRedirect('Success', 'Book updated successfully!', window.location.href);
}

export async function cancelPenalty(penaltyId, borrowerId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const isConfirmed = await showDangerConfirmation(
        'Cancel Reservation?',
        'Are you sure you want to cancel this penalty?',
        'Yes, cancel'
    );      

    if (!isConfirmed) return false;

    const response = await fetch(TRANSACTION_ROUTES.CANCEL_PENALTY(penaltyId, borrowerId), {
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
    showToast('Penalty Cancelled!', 'success');
    return true;
}