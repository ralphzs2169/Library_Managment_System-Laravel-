import { API_ROUTES, BASE_URL } from '../config.js';
import { redirectTo, showError, showSuccessWithRedirect, showLoader, hideLoader, confirmLogout } from '../utils.js';
import { displayInputErrors } from '../helpers.js';

export async function loginHandler(username, password) {
  try {
    const response = await fetch(API_ROUTES.LOGIN, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, password }),
    });

    const result = await response.json();

    if (result.errors) {
        displayInputErrors(result.errors);
        return;
    }
    
     window.location.href = BASE_URL + 'public/index.php';
  } catch (err) {
    showError("Something went wrong while logging in.");
    console.error(err);
    return null;
  }
}

export async function signupHandler(data) {
    console.log(API_ROUTES.SIGNUP);
    // showLoader();
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
             BASE_URL + 'views/auth/login.php'  
        );

    } catch (error) {
        hideLoader();
        console.error('Error:', error);
        showError('Something went wrong', 'Please try again later.');
    }
}

export async function logoutHandler(){
    
    try {
      const response = await fetch(API_ROUTES.LOGOUT, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        }
      });

      if (response.ok) {
        window.location.href = BASE_URL + 'views/auth/login.php';
      } else {
        showError('Logout Failed', 'Something went wrong while logging out. Please try again.');
      }
    } catch (error) {
      console.error('Error:', error);
      showError('Network Error', 'Unable to connect to the server. Please check your internet connection or try again later.');
    }
}