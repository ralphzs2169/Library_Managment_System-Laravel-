import { BORROWER_FILTERS, ACTIVE_BORROW_FILTERS, UNPAID_PENALTIES_FILTERS, QUEUE_RESERVATIONS_FILTERS } from "../../utils/tableFilters.js";
import { initSearch, initFilter } from "../../tableControls.js";
import { loadActiveBorrows, loadMembers, loadUnpaidPenalties, loadQueueReservations } from "../../ajax/staffDashboardHandler.js";
import { initStaffDashboardPagination } from "../../tableControls.js";
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
    const sortFilter = replaceNodeWithClone('#borrower-sort-filter');

    const resetBtn = replaceNodeWithClone('#reset-borrower-filters');
    const container = document.getElementById('members-table-container');

    // Use tableControls.js helpers
    initStaffDashboardPagination(loadMembers);

    initSearch('#borrowers-search', loadMembers, '#members-table-container', [2, 3]);

    initFilter('#borrower-role-filter', loadMembers);
    initFilter('#borrower-status-filter', loadMembers);
    initFilter('#borrower-sort-filter', loadMembers);

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('borrowers-search');
            const roleFilter = document.getElementById('borrower-role-filter');
            const statusFilter = document.getElementById('borrower-status-filter');
            const sortFilter = document.getElementById('borrower-sort-filter');
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = '';
            BORROWER_FILTERS.search = '';
            BORROWER_FILTERS.role = '';
            BORROWER_FILTERS.status = '';
            BORROWER_FILTERS.sort = '';
            loadMembers(1);
        });
    }
}

export function initActiveBorrowsTableControls() {
    // Remove previous listeners by replacing nodes
    const searchInput = replaceNodeWithClone('#active-borrows-search');
    const roleFilter = replaceNodeWithClone('#active-borrows-role-filter');
    const statusFilter = replaceNodeWithClone('#active-borrows-status-filter');
    const sortFilter = replaceNodeWithClone('#active-borrows-sort-filter');


    const resetBtn = replaceNodeWithClone('#reset-active-borrows-filters');
    const container = document.getElementById('active-borrows-table-container');

    // Pagination
    initStaffDashboardPagination(loadActiveBorrows);

    // Search
    initSearch('#active-borrows-search', loadActiveBorrows, '#active-borrows-table-container', [1, 3]); // Adjust column indexes if needed

    // Filters
    initFilter('#active-borrows-role-filter', loadActiveBorrows);
    initFilter('#active-borrows-status-filter', loadActiveBorrows);
    initFilter('#active-borrows-sort-filter', loadActiveBorrows);

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('active-borrows-search');
            const roleFilter = document.getElementById('active-borrows-role-filter');
            const statusFilter = document.getElementById('active-borrows-status-filter');
            const sortFilter = document.getElementById('active-borrows-sort-filter');
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = '';
            ACTIVE_BORROW_FILTERS.search = '';
            ACTIVE_BORROW_FILTERS.role = '';
            ACTIVE_BORROW_FILTERS.status = '';
            ACTIVE_BORROW_FILTERS.sort = '';
            loadActiveBorrows(1);
        });
    }
}

export function initUnpaidPenaltiesTableControls() {
    console.log("Initializing Unpaid Penalties Table Controls");
    // Remove previous listeners by replacing nodes
    const searchInput = replaceNodeWithClone('#unpaid-penalties-search');
    const roleFilter = replaceNodeWithClone('#unpaid-penalties-role-filter');
    const typeFilter = replaceNodeWithClone('#unpaid-penalties-type-filter');
    const statusFilter = replaceNodeWithClone('#unpaid-penalties-status-filter');
    const sortFilter = replaceNodeWithClone('#unpaid-penalties-sort-filter');

    const resetBtn = replaceNodeWithClone('#reset-unpaid-penalties-filters');
    const container = document.getElementById('unpaid-penalties-table-container');

    // Pagination
    initStaffDashboardPagination(loadUnpaidPenalties);

    // Search
    initSearch('#unpaid-penalties-search', loadUnpaidPenalties, '#unpaid-penalties-table-container', [1, 3]); // Adjust column indexes if needed

    // Filters
    initFilter('#unpaid-penalties-role-filter', loadUnpaidPenalties);
    initFilter('#unpaid-penalties-type-filter', loadUnpaidPenalties);
    initFilter('#unpaid-penalties-status-filter', loadUnpaidPenalties);
    initFilter('#unpaid-penalties-sort-filter', loadUnpaidPenalties);
    
    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('unpaid-penalties-search');
            const roleFilter = document.getElementById('unpaid-penalties-role-filter');
            const typeFilter = document.getElementById('unpaid-penalties-type-filter');
            const statusFilter = document.getElementById('unpaid-penalties-status-filter');
            const sortFilter = document.getElementById('unpaid-penalties-sort-filter');

            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (typeFilter) typeFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = '';

            UNPAID_PENALTIES_FILTERS.search = '';
            UNPAID_PENALTIES_FILTERS.role = '';
            UNPAID_PENALTIES_FILTERS.status = '';
            UNPAID_PENALTIES_FILTERS.type = '';
            UNPAID_PENALTIES_FILTERS.sort = '';
            loadUnpaidPenalties(1);
        });
    }
}

export function initQueueReservationsTableControls() {
    // Remove previous listeners by replacing nodes
    const searchInput = replaceNodeWithClone('#queue-reservations-search');
    const roleFilter = replaceNodeWithClone('#queue-reservations-role-filter');
    const statusFilter = replaceNodeWithClone('#queue-reservations-status-filter');
    const sortFilter = replaceNodeWithClone('#queue-reservations-sort-filter');

    const resetBtn = replaceNodeWithClone('#reset-queue-reservations-filters');
    const container = document.getElementById('queue-reservations-table-container');

    // Pagination
    initStaffDashboardPagination(loadQueueReservations);

    // Search
    initSearch('#queue-reservations-search', loadQueueReservations, '#queue-reservations-table-container', [1, 3]); // Adjust column indexes if needed

    // Filters
    initFilter('#queue-reservations-role-filter', loadQueueReservations);
    initFilter('#queue-reservations-status-filter', loadQueueReservations);
    initFilter('#queue-reservations-sort-filter', loadQueueReservations);
    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('queue-reservations-search');
            const roleFilter = document.getElementById('queue-reservations-role-filter');
            const statusFilter = document.getElementById('queue-reservations-status-filter');
            const sortFilter = document.getElementById('queue-reservations-sort-filter');
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = '';
            QUEUE_RESERVATIONS_FILTERS.search = '';
            QUEUE_RESERVATIONS_FILTERS.status = '';
            QUEUE_RESERVATIONS_FILTERS.expiry = '';
            QUEUE_RESERVATIONS_FILTERS.sort = '';
            loadQueueReservations(1);
        });
    }
}
// Auto-run on import/load
initBorrowersTableControls();