import { showError, getJsonHeaders } from "../utils";

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('activity-logs-container');

    /**
     * Fetch and update activity logs
     * @param {Object} params - Query parameters for filtering/pagination
     */
    async function fetchLogs(params = {}) {
        try {
            const query = new URLSearchParams(params).toString();
            const url = `${window.location.pathname}?${query}`;

            const response = await fetch(url, {
                headers: {
                    ...getJsonHeaders(),
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            container.innerHTML = html;
            history.pushState(null, '', url);

        } catch (error) {
            console.error(error);
            showError('Something went wrong', 'Unable to load activity logs. Please try again.');
        }
    }

    // ======================
    // FILTERS + SORT
    // ======================
    const filters = document.querySelectorAll(
        '#filter-action, select[name="entity"], select[name="role"], input[name="date"], input[name="search"], #filter-sort'
    );

    filters.forEach(filter => {
        filter.addEventListener('change', () => {
            const params = {
                action: document.getElementById('filter-action')?.value || '',
                entity: document.querySelector('select[name="entity"]')?.value || '',
                role: document.querySelector('select[name="role"]')?.value || '',
                date: document.querySelector('input[name="date"]')?.value || '',
                search: document.querySelector('input[name="search"]')?.value || '',
                sort: document.querySelector('#filter-sort')?.value || '', // Sort added here
                page: 1, // Reset pagination when filter changes
            };
            fetchLogs(params);
        });
    });

    // ======================
    // PAGINATION (event delegation)
    // ======================
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link || !link.closest('.pagination')) return;

        e.preventDefault();

        const urlParams = new URL(link.href).searchParams;
        const params = {
            action: document.getElementById('filter-action')?.value || '',
            entity: document.querySelector('select[name="entity"]')?.value || '',
            role: document.querySelector('select[name="role"]')?.value || '',
            date: document.querySelector('input[name="date"]')?.value || '',
            search: document.querySelector('input[name="search"]')?.value || '',
            sort: document.querySelector('#filter-sort')?.value || '', // Include sort
            page: urlParams.get('page') || 1,
        };

        fetchLogs(params);
    });

    // ======================
    // RESET FILTERS
    // ======================
    const resetBtn = document.querySelector('button[type="button"]');
    resetBtn.addEventListener('click', () => {
        // Reset all filters and sort
        document.getElementById('filter-action').value = '';
        document.querySelector('select[name="entity"]').value = '';
        document.querySelector('select[name="role"]').value = '';
        document.querySelector('input[name="date"]').value = '';
        document.querySelector('input[name="search"]').value = '';
        document.querySelector('#filter-sort').value = '';

        history.pushState(null, '', window.location.pathname);
        fetchLogs({ page: 1 }); // reload first page with no filters
    });
});
