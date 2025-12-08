import { GENRE_ROUTES } from "../config.js";
import { showConfirmation, showInfo, showError, showToast } from "../utils/alerts.js";
import { apiRequest, getJsonHeaders } from "../utils.js";
import { loadCategoryManagement } from "./categoryHandler.js";

export async function addGenreHandler(categoryId, genreName) {
    // Step 1: Validate only
    let result = await apiRequest(GENRE_ROUTES.ADD_GENRE, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ 
            genre_name: genreName, 
            category_id: categoryId, 
            validate_only: true
        })
    });
    
    if (result?.errorHandled) return false;

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Add Genre?',
        `Are you sure you want to add the genre "${genreName || '(empty)'}"?`,
        'Yes, add it!'
    );

    if (!isConfirmed) return false;

    // Step 3: Actually add
    result = await apiRequest(GENRE_ROUTES.ADD_GENRE, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ genre_name: genreName, category_id: categoryId })
    });
    
    if (result?.errorHandled) return false; 

    // Reload categories via AJAX
    await loadCategoryManagement();
    showToast('Genre added successfully!', 'success');
    return true;
}

export async function editGenreHandler(genreId, genreName) {
    // Step 1: Validate only
    let result = await apiRequest(`${GENRE_ROUTES.UPDATE_GENRE}/${genreId}`, {
        method: 'PUT',
        headers: getJsonHeaders(),
        body: JSON.stringify({ updated_genre_name: genreName, validate_only: true })
    });
    
    if (result?.errorHandled) return false;

    if(result.status === 'unchanged') {
        showInfo('No changes detected', 'You didn\'t make any modifications to update.');
        return false;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Update Genre?',
        `Update genre from "${result.data.old_genre_name}" to "${genreName}"?`,
        'Yes, update it!'
    );
    if (!isConfirmed) return false;

    // Step 3: Actual update
    result = await apiRequest(`${GENRE_ROUTES.UPDATE_GENRE}/${genreId}`, {
        method: 'PUT',
        headers: getJsonHeaders(),
        body: JSON.stringify({ updated_genre_name: genreName })
    });
    
    if (result?.errorHandled) return false; 

    // Reload categories via AJAX
    await loadCategoryManagement();
    showToast('Genre updated successfully!', 'success');
    return true;
}

export async function deleteGenreHandler(genreId) {
    let result = await apiRequest(`${GENRE_ROUTES.DELETE_GENRE}/${genreId}`, {
        method: 'DELETE',
        headers: getJsonHeaders(),
    });

    if (result?.errorHandled) return false;
    
    // Reload categories via AJAX
    await loadCategoryManagement();
    showToast('Genre deleted successfully!', 'success');
    return true;
}

export async function fetchGenresByCategory(categorySelect, genreSelect, genreLoading, categoryId, selectedId = null) {
    try {
        const endpoint = categorySelect.getAttribute('data-endpoint');

        if (genreLoading) genreLoading.classList.remove('hidden');
    
        const resp = await fetch(`${endpoint}?category_id=${encodeURIComponent(categoryId)}`, {
            headers: { 'Accept': 'application/json' }
        });

        if (!resp.ok) throw new Error('Failed to fetch genres');

        const data = await resp.json();
        const genres = data.genres ?? [];

        genres.forEach(genre => {
            const opt = document.createElement('option');
            opt.value = genre.id;
            opt.textContent = genre.name;

            // Only preselect on initial form load
            if (selectedId && String(genre.id) === String(selectedId)) {
                opt.selected = true;
            }

            genreSelect.appendChild(opt);
        });

        genreSelect.disabled = genres.length === 0;
    } catch (e) {
        console.error(e);
        showError('Something went wrong', 'Failed to fetch genres.');
    } finally {
        if (genreLoading) genreLoading.classList.add('hidden');
    }
}