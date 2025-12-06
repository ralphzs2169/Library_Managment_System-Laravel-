import { showError } from "../utils/alerts.js";
import { ACTIVE_BORROW_FILTERS, BORROWER_FILTERS, UNPAID_PENALTIES_FILTERS, QUEUE_RESERVATIONS_FILTERS } from "../utils/tableFilters.js";
import { highlightSearchMatches } from "../tableControls.js";
import { SEARCH_COLUMN_INDEXES } from "../utils/tableFilters.js";
import { initStaffDashboardPagination } from "../tableControls.js";
import { STAFF_DASHBOARD_ROUTES } from "../config.js";
import { showSkeleton, hideSkeleton } from "../utils.js";
import { activeBorrowsSkeletonLoader, unpaidPenaltiesSkeletonLoader, queueReservationsSkeletonLoader } from "../pages/staff/skeleton-loader/skeletonMainTabs.js";
import { initReservationRecordDetailListeners } from "../pages/manage-record-details/reservationRecordDetails.js";
import { initBorrowRecordDetailListeners } from "../pages/manage-record-details/borrowRecordDetails.js";
import { initPenaltyRecordsListeners } from "../pages/manage-record-details/penaltyRecordDetails.js";

export async function loadMembers(page = BORROWER_FILTERS.page, scrollUp = true, isFiltered = false) {
    const container = document.getElementById('members-table-container');
    const countElement = document.getElementById('header-total-members');

    if (scrollUp) {
        window.scrollTo(0, 100);
    }

    countElement.textContent = '';

    let skeletonTimer = setTimeout(() => {
        showSkeleton(container, '#members-skeleton-body', '#members-real-table-body');
    }, 200); 

    try {
        BORROWER_FILTERS.page = page;
        const searchInput = document.getElementById('borrowers-search');
        const roleFilter = document.getElementById('borrower-role-filter');
        const statusFilter = document.getElementById('borrower-status-filter');
        const sortFilter = document.getElementById('borrower-sort-filter');

        if (searchInput) BORROWER_FILTERS.search = searchInput.value || '';
        if (roleFilter) BORROWER_FILTERS.role = roleFilter.value || '';
        if (statusFilter) BORROWER_FILTERS.status = statusFilter.value || '';
        if (sortFilter) BORROWER_FILTERS.sort = sortFilter.value || '';

        const params = new URLSearchParams({
            page: BORROWER_FILTERS.page,
            search: BORROWER_FILTERS.search,
            role: BORROWER_FILTERS.role,
            status: BORROWER_FILTERS.status,
            sort: BORROWER_FILTERS.sort
        });

        const response = await fetch(`${STAFF_DASHBOARD_ROUTES.MEMBERS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        container.innerHTML = data.html;

        if (countElement) {
            countElement.textContent = data.count; 
        }

        clearTimeout(skeletonTimer);
        hideSkeleton(container, '#members-skeleton-body', '#members-real-table-body');
        initStaffDashboardPagination(loadMembers);
        

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#members-table-container', SEARCH_COLUMN_INDEXES.BORROWERS_LIST);
        }


    } catch (error) {
        showError("Something went wrong", "Failed to load members.");
    }
}

// AJAX handler for staff dashboard tables

export async function loadActiveBorrows(page = ACTIVE_BORROW_FILTERS.page, scrollUp = true) {
    const container = document.getElementById('active-borrows-table-container');
    const countElement = document.getElementById('header-total-active-borrows');
    
    if (scrollUp) {
        window.scrollTo(0, 100);
    }

    countElement.textContent = '';
    
    let skeletonTimer = setTimeout(() => {
        showSkeleton(container, '#active-borrows-skeleton-body', '#active-borrows-real-table-body');
    }, 200);
    container.innerHTML = activeBorrowsSkeletonLoader();
  

    try {
        ACTIVE_BORROW_FILTERS.page = page;
        const searchInput = document.getElementById('active-borrows-search');
        const roleFilter = document.getElementById('active-borrows-role-filter');
        const statusFilter = document.getElementById('active-borrows-status-filter');
        const sortFilter = document.getElementById('active-borrows-sort-filter');

        ACTIVE_BORROW_FILTERS.search = searchInput ? searchInput.value : '';
        ACTIVE_BORROW_FILTERS.role = roleFilter ? roleFilter.value : '';
        ACTIVE_BORROW_FILTERS.status = statusFilter ? statusFilter.value : '';
        ACTIVE_BORROW_FILTERS.sort = sortFilter ? sortFilter.value : '';

        const params = new URLSearchParams({
            page: ACTIVE_BORROW_FILTERS.page,
            search: ACTIVE_BORROW_FILTERS.search,
            role: ACTIVE_BORROW_FILTERS.role,
            status: ACTIVE_BORROW_FILTERS.status,
            sort: ACTIVE_BORROW_FILTERS.sort
        });

        const response = await fetch(`${STAFF_DASHBOARD_ROUTES.ACTIVE_BORROWS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        container.innerHTML = data.html;

        if (countElement) {
            countElement.textContent = data.count; 
        }
    
        // Hide skeleton and show real table after loading
        clearTimeout(skeletonTimer);
        hideSkeleton(container, '#active-borrows-skeleton-body', '#active-borrows-real-table-body');
        initStaffDashboardPagination(loadActiveBorrows);
        initBorrowRecordDetailListeners();
        

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#active-borrows-table-container', SEARCH_COLUMN_INDEXES.ACTIVE_BORROWS);
        }

    } catch (error) {
        showError("Something went wrong", "Failed to load queue reservations.");
    }
}

export async function loadUnpaidPenalties(page = UNPAID_PENALTIES_FILTERS.page, scrollUp = true) {
    const container = document.getElementById('unpaid-penalties-table-container');
    const countElement = document.getElementById('header-total-unpaid-penalties');

    if (scrollUp) {
        window.scrollTo({ top: 0 });
    }

    countElement.textContent = '';
   
    let skeletonTimer = setTimeout(() => {
        showSkeleton(container, '#unpaid-penalties-skeleton-body', '#unpaid-penalties-real-table-body');
    }, 200);

    container.innerHTML = unpaidPenaltiesSkeletonLoader();
    try {
        UNPAID_PENALTIES_FILTERS.page = page;
        const container = document.getElementById('unpaid-penalties-table-container');
        if (!container) return;

        // Get filter values from inputs
        const searchInput = document.getElementById('unpaid-penalties-search');
        const roleFilter = document.getElementById('unpaid-penalties-role-filter');
        const typeFilter = document.getElementById('unpaid-penalties-type-filter');
        const statusFilter = document.getElementById('unpaid-penalties-status-filter');
        const sortFilter = document.getElementById('unpaid-penalties-sort-filter');

        // Update UNPAID_PENALTIES_FILTERS with current values
        UNPAID_PENALTIES_FILTERS.search = searchInput ? searchInput.value : '';
        UNPAID_PENALTIES_FILTERS.role = roleFilter ? roleFilter.value : '';
        UNPAID_PENALTIES_FILTERS.type = typeFilter ? typeFilter.value : '';
        UNPAID_PENALTIES_FILTERS.status = statusFilter ? statusFilter.value : '';
        UNPAID_PENALTIES_FILTERS.sort = sortFilter ? sortFilter.value : '';

        const params = new URLSearchParams({
            page: UNPAID_PENALTIES_FILTERS.page,
            search: UNPAID_PENALTIES_FILTERS.search,
            role: UNPAID_PENALTIES_FILTERS.role,
            type: UNPAID_PENALTIES_FILTERS.type,
            status: UNPAID_PENALTIES_FILTERS.status,
            sort: UNPAID_PENALTIES_FILTERS.sort
        });

        const response = await fetch(`${STAFF_DASHBOARD_ROUTES.UNPAID_PENALTIES}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        container.innerHTML = data.html;

        if(countElement){
            countElement.textContent = data.count; 
        }

        clearTimeout(skeletonTimer);
        hideSkeleton(container, '#unpaid-penalties-skeleton-body', '#unpaid-penalties-real-table-body');
        initStaffDashboardPagination(loadUnpaidPenalties);
        initPenaltyRecordsListeners();

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#unpaid-penalties-table-container', SEARCH_COLUMN_INDEXES.UNPAID_PENALTIES);
        }

    } catch (error) {
       showError("Something went wrong", "Failed to load unpaid penalties.");
    }
}

export async function loadQueueReservations(page = QUEUE_RESERVATIONS_FILTERS.page, scrollUp = true) {
    const container = document.getElementById('queue-reservations-table-container');
    const countElement = document.getElementById('header-total-queue-reservations');
    if (scrollUp) {
        window.scrollTo({ top: 0 });
    }

    countElement.textContent = '';
   
    let skeletonTimer = setTimeout(() => {
        showSkeleton(container, '#queue-reservations-skeleton-body', '#queue-reservations-real-table-body');
    }, 200);

    container.innerHTML = queueReservationsSkeletonLoader();
    try {
        QUEUE_RESERVATIONS_FILTERS.page = page;
        const container = document.getElementById('queue-reservations-table-container');
        if (!container) return;

        // Get filter values from inputs
        const searchInput = document.getElementById('queue-reservations-search');
        const roleFilter = document.getElementById('queue-reservations-role-filter');
        const statusFilter = document.getElementById('queue-reservations-status-filter');
        const sortFilter = document.getElementById('queue-reservations-sort-filter');

        // Update QUEUE_RESERVATIONS_FILTERS with current values
        QUEUE_RESERVATIONS_FILTERS.search = searchInput ? searchInput.value : '';
        QUEUE_RESERVATIONS_FILTERS.role = roleFilter ? roleFilter.value : '';
        QUEUE_RESERVATIONS_FILTERS.status = statusFilter ? statusFilter.value : '';
        QUEUE_RESERVATIONS_FILTERS.sort = sortFilter ? sortFilter.value : '';

        const params = new URLSearchParams({
            page: QUEUE_RESERVATIONS_FILTERS.page,
            search: QUEUE_RESERVATIONS_FILTERS.search,
            role: QUEUE_RESERVATIONS_FILTERS.role,
            status: QUEUE_RESERVATIONS_FILTERS.status,
            sort: QUEUE_RESERVATIONS_FILTERS.sort
        });

        const response = await fetch(`${STAFF_DASHBOARD_ROUTES.QUEUE_RESERVATIONS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        container.innerHTML = data.html;

        if(countElement){
            countElement.textContent = data.count; 
        }

        clearTimeout(skeletonTimer);
        hideSkeleton(container, '#queue-reservations-skeleton-body', '#queue-reservations-real-table-body');
        // Re-attach pagination listeners for active borrows
        initStaffDashboardPagination(loadQueueReservations);
        initReservationRecordDetailListeners();
        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#queue-reservations-table-container', SEARCH_COLUMN_INDEXES.QUEUE_RESERVATIONS);
        }
    } catch (error) {
       showError("Something went wrong", "Failed to load queue reservations.");
    }
}

export function reloadStaffDashboardData() {
    loadMembers(undefined, false);
    loadActiveBorrows(undefined, false);
    loadUnpaidPenalties(undefined, false);
    loadQueueReservations(undefined, false);
}