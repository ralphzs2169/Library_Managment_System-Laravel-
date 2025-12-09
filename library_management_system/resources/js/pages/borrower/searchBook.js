import { highlightSearchMatches } from "../../tableControls";
import { debounce } from "../../utils";

document.addEventListener('DOMContentLoaded', function() {
            // --- Search Logic ---
            const searchInput = document.getElementById('public-book-search');
            const resultsContainer = document.getElementById('search-results-container');
            const resultsList = document.getElementById('search-results-list');
            const noResults = document.getElementById('search-no-results');
            const loadingIndicator = document.getElementById('search-loading');
            
            const handleSearch = debounce((query) => {
                // Check if input is still valid before fetching (handles case where input was cleared while debounce was pending)
                if (searchInput.value.trim().length < 2) {
                    loadingIndicator.classList.add('hidden');
                    return;
                }
                fetchBooks(query);
            }, 300);

            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();

                if (query.length < 2) {
                    resultsContainer.classList.add('hidden');
                    loadingIndicator.classList.add('hidden');
                    return;
                }

                loadingIndicator.classList.remove('hidden');
                handleSearch(query);
            });

            // Close search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.classList.add('hidden');
                }
            });

            // Re-open search results if input has value and is focused
            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 2 && resultsList.children.length > 0) {
                    resultsContainer.classList.remove('hidden');
                }
            });

            async function fetchBooks(query) {
                // Double check input value to handle race conditions
                if (searchInput.value.trim().length < 2) {
                    loadingIndicator.classList.add('hidden');
                    return;
                }

                try {
                    // Use current URL with query param, controller handles AJAX
                    const response = await fetch(`/?query=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                            , 'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();
                    
                    // Final check before rendering
                    if (searchInput.value.trim().length < 2) {
                        loadingIndicator.classList.add('hidden');
                        return;
                    }

                    renderResults(data.data);

                } catch (error) {
                    console.error('Error fetching books:', error);
                } finally {
                    loadingIndicator.classList.add('hidden');
                }
            }

            function renderResults(books) {
                resultsList.innerHTML = '';

                if (books.length === 0) {
                    noResults.classList.remove('hidden');
                    resultsList.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    resultsList.classList.remove('hidden');

                    books.forEach(book => {
                        const item = document.createElement('div');
                        item.className = 'flex items-center gap-4 p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors';

                        // Construct image URL
                        const imageUrl = book.cover_image ?
                            `/storage/${book.cover_image}` :
                            null;

                        const imageHtml = imageUrl ?
                            `<img src="${imageUrl}" alt="${book.title}" class="w-12 h-16 object-cover rounded shadow-sm">` :
                            `<div class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500 text-center p-1">No Cover</div>`;

                        item.innerHTML = `
                            ${imageHtml}
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900 truncate">${book.title}</h4>
                                <p class="text-xs text-gray-600 truncate">by ${book.author ? book.author.firstname + ' ' + book.author.lastname : 'Unknown Author'}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">${book.genre?.category?.name ?? 'N/A'}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">${book.publication_year ?? 'N/A'}</span>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        `;

                        // Optional: Add click handler to navigate to book details if you have a route
                        // item.addEventListener('click', () => window.location.href = `/books/${book.id}`);

                        resultsList.appendChild(item);
                    });

                    // Highlight matches
                    const query = searchInput.value.trim();
                    if (query) {
                        highlightSearchMatches(query, '#search-results-list', 'h4, p');
                    }
                }

                resultsContainer.classList.remove('hidden');
            }
        });
