import { showError } from "../utils.js";
import { getCurrentFilters } from "../pages/staff/borrowersPagination.js";
import { highlightSearchMatches } from "../tableControls.js";
import { filters } from "../pages/staff/borrowersPagination.js";
// Load borrowers with filters

export async function loadBorrowers(page = filters.page, scrollUp = true) {
    const container = document.querySelector('#members-table-container');
    if (!container) return;
    
    Object.assign(filters, getCurrentFilters(), { page });
    const params = new URLSearchParams({
        page,
        ...filters
    });

    try {
        const response = await fetch(`?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (response.ok) {
            const html = await response.text();
            container.innerHTML = html;
            
            // Highlight search matches after content is loaded
            if (filters.search) {
                highlightSearchMatches(filters.search, '#members-table-container', [1, 2]);
            }
            
            if (scrollUp) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } else {
            showError('Something went wrong', 'Unable to load members. Please try again.');
        }
    } catch (error) {
        showError('Something went wrong', 'Unable to load members. Please try again.');
    }
}