import { debounce } from "../../utils";

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('book-search');
    const sortSelect = document.getElementById('book-sort');
    const yearFrom = document.getElementById('year-from');
    const yearTo = document.getElementById('year-to');
    const languageCheckboxes = document.querySelectorAll('.language-checkbox');
    const availabilityCheckboxes = document.querySelectorAll('.availability-checkbox');
    const yearPresetBtns = document.querySelectorAll('.year-preset-btn');
    const applyFiltersBtn = document.getElementById('apply-filters-btn');
    const clearFiltersBtn = document.getElementById('clear-filters-btn');
    const booksContainer = document.getElementById('books-container');
    const loadMoreContainer = document.getElementById('load-more-container');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadMoreText = document.getElementById('load-more-text');
    const loadMoreSpinner = document.getElementById('load-more-spinner');
    const currentCountSpan = document.getElementById('current-count');
    const totalCountSpan = document.getElementById('total-count');
    const activeFiltersDiv = document.getElementById('active-filters');
    const filterTagsContainer = document.getElementById('filter-tags');

    let currentPage = 1;
    let hasMorePages = true;
    let isLoading = false;

    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    // Fix: If page param exists in URL on load, remove it so we start fresh visually (since controller forces page 1)
    if (urlParams.has('page')) {
        urlParams.delete('page');
        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, '', newUrl);
    }
    
    // Set initial values from URL
    if (urlParams.has('search')) searchInput.value = urlParams.get('search');
    if (urlParams.has('sort')) sortSelect.value = urlParams.get('sort');
    if (urlParams.has('year_from')) yearFrom.value = urlParams.get('year_from');
    if (urlParams.has('year_to')) yearTo.value = urlParams.get('year_to');
    
    // Set checkboxes from URL
    if (urlParams.has('languages')) {
        const languages = urlParams.get('languages').split(',');
        languageCheckboxes.forEach(cb => {
            if (languages.includes(cb.value)) cb.checked = true;
        });
    }
    
    if (urlParams.has('availability')) {
        const statuses = urlParams.get('availability').split(',');
        availabilityCheckboxes.forEach(cb => {
            if (statuses.includes(cb.value)) cb.checked = true;
        });
    }

    // Year preset buttons
    yearPresetBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const years = this.dataset.years;
            const currentYear = new Date().getFullYear();
            
            if (years === 'all') {
                yearFrom.value = '';
                yearTo.value = currentYear - 50;
            } else {
                yearFrom.value = currentYear - parseInt(years);
                yearTo.value = currentYear;
            }
            
            updateActiveFilters();
        });
    });

    // Debounced search
    const handleSearch = debounce(() => {
        resetAndLoadBooks();
    }, 500);

    searchInput.addEventListener('input', handleSearch);

    // Apply filters button
    applyFiltersBtn.addEventListener('click', () => {
        resetAndLoadBooks();
    });

    // Clear filters button
    clearFiltersBtn.addEventListener('click', () => {
        searchInput.value = '';
        sortSelect.value = 'title_asc';
        yearFrom.value = '';
        yearTo.value = '';
        
        languageCheckboxes.forEach(cb => cb.checked = false);
        availabilityCheckboxes.forEach(cb => cb.checked = false);
        
        activeFiltersDiv.classList.add('hidden');
        filterTagsContainer.innerHTML = '';
        
        window.history.pushState({}, '', window.location.pathname);
        resetAndLoadBooks();
    });

    // Load More button
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            if (!isLoading && hasMorePages) {
                currentPage++;
                loadBooks(false);
            }
        });
    }

    // Update active filters display
    function updateActiveFilters() {
        const filters = [];
        
        if (yearFrom.value || yearTo.value) {
            const from = yearFrom.value || '...';
            const to = yearTo.value || '...';
            filters.push({ label: `Year: ${from} - ${to}`, type: 'year' });
        }
        
        languageCheckboxes.forEach(cb => {
            if (cb.checked) filters.push({ label: cb.value, type: 'language' });
        });
        
        availabilityCheckboxes.forEach(cb => {
            if (cb.checked) filters.push({ label: cb.value, type: 'availability' });
        });
        
        if (filters.length > 0) {
            activeFiltersDiv.classList.remove('hidden');
            filterTagsContainer.innerHTML = filters.map(f => `
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-accent/10 text-accent rounded-full text-xs font-medium">
                    ${f.label}
                    <button class="hover:text-accent/80" onclick="this.parentElement.remove()">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            `).join('');
        } else {
            activeFiltersDiv.classList.add('hidden');
        }
    }

    // Watch for filter changes
    [yearFrom, yearTo, ...languageCheckboxes, ...availabilityCheckboxes].forEach(el => {
        el.addEventListener('change', updateActiveFilters);
    });

    // Initial filter display
    updateActiveFilters();

    function resetAndLoadBooks() {
        currentPage = 1;
        hasMorePages = true;
        // Don't clear immediately to avoid flash, loadBooks will handle replacement for page 1
        loadBooks(true);
    }

    async function loadBooks(isReset = false) {
        if (isLoading) return;

        isLoading = true;
        if (loadMoreBtn) {
            loadMoreBtn.disabled = true;
            loadMoreText.textContent = 'Loading...';
            loadMoreSpinner.classList.remove('hidden');
        }

        // Get selected languages
        const selectedLanguages = Array.from(languageCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value)
            .join(',');
        
        // Get selected availability statuses
        const selectedAvailability = Array.from(availabilityCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value)
            .join(',');

        const params = new URLSearchParams({
            search: searchInput.value,
            sort: sortSelect.value,
            year_from: yearFrom.value,
            year_to: yearTo.value,
            languages: selectedLanguages,
            availability: selectedAvailability,
            page: currentPage
        });

        // Get genre/category from URL if present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('genre')) params.set('genre', urlParams.get('genre'));
        if (urlParams.has('category')) params.set('category', urlParams.get('category'));

        // Update URL without reload
        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({}, '', newUrl);

        try {
            const response = await fetch(`/users/book-catalog?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to load books');

            const data = await response.json();
            
            if (isReset) {
                booksContainer.innerHTML = data.html;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // Create a temporary container to hold the new books
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                
                // Get the grid container or create one
                let gridContainer = booksContainer.querySelector('.grid');
                
                if (!gridContainer) {
                    // If no grid exists (e.g. empty state previously), replace content
                    booksContainer.innerHTML = data.html;
                    gridContainer = booksContainer.querySelector('.grid');
                } else {
                    // Append new book cards if grid exists
                    const newGrid = tempDiv.querySelector('.grid');
                    if (newGrid) {
                        const newCards = newGrid.children;
                        Array.from(newCards).forEach(card => {
                            gridContainer.appendChild(card);
                            // Add fade-in animation
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            setTimeout(() => {
                                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 50);
                        });
                    } else {
                        // Fallback if partial structure changes
                        booksContainer.insertAdjacentHTML('beforeend', data.html);
                    }
                }
            }

            // Update state
            hasMorePages = data.hasMore;
            
            // Update counts
            const gridContainer = booksContainer.querySelector('.grid');
            const currentCount = gridContainer ? gridContainer.children.length : 0;
            if (currentCountSpan) currentCountSpan.textContent = currentCount;
            if (totalCountSpan) totalCountSpan.textContent = data.total;

            // Hide/show load more button
            if (loadMoreContainer) {
                if (!hasMorePages) {
                    loadMoreContainer.classList.add('hidden');
                } else {
                    loadMoreContainer.classList.remove('hidden');
                }
            }

        } catch (error) {
            console.error('Error loading books:', error);
            if (currentPage === 1) {
                booksContainer.innerHTML = `
                    <div class="col-span-full bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                        <p class="text-red-800">Failed to load books. Please try again.</p>
                    </div>
                `;
            }
        } finally {
            isLoading = false;
            if (loadMoreBtn) {
                loadMoreBtn.disabled = false;
                loadMoreText.textContent = 'Load More Books';
                loadMoreSpinner.classList.add('hidden');
            }
        }
    }
});
