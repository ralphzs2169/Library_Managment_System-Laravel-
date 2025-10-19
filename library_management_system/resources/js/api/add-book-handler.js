import { API_ROUTES, VALIDATION_ERROR } from "../config.js";
import { displayInputErrors } from "../helpers.js";
import { showSuccessWithRedirect, showWarning, showConfirmation, showError } from "../utils.js";

export async function addBookHandler(bookDetails) {
    // Step 1: Validate only
    bookDetails.append('validate_only', 1);
    let response = await fetch(API_ROUTES.ADD_BOOK, {
        method: 'POST',
        body: bookDetails
    });
    
    const result = await response.json();

    if (!response.ok) {
    if (response.status === VALIDATION_ERROR) { // Laravel validation error
        displayInputErrors(result.errors); // show field errors
        return;
    }
    showWarning('Error', result.message || 'An error occurred during validation.');
    return;
}


    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Add Category?',
        `Are you sure you want to add "}" as a category?`,
        'Yes, add it!'
    );
    if (!isConfirmed) return;
    bookDetails.delete('validate_only');
    // Step 3: Add category
    response = await fetch(API_ROUTES.ADD_BOOK, {
        method: 'POST',
        body: bookDetails
    });

    if (response?.errorHandled) return;

    showSuccessWithRedirect('Success', 'Book added successfully!', window.location.href);
}

export async function fetchGenresByCategory(categorySelect, genreSelect, genreLoading, categoryId) {
    try {
        const endpoint = categorySelect.getAttribute('data-endpoint');
        const resp = await fetch(`${endpoint}?category_id=${encodeURIComponent(categoryId)}`);
        const data = await resp.json();

        
        if (Array.isArray(data.genres)) {
            // Loop through genres and add to select option
            data.genres.forEach(genre => {
                const opt = document.createElement('option');
                opt.value = genre.id;
                opt.textContent = genre.name;
                genreSelect.appendChild(opt);
            });
        }
    } catch (e) {
        showError('Something went wrong', 'Failed to fetch genres. Please try again later.');
    } finally {
        genreSelect.disabled = false; 
        if (genreLoading) genreLoading.classList.add('hidden');
    }
}