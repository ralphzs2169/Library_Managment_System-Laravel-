import { showError, showConfirmation, showWarning, showToast } from "../utils/alerts.js";
import { BUSINESS_RULE_VIOLATION, NOT_FOUND, TRANSACTION_ROUTES } from "../config.js";
import { reloadStaffDashboardData } from "../ajax/staffDashboardHandler.js";
// import { loadClearanceRecords } from "../ajax/librarianSectionsHandler.js";


export async function requestClearance(userId, requestorId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('requestor_id', requestorId);
    formData.append('_token', csrfToken);

    // Step 1: Validate
    let response = await fetch(TRANSACTION_ROUTES.VALIDATE_CLEARANCE_REQUEST(userId, requestorId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    });

    let result = await response.json();

    if (!response.ok) {
        if (response.status === BUSINESS_RULE_VIOLATION) {
            showWarning('Request Not Allowed', result.message);
            return false;
        } else if (response.status === NOT_FOUND) {
            showWarning('Not Found', result.message);
            return false;
        }
        showError('Something went wrong', result.message || 'Please try again.');
        return false;
    }

    // Step 2: Confirmation
    const isConfirmed = await showConfirmation(
        'Request Clearance?',
        'Are you sure you want to request clearance for this user?',
        'Yes, Request'
    );

    if (!isConfirmed) return false;

    // Step 3: Perform
    response = await fetch(TRANSACTION_ROUTES.PERFORM_CLEARANCE_REQUEST(userId, requestorId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    });

    result = await response.json();

    if (!response.ok) {
        showError('Error', result.message || 'Failed to request clearance.');
        return false;
    }

    showToast('Clearance Requested Successfully', 'success');
    
    // Reload data (assuming this is primarily a staff action)
    if (typeof reloadStaffDashboardData === 'function') {
        reloadStaffDashboardData();
    }

    return true;
}

export async function approveClearance(clearanceId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const isConfirmed = await showConfirmation(
        'Approve Clearance?',
        'Are you sure you want to approve this clearance request? This action cannot be undone.',
        'Yes, Approve'
    );

    if (!isConfirmed) return false;

    const response = await fetch(`/transaction/clearance/${clearanceId}/approve`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === BUSINESS_RULE_VIOLATION) {
            showWarning('Approval Failed', result.message);
        } else {
            showError('Error', result.message || 'Failed to approve clearance.');
        }
        return false;
    }

    showToast('Clearance Approved', 'success');

    // Reload librarian table
    if (typeof loadClearanceRecords === 'function') {
        loadClearanceRecords(undefined, false);
    }

    return true;
}

export async function rejectClearance(clearanceId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const isConfirmed = await showConfirmation(
        'Reject Clearance?',
        'Are you sure you want to reject this clearance request?',
        'Yes, Reject'
    );

    if (!isConfirmed) return false;

    const response = await fetch(`/transaction/clearance/${clearanceId}/reject`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });

    const result = await response.json();

    if (!response.ok) {
        showError('Error', result.message || 'Failed to reject clearance.');
        return false;
    }

    showToast('Clearance Rejected', 'success');

    if (typeof loadClearanceRecords === 'function') {
        loadClearanceRecords(undefined, false);
    }

    return true;
}
