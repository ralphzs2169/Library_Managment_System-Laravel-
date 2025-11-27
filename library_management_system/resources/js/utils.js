import { INVALID_INPUT } from "./config.js";
import { displayInputErrors } from "./helpers.js";


export function showSkeleton(container, skeletonSelector = '.skeleton', realContentSelector = '.real-content') {
    const skeleton = container.querySelector(skeletonSelector);
    const realContent = container.querySelector(realContentSelector);
    if (skeleton && realContent) {
        skeleton.classList.remove('hidden');
        realContent.classList.add('hidden');
    }
}

export function hideSkeleton(container, skeletonSelector = '.skeleton', realContentSelector = '.real-content') {
    const skeleton = container.querySelector(skeletonSelector);
    const realContent = container.querySelector(realContentSelector);
    if (skeleton && realContent) {
        skeleton.classList.add('hidden');
        realContent.classList.remove('hidden');
    }
}


export function getJsonHeaders() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    };
}

export function formatDate(date, locale = 'en-US') {
    const d = (date instanceof Date) ? date : new Date(date);
    return d.toLocaleDateString(locale, { month: 'short', day: 'numeric', year: 'numeric' });
}

export async function parseJsonSafely(response) {
  try {
    // Attempt to parse JSON
    return await response.json();
  } catch (error) {
    // Handle unexpected or invalid JSON
    showError('Something went wrong!', 'Unexpected server response.');
    return null;
  }
}

export async function apiRequest(url, options = {}) {
    try {
        const response = await fetch(url, options);
        const result = await parseJsonSafely(response);
        
        // Handle validation errors
        if (!response.ok) {
            if (response.status === INVALID_INPUT && result?.errors) {
                displayInputErrors(result.errors, options.form);
                return { errorHandled: true };
            }

            showError('Something went wrong!', result?.message || 'Please try again.');
            return { errorHandled: true };
        }

        return result;
    } catch (error) {
        showError('Something went wrong!', 'Unable to process request. Please try again.');
        console.error(error);
        return { errorHandled: true };
    }
}


export function showLoader() {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.classList.remove('hidden');
    }
}

// Hide page loader
export function hideLoader() {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.classList.add('hidden');
    }
}

// Auto-hide loader when page fully loads
window.addEventListener('load', function() {
    hideLoader();
});
// Handle browser back/forward button (pageshow event)
window.addEventListener('pageshow', function(event) {
    // event.persisted is true when page is loaded from cache
    if (event.persisted) {
        hideLoader();
    }
});

// Also hide loader before page unload (when navigating away)
window.addEventListener('beforeunload', function() {
    // Optional: you can show loader here if you want
    // showLoader();
});

// Hide loader when page becomes visible (handles tab switching)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        hideLoader();
    }
});

// Show loader on page navigation
document.addEventListener('DOMContentLoaded', function() {
    // Show loader on link clicks
    const links = document.querySelectorAll('a:not([target="_blank"])');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href !== '#' && !href.startsWith('javascript:')) {
                showLoader();
            }
        });
    });
});

export function formatLastNameFirst(firstname, lastname, middle_initial){
  return `${lastname}, ${firstname}${middle_initial ? ' ' + middle_initial + '.' : ''}`;
}

// Debounce helper
export function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}


export function resetButton(button) {
    // Remove disabled state
    console.log('Resetting button:', button);
    button.disabled = false;
    button.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
    button.classList.add('hover:bg-accent/80', 'bg-accent', 'cursor-pointer');
    button.style.backgroundColor = ''; // Clear inline styles
    
    // Remove any existing tooltip
    const existingTooltip = button.parentElement.querySelector('.tooltip');
    if (existingTooltip) {
        existingTooltip.remove();
    }
    
    // Unwrap button if it's in a wrapper
    const parent = button.parentElement;
    if (parent && parent.classList.contains('relative') && parent.classList.contains('inline-block')) {
        const grandparent = parent.parentElement;
        grandparent.insertBefore(button, parent);
        parent.remove();
    }
    
    // Remove any existing click handlers
    button.onclick = null;
}

export function disableButton(button, msg = '') {
    button.disabled = true;
    button.classList.remove('bg-accent', 'hover:bg-accent/80', 'cursor-pointer');
    button.classList.add('opacity-50', 'cursor-not-allowed');
    button.style.backgroundColor = '#9ca3af'; // Force gray background via inline style to override all classes
    
    // Wrap in relative container
    const wrapper = document.createElement('div');
    wrapper.className = 'relative inline-block';
    button.parentNode.insertBefore(wrapper, button);
    wrapper.appendChild(button);
    
    // Add tooltip
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg border border-gray-300 pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap z-50';
    tooltip.innerHTML = `
        ${msg}
        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
    `;
    
    wrapper.appendChild(tooltip);
    
    // Show/hide tooltip
    wrapper.addEventListener('mouseenter', () => {
        tooltip.classList.remove('opacity-0');
        tooltip.classList.add('opacity-100');
    });
    
    wrapper.addEventListener('mouseleave', () => {
        tooltip.classList.remove('opacity-100');
        tooltip.classList.add('opacity-0');
    });
}