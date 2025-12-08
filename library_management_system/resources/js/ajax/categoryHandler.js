import { CATEGORY_ROUTES } from "../config.js";
import { showWarning, showConfirmation, showInfo, showToast, showError } from "../utils/alerts.js";
import { apiRequest, getJsonHeaders } from "../utils.js";
import { initCategoryGenreListeners } from "../pages/librarian/categoryGenre.js";

export async function loadCategoryManagement() {
    try {
        const url = CATEGORY_ROUTES?.CONTENT || '/librarian/category-management/content';
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to load categories');
        }

        const html = await response.text();
        const container = document.getElementById('categories-container');
        
        if (container) {
            container.innerHTML = html;
            // Re-initialize event listeners after DOM update
            initCategoryGenreListeners();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        showError('Something went wrong', 'Failed to load categories. Please refresh the page.');
    }
}

export async function addCategoryHandler(categoryName) {
    // Step 1: Validate only
    let result = await apiRequest(CATEGORY_ROUTES.ADD_CATEGORY, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ category_name: categoryName, validate_only: true })
    });

    if (result?.errorHandled) return false;

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Add Category?',
        `Are you sure you want to add "${categoryName || '(empty)'}" as a category?`,
        'Yes, add it!'
    );
    if (!isConfirmed) return false;

    // Step 3: Add category
    result = await apiRequest(CATEGORY_ROUTES.ADD_CATEGORY, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ category_name: categoryName })
    });

    if (result?.errorHandled) return false;

    // Reload categories via AJAX
    await loadCategoryManagement();
    showToast('Category added successfully!', 'success');
    return true;
}


export async function editCategoryHandler(categoryId, categoryName) {
    // Step 1: Validate only
    let result = await apiRequest(`${CATEGORY_ROUTES.UPDATE_CATEGORY}/${categoryId}`, {
        method: 'PUT',
        headers: getJsonHeaders(),
        body: JSON.stringify({ updated_category_name: categoryName, validate_only: true })
    });

    if (result?.errorHandled) return false;

    if (result.status === 'unchanged') {
        showInfo('No changes detected', 'You didn’t make any modifications to update.');
        return false;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Update Category?',
        `Update category from "${result.data.old_category_name}" to "${categoryName}"?`,
        'Yes, update it!'
    );
    if (!isConfirmed) return false;

    // Step 3: Actual update
    result = await apiRequest(`${CATEGORY_ROUTES.UPDATE_CATEGORY}/${categoryId}`, {
        method: 'PUT',
        headers: getJsonHeaders(),
        body: JSON.stringify({ updated_category_name: categoryName, current_category_id: categoryId })
    });

    if (result?.errorHandled) return false;

    // Reload categories via AJAX
    await loadCategoryManagement();
    showToast('Category updated successfully!', 'success');
    return true;
}


export async function deleteCategoryHandler(categoryId) {
    const result = await apiRequest(`${CATEGORY_ROUTES.DELETE_CATEGORY}/${categoryId}`, {
        method: 'DELETE',
        headers: getJsonHeaders()
    });

    if (result?.errorHandled) return false;

    if (result.status === 'error') {
        showWarning(
            'Cannot Delete Category',
            'This category cannot be deleted because it is associated with one or more genres.'
        );
        return false;
    }

    // Reload categories via AJAX
    await loadCategoryManagement();
    showToast('Category deleted successfully!', 'success');
    return true;
}
