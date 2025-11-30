import { showError } from "../utils/alerts.js";
import { ACTIVE_BORROW_FILTERS, BORROWER_FILTERS, UNPAID_PENALTIES_FILTERS, QUEUE_RESERVATIONS_FILTERS } from "../utils/tableFilters.js";
import { highlightSearchMatches } from "../tableControls.js";
import { SEARCH_COLUMN_INDEXES } from "../utils/tableFilters.js";
import { initStaffDashboardPagination } from "../tableControls.js";
import { STAFF_DASHBOARD_ROUTES } from "../config.js";
import { showSkeleton, hideSkeleton } from "../utils.js";

export async function loadMembers(page = BORROWER_FILTERS.page, scrollUp = true) {
    const container = document.getElementById('members-table-container');

    if (scrollUp) {
        window.scrollTo({ top: 0});
    }
    showSkeleton(container, '#members-skeleton-body', '#members-real-table-body');
    try {
        BORROWER_FILTERS.page = page;
        const searchInput = document.getElementById('borrowers-search');
        const roleFilter = document.getElementById('borrower-role-filter');
        const statusFilter = document.getElementById('borrower-status-filter');

        if (searchInput) BORROWER_FILTERS.search = searchInput.value || '';
        if (roleFilter) BORROWER_FILTERS.role = roleFilter.value || '';
        if (statusFilter) BORROWER_FILTERS.status = statusFilter.value || '';

        const params = new URLSearchParams({
            page: BORROWER_FILTERS.page,
            search: BORROWER_FILTERS.search,
            role: BORROWER_FILTERS.role,
            status: BORROWER_FILTERS.status
        });

        const response = await fetch(`${STAFF_DASHBOARD_ROUTES.MEMBERS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const html = await response.text();

        if (container) {
            container.innerHTML = html;
            // Hide skeleton and show real table after loading
            hideSkeleton(container, '#members-skeleton-body', '#members-real-table-body');
            initStaffDashboardPagination(loadMembers);
        }

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
    if (scrollUp) {
        window.scrollTo({ top: 0 });
    }
    // Show skeleton before AJAX
    if (container) {
        // Always render the skeleton table structure before AJAX
        container.innerHTML = `
            <table class="w-full text-sm rounded-lg">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">No.</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Book</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Copy No.</th>
                        <th class="py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"></th>
                        <th class="py-3 pl-2 pr-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrower</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Borrowed At</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Due Date</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap w-20">Actions</th>
                    </tr>
                </thead>
                <tbody id="active-borrows-skeleton-body" class="bg-white divide-y divide-gray-100">
                    ${Array.from({ length: 8 }).map(() => `
                        <tr>
                            <td class="py-3 px-4"><div class="h-5 w-8 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-3 px-4 flex items-center gap-3">
                                <div class="w-12 h-16 rounded-md bg-gray-200 animate-pulse"></div>
                                <div class="flex flex-col gap-1">
                                    <div class="h-5 w-32 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </td>
                            <td class="py-3 px-4"><div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-2 text-center"><div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse mx-auto"></div></td>
                            <td class="py-3 pl-2 pr-4">
                                <div class="h-5 w-32 bg-gray-200 rounded animate-pulse mb-2"></div>
                                <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                            </td>
                            <td class="py-3 px-4"><div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-3 px-4"><div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-3 px-4"><div class="h-5 w-16 bg-gray-200 rounded animate-pulse"></div></td>
                            <td class="py-3 px-4"><div class="h-8 w-24 bg-gray-200 rounded-lg animate-pulse mx-auto"></div></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    }
    try {
        ACTIVE_BORROW_FILTERS.page = page;
        const searchInput = document.getElementById('active-borrows-search');
        const roleFilter = document.getElementById('active-borrows-role-filter');
        const statusFilter = document.getElementById('active-borrows-status-filter');

        ACTIVE_BORROW_FILTERS.search = searchInput ? searchInput.value : '';
        ACTIVE_BORROW_FILTERS.role = roleFilter ? roleFilter.value : '';
        ACTIVE_BORROW_FILTERS.status = statusFilter ? statusFilter.value : '';

        const params = new URLSearchParams({
            page: ACTIVE_BORROW_FILTERS.page,
            search: ACTIVE_BORROW_FILTERS.search,
            role: ACTIVE_BORROW_FILTERS.role,
            status: ACTIVE_BORROW_FILTERS.status
        });

        const response = await fetch(`${STAFF_DASHBOARD_ROUTES.ACTIVE_BORROWS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const html = await response.text();
        if (container) {
            container.innerHTML = html;
            // Hide skeleton and show real table after loading
            hideSkeleton(container, '#active-borrows-skeleton-body', '#active-borrows-real-table-body');
            initStaffDashboardPagination(loadActiveBorrows);
        }

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#active-borrows-table-container', SEARCH_COLUMN_INDEXES.ACTIVE_BORROWS);
        }

    } catch (error) {
        showError("Something went wrong", "Failed to load queue reservations.");
    }
}

export async function loadUnpaidPenalties(page = UNPAID_PENALTIES_FILTERS.page, scrollUp = true) {
    try {
        UNPAID_PENALTIES_FILTERS.page = page;
        const container = document.getElementById('unpaid-penalties-table-container');
        if (!container) return;

        // Get filter values from inputs
        const searchInput = document.getElementById('unpaid-penalties-search');
        const roleFilter = document.getElementById('unpaid-penalties-role-filter');
        const statusFilter = document.getElementById('unpaid-penalties-status-filter');

        // Update UNPAID_PENALTIES_FILTERS with current values
        UNPAID_PENALTIES_FILTERS.search = searchInput ? searchInput.value : '';
        UNPAID_PENALTIES_FILTERS.role = roleFilter ? roleFilter.value : '';
        UNPAID_PENALTIES_FILTERS.status = statusFilter ? statusFilter.value : '';

        const params = new URLSearchParams({
            page: UNPAID_PENALTIES_FILTERS.page,
            search: UNPAID_PENALTIES_FILTERS.search,
            role: UNPAID_PENALTIES_FILTERS.role,
            status: UNPAID_PENALTIES_FILTERS.status
        });

        const response = await fetch(`${STAFF_DASHBOARD_ROUTES.UNPAID_PENALTIES}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const html = await response.text();
        container.innerHTML = html;

        // Re-attach pagination listeners for active borrows
        initStaffDashboardPagination(loadActiveBorrows);

        if (scrollUp) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    } catch (error) {
       showError("Something went wrong", "Failed to load unpaid penalties.");
    }
}

export async function loadQueueReservations(page = QUEUE_RESERVATIONS_FILTERS.page, scrollUp = true) {
    try {
        QUEUE_RESERVATIONS_FILTERS.page = page;
        const container = document.getElementById('queue-reservations-table-container');
        if (!container) return;

        // Get filter values from inputs
        const searchInput = document.getElementById('queue-reservations-search');
        const roleFilter = document.getElementById('queue-reservations-role-filter');
        const statusFilter = document.getElementById('queue-reservations-status-filter');

        // Update QUEUE_RESERVATIONS_FILTERS with current values
        QUEUE_RESERVATIONS_FILTERS.search = searchInput ? searchInput.value : '';
        QUEUE_RESERVATIONS_FILTERS.role = roleFilter ? roleFilter.value : '';
        QUEUE_RESERVATIONS_FILTERS.status = statusFilter ? statusFilter.value : '';

        const params = new URLSearchParams({
            page: QUEUE_RESERVATIONS_FILTERS.page,
            search: QUEUE_RESERVATIONS_FILTERS.search,
            role: QUEUE_RESERVATIONS_FILTERS.role,
            status: QUEUE_RESERVATIONS_FILTERS.status
        });

        const response = await fetch(`${STAFF_DASHBOARD_ROUTES.QUEUE_RESERVATIONS}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const html = await response.text();
        container.innerHTML = html;

        // Re-attach pagination listeners for active borrows
        initStaffDashboardPagination(loadQueueReservations);

        if (scrollUp) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    } catch (error) {
       showError("Something went wrong", "Failed to load queue reservations.");
    }
}
