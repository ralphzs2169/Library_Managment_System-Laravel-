import { apiRequest, getJsonHeaders } from "../utils.js";
import { showToast } from '../utils/alerts.js';
import { loadBorrowersLibrarianSection } from "./librarianSectionsHandler.js";

const SUSPENSION_ROUTES = {
    SUSPEND: '/librarian/users/suspend',
    LIFT: '/librarian/users/lift-suspension'
};

export async function suspendUser(userId, reason, staffId) {
    // Append userId to the URL
    const result = await apiRequest(`${SUSPENSION_ROUTES.SUSPEND}/${userId}`, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ reason: reason, staff_id: staffId })
    });

    

    if (result.success) {
        loadBorrowersLibrarianSection(undefined, false);
        showToast('User suspended successfully', 'success');
        return true;
    } 
    return false;
}

export async function liftSuspension(userId, reason, staffId) {
    // Append userId to the URL
    const result = await apiRequest(`${SUSPENSION_ROUTES.LIFT}/${userId}`, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ reason: reason, staff_id: staffId })
    });

    if (result.success) {
        loadBorrowersLibrarianSection(undefined, false);
        showToast('Suspension lifted successfully', 'success');
        return true;
    } 
    return false;
}
