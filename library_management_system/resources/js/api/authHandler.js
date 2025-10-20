import { API_ROUTES, BASE_URL } from '../config.js';
import { showError, showSuccessWithRedirect, showLoader, hideLoader, confirmLogout, apiRequest, getJsonHeaders } from '../utils.js';
import { displayInputErrors } from '../helpers.js';

export async function loginHandler(username, password) {
  let result = await apiRequest(API_ROUTES.LOGIN, {
    method: "POST",
    headers: getJsonHeaders(),
    body: JSON.stringify({ username, password }),
  });

  if (result?.errorHandled) return;

  if (result.role === 'librarian') {
    window.location.href = BASE_URL + 'librarian/dashboard';
  } else {
    window.location.href = BASE_URL;
  }

}

export async function signupHandler(data) {
    showLoader();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    try {
        const response = await fetch(API_ROUTES.SIGNUP, {
            method: 'POST',
            headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.errors){
            displayInputErrors(result.errors, '#signup-form');
            hideLoader();
            return;
        }

        hideLoader();

        showSuccessWithRedirect(
            'Account Successfully Created', 
            'You have successfully registered!',
             '/login'  
        );
    } catch (error) {
        hideLoader();
        console.error('Error:', error);
        showError('Something went wrong', 'Please try again later.');
    }
}

export async function logoutHandler(){
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    try {
      const response = await fetch(API_ROUTES.LOGOUT, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'include' // Include cookies for session management
      });

      const result = await response.json();

      if (result.status === 'success') {
        window.location.href = BASE_URL + 'login';
      } else {
        showError('Logout Failed', 'Something went wrong while logging out. Please try again.');
      }
    } catch (error) {
      console.error('Error:', error);
      showError('Network Error', 'Unable to connect to the server. Please check your internet connection or try again later.');
    }
}