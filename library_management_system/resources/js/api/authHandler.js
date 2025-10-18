import { API_ROUTES, BASE_URL } from '../config.js';
import { redirectTo, showError, showSuccessWithRedirect, showLoader, hideLoader, confirmLogout } from '../utils.js';
import { displayInputErrors } from '../helpers.js';

export async function loginHandler(username, password) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  
  try {
    const response = await fetch(API_ROUTES.LOGIN, {
      method: "POST",
       headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ username, password }),
    });

    const result = await response.json();
    
    if (result.errors) {
        displayInputErrors(result.errors);
        return;
    }


    if (result.status === 'success' && result.role === 'librarian') {
      window.location.href = BASE_URL + 'librarian/dashboard';
    } else {
       window.location.href = BASE_URL;
    }
  } catch (err) {
    showError("Something went wrong while logging in.");
    console.error(err);
    return null;
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
            displayInputErrors(result.errors);
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