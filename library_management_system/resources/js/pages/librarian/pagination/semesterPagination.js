import { highlightSearchMatches, debounce } from "../../../utils.js";
import { loadSemesters } from "../../../api/semesterHandler.js";

// Pagination click handler
document.addEventListener('click', async (e) => {
    if (e.target.matches('.pagination-btn') && !e.target.disabled) {
        const page = e.target.getAttribute('data-page');
        if (!page) return;

        e.preventDefault();
        loadSemesters(page);
    }
});

// Highlight search matches on initial page load
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('#semester-search');
    const searchTerm = searchInput?.value?.trim();
    
    if (searchTerm) {
        highlightSearchMatches(searchTerm, '#semesters-table-container', [1]);
    }
});

// Search input
const searchInput = document.querySelector('#semester-search');
if (searchInput) {
    searchInput.addEventListener('input', debounce(() => {
        loadSemesters(1);
    }, 500));
}

// Sort dropdown
const sortSelect = document.querySelector('#semester-sort');
if (sortSelect) {
    sortSelect.addEventListener('change', () => loadSemesters(1));
}

// Status filter dropdown
const statusFilter = document.querySelector('#semester-status-filter');
if (statusFilter) {
    statusFilter.addEventListener('change', () => loadSemesters(1));
}
