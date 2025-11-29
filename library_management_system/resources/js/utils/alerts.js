import { logoutHandler } from "../ajax/authHandler.js";
import Swal from 'sweetalert2';
import Toaster from "toaster-ui";

const toaster = new Toaster();

export function showToast(message, type = 'info') {
  // Define background color per type
  const bgColor = type === 'success' ? '#4BB543' :
                  type === 'error'   ? '#c82f2f' :
                  type === 'warning' ? '#FFCC00' :
                  '#333333';

  // Define icon with shake animation for warning
  const icon = type === 'success' ? '<img src="/build/assets/icons/success-toast.svg" class="mr-2 icon-size-md"/>' :
               type === 'error'   ? '<i class="fa-solid fa-xmark mr-2"></i>' :
               type === 'warning' ? '<i class="fa-solid fa-triangle-exclamation mr-2 shake-icon"></i>' :
               '<i class="fa-solid fa-info mr-2"></i>';

  const options = {
    allowHtml: true,
    duration: 3000,
    styles: {
      backgroundColor: bgColor,
      color: '#ffffff',
      borderRadius: '8px',
      display: 'flex',
      alignItems: 'center',
    },
  };

  toaster.addToast(`${icon}${message}`, type, options);
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