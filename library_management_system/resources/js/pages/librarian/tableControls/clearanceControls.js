import { CLEARANCE_RECORDS_FILTERS } from "../../../utils/tableFilters.js";
import { initSearch, initFilter, initPagination } from "../../../tableControls.js";
import { loadClearanceRecords } from "../../../ajax/librarianSectionsHandler.js";
import { replaceNodeWithClone } from "../../../utils.js";

export function initClearanceTableControls() {
    // Remove previous listeners by replacing nodes
    replaceNodeWithClone('#clearance-records-search');
    replaceNodeWithClone('#clearance-records-role-filter');
    replaceNodeWithClone('#clearance-records-status-filter');
    replaceNodeWithClone('#clearance-records-sort-filter');

    const resetBtn = replaceNodeWithClone('#reset-clearance-records-filters');

    // Use tableControls.js helpers
    initPagination(loadClearanceRecords);

    initSearch('#clearance-records-search', loadClearanceRecords);

    initFilter('#clearance-records-role-filter', loadClearanceRecords);
    initFilter('#clearance-records-status-filter', loadClearanceRecords);
    initFilter('#clearance-records-sort-filter', loadClearanceRecords);

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('clearance-records-search');
            const roleFilter = document.getElementById('clearance-records-role-filter');
            const statusFilter = document.getElementById('clearance-records-status-filter');
            const sortFilter = document.getElementById('clearance-records-sort-filter');

            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = 'date_desc';

            CLEARANCE_RECORDS_FILTERS.search = '';
            CLEARANCE_RECORDS_FILTERS.role = '';
            CLEARANCE_RECORDS_FILTERS.status = '';
            CLEARANCE_RECORDS_FILTERS.sort = 'date_desc';

            loadClearanceRecords(1);
        });
    }
}

initClearanceTableControls();