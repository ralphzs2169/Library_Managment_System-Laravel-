import { INVALID_INPUT, TRANSACTION_ROUTES } from "../config";
import { reloadStaffDashboardData } from "./staffDashboardHandler";
import { showWarning, showToast, showError, showConfirmation, showDangerConfirmation } from "../utils/alerts";
import { displayInputErrors } from "../helpers";
import { loadPenaltyRecords } from "./librarianSectionsHandler";


export async function processPenalty(paymentDetails, form, isStaff = true) {
    // Make sure validate_only is set after all other fields
    paymentDetails.set('validate_only', 1);
    const penaltyId = paymentDetails.get('penalty_id');
    
    const route = isStaff ? TRANSACTION_ROUTES.STAFF_PROCESS_PENALTY(penaltyId) : TRANSACTION_ROUTES.LIBRARIAN_PROCESS_PENALTY(penaltyId);
    // Add CSRF token and method spoofing for PUT request
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    paymentDetails.append('_token', csrfToken);
    paymentDetails.append('_method', 'PUT');

    let response = await fetch(route, {
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
    response = await fetch(route, {
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

    isStaff ? reloadStaffDashboardData() : loadPenaltyRecords(undefined, false);

    return true;
}

export async function cancelPenalty(penaltyId, borrowerId, isStaff = true) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const route = isStaff ? TRANSACTION_ROUTES.STAFF_CANCEL_PENALTY(penaltyId, borrowerId) : TRANSACTION_ROUTES.LIBRARIAN_CANCEL_PENALTY(penaltyId, borrowerId);
    
    const isConfirmed = await showDangerConfirmation(
        'Cancel Penalty?',
        'Are you sure you want to cancel this penalty?',
        'Yes, cancel'
    );      

    if (!isConfirmed) return false;

    const response = await fetch(route, {
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

    isStaff ? reloadStaffDashboardData() : loadPenaltyRecords(undefined, false);

    showToast('Penalty Cancelled!', 'success');
    return true;
}