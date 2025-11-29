import { BORROWER_FILTERS } from "../../utils/tableFilters.js";
import { initSearch, initFilter } from "../../tableControls.js";
import { loadBorrowers } from "../../ajax/borrowerHandler.js";
import { initStaffDashboardPagination } from "../../tableControls.js"; // <-- fix import path
// Helper to remove all previous listeners by replacing the node
function replaceNodeWithClone(selector) {
    const el = document.querySelector(selector);
    if (el) {
        const clone = el.cloneNode(true);
        el.parentNode.replaceChild(clone, el);
        return clone;
    }
    return null;
}

export function initBorrowersTableControls() {
    // Remove previous listeners by replacing nodes
    const searchInput = replaceNodeWithClone('#borrowers-search');
    const roleFilter = replaceNodeWithClone('#borrower-role-filter');
    const statusFilter = replaceNodeWithClone('#borrower-status-filter');
    const resetBtn = replaceNodeWithClone('#reset-borrower-filters');
    const container = document.getElementById('members-table-container');

    // Use tableControls.js helpers
    initStaffDashboardPagination(loadBorrowers); // <-- make sure this import is correct
    initSearch('#borrowers-search', loadBorrowers, '#members-table-container', [2, 3]); // Adjust column indexes as needed
    initFilter('#borrower-role-filter', loadBorrowers);
    initFilter('#borrower-status-filter', loadBorrowers);

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('borrowers-search');
            const roleFilter = document.getElementById('borrower-role-filter');
            const statusFilter = document.getElementById('borrower-status-filter');
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            BORROWER_FILTERS.search = '';
            BORROWER_FILTERS.role = '';
            BORROWER_FILTERS.status = '';
            loadBorrowers(1);
        });
    }
}

// Auto-run on import/load
initBorrowersTableControls();