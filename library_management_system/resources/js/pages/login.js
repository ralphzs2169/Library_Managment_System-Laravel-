import { clearInputError, blurActiveElement } from '../helpers.js';
import { BASE_URL } from '../config.js';
import { loginHandler } from '../api/authHandler.js';

// password visibility toggle
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
const eyeIcon = document.getElementById('eyeIcon');

togglePassword.addEventListener('click', function() {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    
    if (type === 'text') {
        eyeIcon.src = BASE_URL + 'public/assets/icons/eye-on.svg';
    } else {
        eyeIcon.src = BASE_URL + 'public/assets/icons/eye-off.svg';
    }
});

// form error handling
const usernameInput = document.getElementById('username');
const passwordInputField = document.getElementById('password');

// Display errors from server response
usernameInput.addEventListener('focus', function() {
    clearInputError(usernameInput);
});
passwordInputField.addEventListener('focus', function() {
    clearInputError(passwordInputField);
});

// Handle form submission
const loginForm = document.getElementById('loginForm');

loginForm.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    blurActiveElement();

    const formData = {
        username: document.getElementById('username').value,
        password: document.getElementById('password').value
    };

    loginHandler(formData.username, formData.password, loginForm.id);
});