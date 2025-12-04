import { PENALTY_RECORDS_FILTERS } from "../../../utils/tableFilters.js";
import { initSearch, initFilter, initPagination } from "../../../tableControls.js";
import { loadPenaltyRecords } from "../../../ajax/librarianSectionsHandler.js";
import { replaceNodeWithClone } from "../../../utils.js";

export function initPenaltyRecordsTableControls() {
    // Remove previous listeners by replacing nodes
    replaceNodeWithClone('#penalty-records-search');
    replaceNodeWithClone('#penalty-records-role-filter');
    replaceNodeWithClone('#penalty-records-type-filter');
    replaceNodeWithClone('#penalty-records-status-filter');
    replaceNodeWithClone('#penalty-records-sort-filter');
    replaceNodeWithClone('#penalty-records-semester-filter');

    const resetBtn = replaceNodeWithClone('#reset-penalty-records-filters');

    // Use tableControls.js helpers
    initPagination(loadPenaltyRecords);

    initSearch('#penalty-records-search', loadPenaltyRecords, '#penalty-records-container', [2, 6]);

    initFilter('#penalty-records-role-filter', loadPenaltyRecords);
    initFilter('#penalty-records-type-filter', loadPenaltyRecords);
    initFilter('#penalty-records-status-filter', loadPenaltyRecords);
    initFilter('#penalty-records-sort-filter', loadPenaltyRecords);
    initFilter('#penalty-records-semester-filter', loadPenaltyRecords);

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('penalty-records-search');
            const roleFilter = document.getElementById('penalty-records-role-filter');
            const typeFilter = document.getElementById('penalty-records-type-filter');
            const statusFilter = document.getElementById('penalty-records-status-filter');
            const sortFilter = document.getElementById('penalty-records-sort-filter');
            const semesterFilter = document.getElementById('penalty-records-semester-filter');

            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (typeFilter) typeFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = ''; // Priority (Unpaid First)
            if (semesterFilter) {
                // Default to active semester if available, else all
                const activeSemesterOption = semesterFilter.querySelector('option[selected]');
                semesterFilter.value = activeSemesterOption ? activeSemesterOption.value : '';
            }

            PENALTY_RECORDS_FILTERS.search = '';
            PENALTY_RECORDS_FILTERS.role = '';
            PENALTY_RECORDS_FILTERS.type = '';
            PENALTY_RECORDS_FILTERS.status = '';
            PENALTY_RECORDS_FILTERS.sort = '';
            PENALTY_RECORDS_FILTERS.semester = semesterFilter ? semesterFilter.value : null;

            loadPenaltyRecords(1);
        });
    }
}

initPenaltyRecordsTableControls();