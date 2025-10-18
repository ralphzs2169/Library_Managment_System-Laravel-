
import { API_ROUTES } from "../config.js";
import { showError, showSuccessWithRedirect, showWarning, showConfirmation, showInfo } from "../utils.js";
import { displayInputErrors } from "../helpers.js";

export async function addGenreHandler(categoryId, genreName) {
    try {
        // Step 1: Validate only
        let response = await fetch(API_ROUTES.ADD_GENRE, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name: genreName, category_id: categoryId, validate_only: true })
        });
        let result = await response.json();
        if(result.status === 'invalid') {
            displayInputErrors(result.errors);
            return;
        }
        // Step 2: Show confirmation
        const isConfirmed = await showConfirmation(
            'Add Genre?',
            `Are you sure you want to add the genre "${genreName || '(empty)'}"?`,
            'Yes, add it!'
        );
        if (!isConfirmed) return;

        // Step 3: Actually add
        response = await fetch(API_ROUTES.ADD_GENRE, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name: genreName, category_id: categoryId })
        });
        result = await response.json();
        if(result.status === 'failed') {
            displayInputErrors(result.errors);
            return;
        }
        showSuccessWithRedirect('Success', 'Genre added successfully!', window.location.href);
    } catch (error) {
        showError('Network Error', 'Unable to add genre. Please try again.');
        console.error(error);
    }
}

export async function editGenreHandler(genreId, genreName) {
    try {
        // Step 1: Validate only
        let response = await fetch(`${API_ROUTES.UPDATE_GENRE}/${genreId}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name: genreName, validate_only: true })
        });
        let result = await response.json();

        if(result.status === 'invalid') {
            // Remap error key for edit modal
            if (result.errors && result.errors['genre-name']) {
                result.errors['edit-genre-name'] = result.errors['genre-name'];
                delete result.errors['genre-name'];
            }
            displayInputErrors(result.errors);
            return;
        }

        if(result.status === 'unchanged') {
            showInfo('No changes detected', 'You didn’t make any modifications to update.');
            return;
        }

        // Step 2: Show confirmation
        const isConfirmed = await showConfirmation(
            'Update Genre?',
            `Update genre from "${result.oldGenre}" to "${genreName}"?`,
            'Yes, update it!'
        );
        if (!isConfirmed) return;

        // Step 3: Actual update
        response = await fetch(`${API_ROUTES.UPDATE_GENRE}/${genreId}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name: genreName })
        });
        result = await response.json();
        if(result.status === 'failed') {
            showError('Error', 'An error occurred while updating the genre.');
            return;
        }
        showSuccessWithRedirect('Success', 'Genre updated successfully!', window.location.href);
    } catch (error) {
        showError('Network Error', 'Unable to update genre. Please try again.');
        console.error(error);
    }
}

export async function deleteGenreHandler(genreId) {
    try {
        const response = await fetch(`${API_ROUTES.DELETE_GENRE}/${genreId}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ _method: 'DELETE'})
        });

        const result = await response.json();
        
        if (result.status === 'failed') {
            showError('Error', result.message || 'An error occurred while deleting the genre.');
            return;
        }

        showSuccessWithRedirect('Success', 'Genre deleted successfully!', window.location.href);
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while deleting the genre.');
    }
}
