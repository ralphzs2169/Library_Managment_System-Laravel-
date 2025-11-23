import { loadBorrowers } from "../../api/borrowerHandler";
import { initPagination, initSearch } from "../../tableControls.js";
import { SEARCH_COLUMN_INDEXES } from "../../utils/tableFilters.js";
import { BORROWER_FILTERS } from "../../utils/tableFilters.js";
// Add filter event listeners
const borrowerStatusFilter = document.getElementById('borrower-status-filter');
const borrowerRoleFilter = document.getElementById('borrower-role-filter');
const resetFiltersBtn = document.getElementById('reset-borrower-filters');
const searchInput = document.getElementById('borrowers-search');

initPagination(loadBorrowers);
initSearch('#borrowers-search', loadBorrowers, '#borrowers-table-body', SEARCH_COLUMN_INDEXES.BORROWERS);

if (borrowerRoleFilter) {
    borrowerRoleFilter.addEventListener('change', () => loadBorrowers(1));
}

if (borrowerStatusFilter) {
    borrowerStatusFilter.addEventListener('change', () => loadBorrowers(1));
}

// Reset filters functionality
if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener('click', () => {
        // Reset all filter inputs to default values
        if (searchInput) searchInput.value = '';
        if (borrowerRoleFilter) borrowerRoleFilter.value = '';
        if (borrowerStatusFilter) borrowerStatusFilter.value = '';
        
        BORROWER_FILTERS.search = '';
        BORROWER_FILTERS.role = '';
        BORROWER_FILTERS.status = '';
        // Reload books with default filters
        loadBorrowers(1);
    });
}
