import { debounce } from "./utils.js";

export function initPagination(loadFunction) {

    document.addEventListener('click', (e) => {
        if (e.target.matches('.pagination-btn') && !e.target.disabled) {
            const page = e.target.getAttribute('data-page');
            if (!page) return;
            e.preventDefault();
            loadFunction(page);
        }
    });
}

export function initSearch(searchSelector, loadFunction, tableContainerSelector, columnIndexes = [1, 2]) {
    console.log('Initializing search handler');
    const searchInput = document.querySelector(searchSelector);
    console.log('Search input element:', searchInput);
    if (!searchInput) return;

    // Highlight on initial load
    document.addEventListener('DOMContentLoaded', () => {
        const term = searchInput.value.trim();
        if (term) highlightSearchMatches(term, tableContainerSelector, columnIndexes);
    });

    // Debounced input
    searchInput.addEventListener('input', debounce(() => loadFunction(1), 500));
}

export function initFilter(selector, loadFunction) {
    const filter = document.querySelector(selector);
    if (!filter) return;

    filter.addEventListener('change', () => loadFunction(1));
}

export function highlightSearchMatches(searchTerm, containerSelector = '#members-table-container', columnIndexes = [1, 2]) {
    if (!searchTerm || searchTerm.trim().length === 0) return;
    console.log(`Highlighting search term: "${searchTerm}" in container: "${containerSelector}" for columns:`, columnIndexes);
    const term = searchTerm.trim().toLowerCase();
    const container = document.querySelector(containerSelector);
    if (!container) return;
    
    const rows = container.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        // Skip rows with colspan (empty states)
        if (row.querySelector('[colspan]')) return;
        
        // Highlight specified columns
        columnIndexes.forEach(index => {
            const cell = row.cells[index];
            if (cell) highlightTextInCell(cell, term);
        });
    });
}


function highlightTextInCell(cell, searchTerm) {
    const walker = document.createTreeWalker(cell, NodeFilter.SHOW_TEXT);
    const nodesToReplace = [];
    let node;
    
    while (node = walker.nextNode()) {
        const text = node.nodeValue;
        const lowerText = text.toLowerCase();
        const index = lowerText.indexOf(searchTerm);
        
        if (index !== -1) {
            nodesToReplace.push({ 
                node, 
                index, 
                length: searchTerm.length 
            });
        }
    }
    
    // Replace text nodes with highlighted versions
    nodesToReplace.forEach(({ node, index, length }) => {
        const text = node.nodeValue;
        const before = text.substring(0, index);
        const match = text.substring(index, index + length);
        const after = text.substring(index + length);
        
        const fragment = document.createDocumentFragment();
        
        if (before) fragment.appendChild(document.createTextNode(before));
        
        const mark = document.createElement('mark');
        mark.className = 'bg-accent/30 px-0.5 rounded';
        mark.textContent = match;
        fragment.appendChild(mark);
        
        if (after) fragment.appendChild(document.createTextNode(after));
        
        node.parentNode.replaceChild(fragment, node);
    });
}
