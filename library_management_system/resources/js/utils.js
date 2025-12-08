import { BUSINESS_RULE_VIOLATION, INVALID_INPUT } from "./config.js";
import { displayInputErrors } from "./helpers.js";
import { showError } from "./utils/alerts.js";

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

export function formatTime(time, locale = 'en-US') {
    // Ensure the input is a Date object, converting it if it's a string/timestamp.
    const t = (time instanceof Date) ? time : new Date(time);

    // Use toLocaleTimeString to format the time, showing hours and minutes.
    return t.toLocaleTimeString(locale, { hour: 'numeric', minute: '2-digit' });
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
            } else if (response.status === BUSINESS_RULE_VIOLATION){
                showError('Action Not Allowed', result?.message || 'The requested operation violates business rules.');
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


export function resetButton(button, defaultIcon) {
    // Remove disabled state
    button.classList.add('inline-flex','cursor-pointer', 'text-white', 'bg-secondary', 'hover:bg-secondary/90', 'shadow-sm', 'hover:shadow', 'bg-secondary-light', 'hover:bg-secondary-light/90');
    button.classList.remove(
        'cursor-not-allowed',
        'opacity-50',
        'bg-gray-300',
        'text-gray-500'
    );
    button.disabled = false;
    
    const img = button.querySelector('img');
    if (img) {
        img.src = defaultIcon;
    }

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

export function disableButton(button, tooltipMsg = '', icon) {
    button.disabled = true;
    button.classList.remove('cursor-pointer', 'text-white', 'bg-secondary', 'hover:bg-secondary/90', 'shadow-sm', 'hover:shadow', 'bg-secondary-light', 'hover:bg-secondary-light/90');
    button.classList.add(
        'cursor-not-allowed',
        'opacity-50',
        'bg-gray-300',
        'text-gray-500'
    );

    const img = button.querySelector('img');
    if (img) {
        img.src = icon;
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'relative inline-block group';

    button.parentNode.insertBefore(wrapper, button);
    wrapper.appendChild(button);

    const tooltip = document.createElement('div');
tooltip.className =
    'absolute bottom-full -left-10 mb-3 px-3 py-2 bg-gray-900 text-white ' +
    'text-xs rounded-lg shadow-lg text-center whitespace-normal break-words max-w-xl ' +
    'opacity-0 transition-opacity duration-200 pointer-events-none group-hover:opacity-100';

tooltip.innerHTML = `
    ${tooltipMsg}
    <div class="absolute top-full right-20 w-0 h-0 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-900"></div>
`;

wrapper.appendChild(tooltip);
}

export function replaceNodeWithClone(selector) {
    const el = document.querySelector(selector);
    if (el) {
        const clone = el.cloneNode(true);
        el.parentNode.replaceChild(clone, el);
        return clone;
    }
    return null;
}




