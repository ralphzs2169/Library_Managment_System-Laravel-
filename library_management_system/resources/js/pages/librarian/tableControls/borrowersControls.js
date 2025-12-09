import { BORROWER_FILTERS } from "../../../utils/tableFilters.js";
import { initSearch, initFilter, initPagination } from "../../../tableControls.js";
import { loadBorrowersLibrarianSection } from "../../../ajax/librarianSectionsHandler.js";
import { replaceNodeWithClone } from "../../../utils.js";

export function initBorrowersTableControls() {
    // Remove previous listeners by replacing nodes
    replaceNodeWithClone('#borrowers-search');
    replaceNodeWithClone('#borrowers-role-filter');
    replaceNodeWithClone('#borrowers-status-filter');
    replaceNodeWithClone('#borrowers-sort-filter');
    
    // New filters
    replaceNodeWithClone('#borrowers-active-borrows-filter');
    replaceNodeWithClone('#borrowers-reservation-filter');
    replaceNodeWithClone('#borrowers-fines-filter');

    const resetBtn = replaceNodeWithClone('#reset-borrowers-filters');

    // Use tableControls.js helpers
    initPagination(loadBorrowersLibrarianSection);

    initSearch('#borrowers-search', loadBorrowersLibrarianSection, '#borrowers-container', [2, 3]);

    initFilter('#borrowers-role-filter', loadBorrowersLibrarianSection);
    initFilter('#borrowers-status-filter', loadBorrowersLibrarianSection);
    initFilter('#borrowers-sort-filter', loadBorrowersLibrarianSection);
    
    // New filters
    initFilter('#borrowers-active-borrows-filter', loadBorrowersLibrarianSection);
    initFilter('#borrowers-reservation-filter', loadBorrowersLibrarianSection);
    initFilter('#borrowers-fines-filter', loadBorrowersLibrarianSection);

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('borrowers-search');
            const roleFilter = document.getElementById('borrowers-role-filter');
            const statusFilter = document.getElementById('borrowers-status-filter');
            const sortFilter = document.getElementById('borrowers-sort-filter');
            
            const activeBorrowsFilter = document.getElementById('borrowers-active-borrows-filter');
            const reservationFilter = document.getElementById('borrowers-reservation-filter');
            const finesFilter = document.getElementById('borrowers-fines-filter');
            
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = '';
            
            if (activeBorrowsFilter) activeBorrowsFilter.value = '';
            if (reservationFilter) reservationFilter.value = '';
            if (finesFilter) finesFilter.value = '';
            
            BORROWER_FILTERS.search = '';
            BORROWER_FILTERS.role = '';
            BORROWER_FILTERS.status = '';
            BORROWER_FILTERS.sort = '';
            
            BORROWER_FILTERS.active_borrows = '';
            BORROWER_FILTERS.reservation_status = '';
            BORROWER_FILTERS.fines = '';
            
            loadBorrowersLibrarianSection(1);
        });
    }
}

initBorrowersTableControls();