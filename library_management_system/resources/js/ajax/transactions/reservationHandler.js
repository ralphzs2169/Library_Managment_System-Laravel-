import { BUSINESS_RULE_VIOLATION, NOT_FOUND } from "../../config.js";
import { reloadStaffDashboardData } from "../staffDashboardHandler.js";
import { showError, showConfirmation, showWarning, showToast, showDangerConfirmation} from "../../utils/alerts.js";
import { loadReservationRecords } from "../librarianSectionsHandler.js";
import { closeConfirmBorrowModal } from "../../pages/staff/confirmBorrow.js";
import { TRANSACTION_ROUTES } from "../../config.js";
import { loadBorrowersLibrarianSection } from "../librarianSectionsHandler.js";

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

    let result = await response.json();

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

    result = await response.json(); 

    if (!response.ok) {
        showError('Something went wrong', result.message || 'Please try again.');
        return false;
    }

    const performerRole = result.data.action_performer_role;
   
    if (performerRole === 'staff') {
        reloadStaffDashboardData();
    } else if (performerRole === 'librarian') {
        loadBorrowersLibrarianSection(undefined, false);
    }

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

    const performedBy = result.data.action_performer_role;
    
    if (performedBy === 'staff') {
        reloadStaffDashboardData();
    } else {
        loadReservationRecords(undefined, false);
        loadBorrowersLibrarianSection(undefined, false);
    }

    showToast('Reservation Cancelled!', 'success');
    return true;
}
