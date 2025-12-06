import { showError } from "../utils/alerts.js";
import { BORROWER_FILTERS } from "../utils/tableFilters.js";
import { highlightSearchMatches } from "../tableControls.js";
import { SEARCH_COLUMN_INDEXES } from "../utils/tableFilters.js";
import { initStaffDashboardPagination } from "../tableControls.js";
import { TRANSACTION_ROUTES } from "../config.js";

export async function loadBorrowers(page = BORROWER_FILTERS.page, scrollUp = true) {
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

        const response = await fetch(`?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const html = await response.text();
        const container = document.getElementById('members-table-container');

        if (container) {
            container.innerHTML = html;

            initStaffDashboardPagination(loadBorrowers); 
        }

        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#members-table-container', SEARCH_COLUMN_INDEXES.BORROWERS_LIST);
        }

        if (scrollUp) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    } catch (error) {
        console.error('Error loading borrowers:', error);
    }
}


export async function fetchBorrowerDetails(userId) {
     try {

            const response = await fetch(TRANSACTION_ROUTES.FETCH_BORROWER(userId), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
           
            if (!response.ok) {
                showError('Something went wrong', 'Failed to load borrower details.');
                return;
           }
           
            return { borrower: result.borrower, actionPerformer: result.action_performer };
        } catch (error) {
            showError('Network Error', error + 'Unable to load borrower details. Please try again later.');
        }
}

