import { VALIDATION_ERROR } from "./config.js";
import { logoutHandler } from "./api/authHandler.js";
import Swal from 'sweetalert2';
import { displayInputErrors } from "./helpers.js";


export function getJsonHeaders() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    };
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
            if (response.status === VALIDATION_ERROR && result?.errors) {
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



export function showError(title, message) {
  Swal.fire({
    title: title,
    text: message,
    icon: "error",
    confirmButtonColor: '#CF3030'
  });
}

export function showSuccess(message) {
  Swal.fire({
    title: "Success",
    text: message,
    icon: "success",
    confirmButtonColor: '#00ADB5'
  });
}

export function showInfo(title, message) {
  Swal.fire({
    title: title,
    text: message,
    icon: "info",
    confirmButtonColor: '#00ADB5'
  });
}

export function showWarning(title, message) {
  Swal.fire({
    title: title,
    text: message,
    icon: "warning",
    confirmButtonColor: '#CF3030'
  });
}

export async function showSuccessWithRedirect(title, text, redirectUrl) {
  const result = await Swal.fire({
    title: title,
    text: text,
    icon: "success",
    confirmButtonText: "OK",
    confirmButtonColor: '#00ADB5',
  });
  
  if (result.isConfirmed) {
    redirectTo(redirectUrl);
  }
}

export async function confirmLogout() {
  const result = await Swal.fire({
    title: "Are you sure you want to log out?",
    text: "You will need to log in again to access your account.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, log out",
    cancelButtonText: "Cancel",
    confirmButtonColor: "#00ADB5",
    cancelButtonColor: "#3085d6",
    reverseButtons: true, // swaps button positions for better UX
  });

  if (result.isConfirmed) {
    logoutHandler();
  }
}

export async function showConfirmation(title, text, confirmText = "Yes, proceed") {
  const result = await Swal.fire({
    title: title,
    text: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: confirmText,
    cancelButtonText: "Cancel",
    confirmButtonColor: "#00ADB5",
    cancelButtonColor: "#6c757d",
    reverseButtons: true, 
  });

  return result.isConfirmed;
}

export async function showDangerConfirmation(title = 'Are you sure?', text = 'This action cannot be undone!') {
    const result = await Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonColor: '#00ADB5',
        cancelButtonColor: '#6b7280',
    });
    
    return result.isConfirmed;
}

export function redirectTo(path) {
  window.location.href = path;
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
    tooltip.className = 'tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 px-3 py-2 bg-white text-black text-xs rounded-lg shadow-lg border border-gray-300 pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap z-50';
    tooltip.innerHTML = `
        ${msg}
        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-white"></div>
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