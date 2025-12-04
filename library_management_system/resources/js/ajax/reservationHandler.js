import { BUSINESS_RULE_VIOLATION, NOT_FOUND } from "../config";
import { reloadStaffDashboardData } from "./staffDashboardHandler";
import { showError, showConfirmation, showWarning, showToast, showDangerConfirmation} from "../utils/alerts.js";
import { loadReservationRecords } from "./librarianSectionsHandler";
import { closeConfirmBorrowModal } from "../pages/staff/confirmBorrow";
import { TRANSACTION_ROUTES } from "../config";

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

    reloadStaffDashboardData();

    showToast('Reservation Successful!', 'success');
    return true;
}

export async function cancelReservation(reservationId, isStaffAction = true) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const isConfirmed = await showDangerConfirmation(
        'Cancel Reservation?',
        'Are you sure you want to cancel this reservation?',
        'Yes, cancel'
    );      

    if (!isConfirmed) return false;

    const route = isStaffAction ? TRANSACTION_ROUTES.STAFF_CANCEL_RESERVATION(reservationId) : TRANSACTION_ROUTES.LIBRARIAN_CANCEL_RESERVATION(reservationId);
    
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

    if (isStaffAction) {
        reloadStaffDashboardData();
    } else {
        loadReservationRecords(undefined, false);
    }

    showToast('Reservation Cancelled!', 'success');
    return true;
}
