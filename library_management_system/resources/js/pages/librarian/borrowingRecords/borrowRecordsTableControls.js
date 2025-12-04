import { BORROW_RECORDS_FILTERS } from "../../../utils/tableFilters.js";
import { initSearch, initFilter, initPagination } from "../../../tableControls.js";
import { loadBorrowRecords } from "../../../ajax/librarianSectionsHandler.js";
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

export function initBorrowRecordsTableControls() {
    // Remove previous listeners by replacing nodes
    replaceNodeWithClone('#borrow-records-search');
    replaceNodeWithClone('#borrow-records-role-filter');
    replaceNodeWithClone('#borrow-records-status-filter');
    replaceNodeWithClone('#borrow-records-sort-filter');
    replaceNodeWithClone('#borrow-records-semester-filter'); // NEW

    const resetBtn = replaceNodeWithClone('#reset-borrow-records-filters');

    // Use tableControls.js helpers
    initPagination(loadBorrowRecords);

    initSearch('#borrow-records-search', loadBorrowRecords, '#borrow-records-container', [2, 3]);

    initFilter('#borrow-records-role-filter', loadBorrowRecords);
    initFilter('#borrow-records-status-filter', loadBorrowRecords);
    initFilter('#borrow-records-sort-filter', loadBorrowRecords);
    initFilter('#borrow-records-semester-filter', loadBorrowRecords); // NEW

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            const searchInput = document.getElementById('borrow-records-search');
            const roleFilter = document.getElementById('borrow-records-role-filter');
            const statusFilter = document.getElementById('borrow-records-status-filter');
            const sortFilter = document.getElementById('borrow-records-sort-filter');
            const semesterFilter = document.getElementById('borrow-records-semester-filter');
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortFilter) sortFilter.value = ''; // Priority sort
            if (semesterFilter) {
                const activeSemesterOption = semesterFilter.querySelector('option[selected]');
                semesterFilter.value = activeSemesterOption ? activeSemesterOption.value : '';
            }
            BORROW_RECORDS_FILTERS.search = '';
            BORROW_RECORDS_FILTERS.role = '';
            BORROW_RECORDS_FILTERS.status = '';
            BORROW_RECORDS_FILTERS.semester = semesterFilter ? semesterFilter.value : null;
            BORROW_RECORDS_FILTERS.sort = '';
            loadBorrowRecords(1);
        });
    }
}

initBorrowRecordsTableControls();