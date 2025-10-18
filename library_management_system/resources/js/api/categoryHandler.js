
import { API_ROUTES } from "../config.js";
import { showError, showSuccessWithRedirect, showWarning, showConfirmation, showInfo } from "../utils.js";
import { displayInputErrors } from "../helpers.js";

export async function addCategoryHandler(categoryName) {
    try {
        // Step 1: Validate only
        let response = await fetch(API_ROUTES.ADD_CATEGORY, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name: categoryName, validate_only: true })
        });
        let result = await response.json();
        if(result.status === 'invalid') {
            displayInputErrors(result.errors);
            return;
        }
        // Step 2: Show confirmation
        const isConfirmed = await showConfirmation(
            'Add Category?',
            `Are you sure you want to add the "${categoryName || '(empty)'}" as a category?`,
            'Yes, add it!'
        );
        if (!isConfirmed) return;

        // Step 3: Actually add
        response = await fetch(API_ROUTES.ADD_CATEGORY, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name: categoryName })
        });
        result = await response.json();
        if(result.status === 'failed') {
            displayInputErrors(result.errors);
            return;
        }
        showSuccessWithRedirect('Success', 'Category added successfully!', window.location.href);
    } catch (error) {
        showError('Network Error', 'Unable to add category. Please try again.');
        console.error(error);
    }
}

export async function editCategoryHandler(categoryId, categoryName) {
    try {
        // Step 1: Validate only
        let response = await fetch(`${API_ROUTES.UPDATE_CATEGORY}/${categoryId}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name: categoryName, validate_only: true })
        });
        let result = await response.json();

        if(result.status === 'invalid') {
            // Remap error key for edit modal
            if (result.errors && result.errors['category-name']) {
                result.errors['edit-category-name'] = result.errors['category-name'];
                delete result.errors['category-name'];
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
            'Update Category?',
            `Update category from "${result.oldCategory}" to "${categoryName}"?`,
            'Yes, update it!'
        );
        if (!isConfirmed) return;

        // Step 3: Actual update
        response = await fetch(`${API_ROUTES.UPDATE_CATEGORY}/${categoryId}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ name: categoryName })
        });
        result = await response.json();
        if(result.status === 'failed') {
            showError('Error', 'An error occurred while updating the category.');
            return;
        }
        showSuccessWithRedirect('Success', 'Category updated successfully!', window.location.href);
    } catch (error) {
        showError('Network Error', 'Unable to update category. Please try again.');
        console.error(error);
    }
}

export async function deleteCategoryHandler(categoryId) {
    try {
        const response = await fetch(`${API_ROUTES.DELETE_CATEGORY}/${categoryId}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ _method: 'DELETE'})
        });

        const result = await response.json();
        
        if (result.status === 'failed') {
            if (result.genre_count > 0) {
                showWarning('Cannot Delete Category', 'This category cannot be deleted because it is associated with one or more genres.');
            } else {
                showError('Error', 'An error occurred while deleting the category.');
            }
            return;
        }

        showSuccessWithRedirect('Success', 'Category deleted successfully!', window.location.href);
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while deleting the category.');
    }
}
