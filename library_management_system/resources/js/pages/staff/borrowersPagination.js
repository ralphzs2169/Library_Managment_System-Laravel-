import { highlightSearchMatches } from "../../utils.js";
import { debounce } from "../../utils.js";
import { loadBorrowers } from "../../api/usersHandler.js";

document.addEventListener('click', async (e) => {
    if (e.target.matches('.pagination-btn') && !e.target.disabled) {
        const page = e.target.getAttribute('data-page');
        if (!page) return;

        e.preventDefault();
        loadBorrowers(page);
    }
});

// Highlight search matches on initial page load
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const searchTerm = searchInput?.value?.trim();
    
    if (searchTerm) {
        highlightSearchMatches(searchTerm, '#members-table-container', [1, 2]);
    }
});

// Search input
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', debounce(() => {
        loadBorrowers(1);
    }, 500));
}

// Filter dropdowns
const statusFilter = document.querySelector('select[name="status"]');
const roleFilter = document.querySelector('select[name="role"]');

if (statusFilter) {
    statusFilter.addEventListener('change', () => loadBorrowers(1));
}

if (roleFilter) {
    roleFilter.addEventListener('change', () => loadBorrowers(1));
}

// Get current filter values
export function getCurrentFilters() {
    const searchInput = document.querySelector('input[name="search"]');
    const statusFilter = document.querySelector('select[name="status"]');
    const roleFilter = document.querySelector('select[name="role"]');
    
    return {
        search: searchInput?.value || '',
        status: statusFilter?.value || '',
        role: roleFilter?.value || ''
    };
}
