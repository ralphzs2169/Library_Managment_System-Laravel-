
import { API_ROUTES } from "../config.js";
import { showSuccessWithRedirect, showConfirmation, showInfo, apiRequest, getJsonHeaders } from "../utils.js";


export async function addGenreHandler(categoryId, genreName) {
    // Step 1: Validate only
    let result = apiRequest(API_ROUTES.ADD_GENRE, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ 
            genre_name: genreName, 
            category_id: categoryId, 
            validate_only: true
        })
    })
    
    if (result?.errorHandled) return;

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Add Genre?',
        `Are you sure you want to add the genre "${genreName || '(empty)'}"?`,
        'Yes, add it!'
    );

    if (!isConfirmed) return;

    // Step 3: Actually add
    result = await apiRequest(API_ROUTES.ADD_GENRE, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ genre_name: genreName, category_id: categoryId })
    });
    
    if (result?.errorHandled) return; 

    showSuccessWithRedirect('Success', 'Genre added successfully!', window.location.href);
}

export async function editGenreHandler(genreId, genreName) {
    // Step 1: Validate only
    let result = await apiRequest(`${API_ROUTES.UPDATE_GENRE}/${genreId}`, {
        method: 'PUT',
        headers: getJsonHeaders(),
        body: JSON.stringify({ updated_genre_name: genreName, validate_only: true })
    });
    
    if (result?.errorHandled) return;

    if(result.status === 'unchanged') {
        showInfo('No changes detected', 'You didn’t make any modifications to update.');
        return;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Update Genre?',
        `Update genre from "${result.data.old_genre_name}" to "${genreName}"?`,
        'Yes, update it!'
    );
    if (!isConfirmed) return;

    // Step 3: Actual update
    result = await apiRequest(`${API_ROUTES.UPDATE_GENRE}/${genreId}`, {
        method: 'PUT',
        headers: getJsonHeaders(),
        body: JSON.stringify({ updated_genre_name: genreName })
    });
    
    if (result?.errorHandled) return; 

    showSuccessWithRedirect('Success', 'Genre updated successfully!', window.location.href);
}

export async function deleteGenreHandler(genreId) {
    let result = await apiRequest(`${API_ROUTES.DELETE_GENRE}/${genreId}`, {
        method: 'DELETE',
        headers: getJsonHeaders(),
    });

    if (result?.errorHandled) return;
    
    showSuccessWithRedirect('Success', 'Genre deleted successfully!', window.location.href);
}
