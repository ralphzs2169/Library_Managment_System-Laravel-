import { showError, highlightSearchMatches } from "../../utils.js";

document.addEventListener('click', async (e) => {
    if (e.target.matches('.pagination-btn') && !e.target.disabled) {
        const page = e.target.getAttribute('data-page');
        if (!page) return;

        e.preventDefault();
        loadMembers(page);
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

// Get current filter values
function getCurrentFilters() {
    const searchInput = document.querySelector('input[name="search"]');
    const statusFilter = document.querySelector('select[name="status"]');
    const roleFilter = document.querySelector('select[name="role"]');
    
    return {
        search: searchInput?.value || '',
        status: statusFilter?.value || '',
        role: roleFilter?.value || ''
    };
}

// Load members with filters
async function loadMembers(page = 1) {
    const container = document.querySelector('#members-table-container');
    if (!container) return;
    
    const filters = getCurrentFilters();
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
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            showError('Something went wrong', 'Unable to load members. Please try again.');
        }
    } catch (error) {
        showError('Something went wrong', 'Unable to load members. Please try again.');
    }
}

// Debounce helper
function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// Search input
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', debounce(() => {
        loadMembers(1);
    }, 500));
}

// Filter dropdowns
const statusFilter = document.querySelector('select[name="status"]');
const roleFilter = document.querySelector('select[name="role"]');

if (statusFilter) {
    statusFilter.addEventListener('change', () => loadMembers(1));
}

if (roleFilter) {
    roleFilter.addEventListener('change', () => loadMembers(1));
}
