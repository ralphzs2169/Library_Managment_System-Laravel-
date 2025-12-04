import { RESERVATION_RECORDS_FILTERS } from "../../../utils/tableFilters.js";
import { initSearch, initFilter, initPagination } from "../../../tableControls.js";
import { loadReservationRecords } from "../../../ajax/librarianSectionsHandler.js";
import { replaceNodeWithClone } from "../../../utils.js";

export function initReservationRecordsTableControls() {
    // Remove previous listeners by replacing nodes
    replaceNodeWithClone('#reservation-records-search');
    replaceNodeWithClone('#reservation-records-role-filter');
    replaceNodeWithClone('#reservation-records-status-filter');
    replaceNodeWithClone('#reservation-records-sort-filter');
    replaceNodeWithClone('#reservation-records-semester-filter'); // NEW

    const resetBtn = replaceNodeWithClone('#reset-reservation-records-filters');

    // Use tableControls.js helpers
    initPagination(loadReservationRecords);

    initSearch('#reservation-records-search', loadReservationRecords, '#reservation-records-container', [2, 3]);

    initFilter('#reservation-records-role-filter', loadReservationRecords);
    initFilter('#reservation-records-status-filter', loadReservationRecords);
    initFilter('#reservation-records-sort-filter', loadReservationRecords);
    initFilter('#reservation-records-semester-filter', loadReservationRecords); // NEW

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('reservation-records-search');
            const roleFilter = document.getElementById('reservation-records-role-filter');
            const statusFilter = document.getElementById('reservation-records-status-filter');
            const sortFilter = document.getElementById('reservation-records-sort-filter');
            const semesterFilter = document.getElementById('reservation-records-semester-filter');
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = 'deadline_asc';
            if (semesterFilter) {
                // Default to active semester if available, else all
                const activeSemesterOption = semesterFilter.querySelector('option[selected]');
                semesterFilter.value = activeSemesterOption ? activeSemesterOption.value : '';
            }
            RESERVATION_RECORDS_FILTERS.search = '';
            RESERVATION_RECORDS_FILTERS.role = '';
            RESERVATION_RECORDS_FILTERS.status = '';
            RESERVATION_RECORDS_FILTERS.sort = 'deadline_asc';
            RESERVATION_RECORDS_FILTERS.semester = semesterFilter ? semesterFilter.value : null;
            loadReservationRecords(1);
        });
    }
}

initReservationRecordsTableControls();