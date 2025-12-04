import { showSkeleton, hideSkeleton } from "../utils";
import { showError } from "../utils/alerts";
import { BORROW_RECORDS_FILTERS, RESERVATION_RECORDS_FILTERS, PENALTY_RECORDS_FILTERS } from "../utils/tableFilters";
import { SEARCH_COLUMN_INDEXES } from "../utils/tableFilters";
import { LIBRARIAN_SECTION_ROUTES } from "../config";
import { highlightSearchMatches } from "../tableControls";

export async function loadBorrowRecords(page = BORROW_RECORDS_FILTERS.page, scrollUp = true, isFiltered = false) {
    const container = document.getElementById('borrow-records-container');
    const countElement = document.getElementById('header-total-borrow-records');

    if (scrollUp) {
        window.scrollTo(0, 0);
    }

    countElement.textContent = '';

    let skeletonTimer = setTimeout(() => {
        showSkeleton(container, '#borrowing-records-skeleton', '#borrowing-records-real-table-body');
    }, 200); 
    
    try {
        BORROW_RECORDS_FILTERS.page = page;
        const searchInput = document.getElementById('borrow-records-search');
        const roleFilter = document.getElementById('borrow-records-role-filter');
        const statusFilter = document.getElementById('borrow-records-status-filter');
        const sortFilter = document.getElementById('borrow-records-sort-filter');
        const semesterFilter = document.getElementById('borrow-records-semester-filter');

        if (searchInput) BORROW_RECORDS_FILTERS.search = searchInput.value || '';
        if (roleFilter) BORROW_RECORDS_FILTERS.role = roleFilter.value || '';
        BORROW_RECORDS_FILTERS.status = statusFilter ? statusFilter.value : '';
        if (semesterFilter) BORROW_RECORDS_FILTERS.semester = semesterFilter.value || '';
        BORROW_RECORDS_FILTERS.sort = sortFilter ? sortFilter.value : '';

        const params = new URLSearchParams({
            page: BORROW_RECORDS_FILTERS.page,
            search: BORROW_RECORDS_FILTERS.search,
            role: BORROW_RECORDS_FILTERS.role,
            status: BORROW_RECORDS_FILTERS.status,
            sort: BORROW_RECORDS_FILTERS.sort,
            semester: BORROW_RECORDS_FILTERS.semester
        });

        const response = await fetch(`${LIBRARIAN_SECTION_ROUTES.BORROW_RECORDS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        container.innerHTML = data.html;

        countElement.textContent = data.count;

        clearTimeout(skeletonTimer);
        hideSkeleton(container, '#borrowing-records-skeleton', '#borrowing-records-real-table-body');

        // Re-attach borrow record detail listeners after table update (like staff side)
        import('../pages/librarian/borrowingRecords/borrowRecordDetails.js').then(mod => {
            if (mod.initializeBorrowRecordDetailListeners) {
                mod.initializeBorrowRecordDetailListeners();
            }
        });

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#borrow-records-container', SEARCH_COLUMN_INDEXES.BORROW_RECORDS);
        }


    } catch (error) {
        showError("Something went wrong", "Failed to load borrowing records.");
    }
}

export async function loadReservationRecords(page = RESERVATION_RECORDS_FILTERS.page, scrollUp = true, isFiltered = false) {
    const container = document.getElementById('reservation-records-container');
    const countElement = document.getElementById('header-total-reservation-records');

    if (scrollUp) {
        window.scrollTo(0, 0);
    }

    countElement.textContent = '';

    let skeletonTimer = setTimeout(() => {
        showSkeleton(container, '#reservation-records-skeleton', '#reservation-records-real-table-body');
    }, 200); 
        
    try {
        RESERVATION_RECORDS_FILTERS.page = page;
        const searchInput = document.getElementById('reservation-records-search');
        const roleFilter = document.getElementById('reservation-records-role-filter');
        const statusFilter = document.getElementById('reservation-records-status-filter');
        const sortFilter = document.getElementById('reservation-records-sort-filter');
        const semesterFilter = document.getElementById('reservation-records-semester-filter');

        if (searchInput) RESERVATION_RECORDS_FILTERS.search = searchInput.value || '';
        if (roleFilter) RESERVATION_RECORDS_FILTERS.role = roleFilter.value || '';
        RESERVATION_RECORDS_FILTERS.status = statusFilter ? statusFilter.value : '';
        if (sortFilter) RESERVATION_RECORDS_FILTERS.sort = sortFilter.value || 'position_asc';
        if (semesterFilter) RESERVATION_RECORDS_FILTERS.semester = semesterFilter.value || '';

        const params = new URLSearchParams({
            page: RESERVATION_RECORDS_FILTERS.page,
            search: RESERVATION_RECORDS_FILTERS.search,
            role: RESERVATION_RECORDS_FILTERS.role,
            status: RESERVATION_RECORDS_FILTERS.status,
            sort: RESERVATION_RECORDS_FILTERS.sort,
            semester: RESERVATION_RECORDS_FILTERS.semester
        });

        const response = await fetch(`${LIBRARIAN_SECTION_ROUTES.RESERVATION_RECORDS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        container.innerHTML = data.html;

        console.log(data.count);
        countElement.textContent = data.count;

        clearTimeout(skeletonTimer);
        hideSkeleton(container, '#reservation-records-skeleton', '#reservation-records-real-table-body');

        // Re-attach reservation record detail listeners after table update (like staff side)
        import('../pages/librarian/reservationRecords/reservationRecordDetails.js').then(mod => {
            if (mod.initializeReservationRecordDetailListeners) {
                mod.initializeReservationRecordDetailListeners();
            }
        });

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#reservation-records-container', SEARCH_COLUMN_INDEXES.RESERVATION_RECORDS);
        }


    } catch (error) {
        showError("Something went wrong", "Failed to load reservation records.");
    }
}

export async function loadPenaltyRecords(page = PENALTY_RECORDS_FILTERS.page, scrollUp = true, isFiltered = false) {
    const container = document.getElementById('penalty-records-container');
    const countElement = document.getElementById('header-total-penalty-records');

    if (scrollUp) {
        window.scrollTo(0, 0);
    }

    countElement.textContent = '';

    let skeletonTimer = setTimeout(() => {
        showSkeleton(container, '#penalty-records-skeleton', '#penalty-records-real-table-body');
    }, 200); 
        
    try {
        PENALTY_RECORDS_FILTERS.page = page;
        const searchInput = document.getElementById('penalty-records-search');
        const roleFilter = document.getElementById('penalty-records-role-filter');
        const typeFilter = document.getElementById('penalty-records-type-filter');
        const statusFilter = document.getElementById('penalty-records-status-filter');
        const sortFilter = document.getElementById('penalty-records-sort-filter');
        const semesterFilter = document.getElementById('penalty-records-semester-filter');

        if (searchInput) PENALTY_RECORDS_FILTERS.search = searchInput.value || '';
        if (roleFilter) PENALTY_RECORDS_FILTERS.role = roleFilter.value || '';
        if (typeFilter) PENALTY_RECORDS_FILTERS.type = typeFilter.value || '';
        PENALTY_RECORDS_FILTERS.status = statusFilter ? statusFilter.value : '';
        PENALTY_RECORDS_FILTERS.sort = sortFilter ? sortFilter.value : '';
        if (semesterFilter) PENALTY_RECORDS_FILTERS.semester = semesterFilter.value || '';

        const params = new URLSearchParams({
            page: PENALTY_RECORDS_FILTERS.page,
            search: PENALTY_RECORDS_FILTERS.search,
            role: PENALTY_RECORDS_FILTERS.role,
            type: PENALTY_RECORDS_FILTERS.type,
            status: PENALTY_RECORDS_FILTERS.status,
            sort: PENALTY_RECORDS_FILTERS.sort,
            semester: PENALTY_RECORDS_FILTERS.semester
        });

        const response = await fetch(`${LIBRARIAN_SECTION_ROUTES.PENALTY_RECORDS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        container.innerHTML = data.html;

        countElement.textContent = data.count;

        clearTimeout(skeletonTimer);
        hideSkeleton(container, '#penalty-records-skeleton', '#penalty-records-real-table-body');

        // Re-attach penalty record detail listeners after table update
        import('../pages/librarian/penaltyRecords/penaltyRecordDetails.js').then(mod => {
            if (mod.initPenaltyRecordsListeners) {
                mod.initPenaltyRecordsListeners();
            }
        });

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#penalty-records-container', SEARCH_COLUMN_INDEXES.PENALTY_RECORDS);
        }

    } catch (error) {
        showError("Something went wrong", "Failed to load penalty records.");
    }
}