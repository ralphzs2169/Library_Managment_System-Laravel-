
import { API_ROUTES } from "../config.js";
import { showSuccessWithRedirect, showWarning, showConfirmation, showInfo, getJsonHeaders, apiRequest } from "../utils.js";

export async function addCategoryHandler(categoryName) {
    // Step 1: Validate only
    let result = await apiRequest(API_ROUTES.ADD_CATEGORY, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ category_name: categoryName, validate_only: true })
    });

    if (result?.errorHandled) return;

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Add Category?',
        `Are you sure you want to add "${categoryName || '(empty)'}" as a category?`,
        'Yes, add it!'
    );
    if (!isConfirmed) return;

    // Step 3: Add category
    result = await apiRequest(API_ROUTES.ADD_CATEGORY, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ category_name: categoryName })
    });

    if (result?.errorHandled) return;

    showSuccessWithRedirect('Success', 'Category added successfully!', window.location.href);
}


export async function editCategoryHandler(categoryId, categoryName) {
    // Step 1: Validate only
    let result = await apiRequest(`${API_ROUTES.UPDATE_CATEGORY}/${categoryId}`, {
        method: 'PUT',
        headers: getJsonHeaders(),
        body: JSON.stringify({ updated_category_name: categoryName, validate_only: true })
    });

    if (result?.errorHandled) return;

    if (result.status === 'unchanged') {
        showInfo('No changes detected', 'You didn’t make any modifications to update.');
        return;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Update Category?',
        `Update category from "${result.data.old_category_name}" to "${categoryName}"?`,
        'Yes, update it!'
    );
    if (!isConfirmed) return;

    // Step 3: Actual update
    result = await apiRequest(`${API_ROUTES.UPDATE_CATEGORY}/${categoryId}`, {
        method: 'PUT',
        headers: getJsonHeaders(),
        body: JSON.stringify({ updated_category_name: categoryName, current_category_id: categoryId })
    });

    if (result?.errorHandled) return;

    showSuccessWithRedirect('Success', 'Category updated successfully!', window.location.href);
}


export async function deleteCategoryHandler(categoryId) {
    const result = await apiRequest(`${API_ROUTES.DELETE_CATEGORY}/${categoryId}`, {
        method: 'DELETE',
        headers: getJsonHeaders()
    });

    if (result?.errorHandled) return;

    if (result.status === 'error') {
        showWarning(
            'Cannot Delete Category',
            'This category cannot be deleted because it is associated with one or more genres.'
        );
        return;
    }

    showSuccessWithRedirect('Success', 'Category deleted successfully!', window.location.href);
}
