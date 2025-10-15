
import { logoutHandler } from "./api/authHandler.js";

const Swal = window.Swal || window.Sweetalert2;

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
    reverseButtons: true, // makes Cancel appear on the left
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
