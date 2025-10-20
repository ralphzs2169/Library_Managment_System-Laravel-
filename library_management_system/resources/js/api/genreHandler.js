import { API_ROUTES } from "../config.js";
import { showSuccessWithRedirect, showConfirmation, showInfo, apiRequest, getJsonHeaders } from "../utils.js";


export async function addGenreHandler(categoryId, genreName) {
    // Step 1: Validate only
    let result = await apiRequest(API_ROUTES.ADD_GENRE, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({ 
            genre_name: genreName, 
            category_id: categoryId, 
            validate_only: true
        })
    });
    
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

export async function fetchGenresByCategory(categorySelect, genreSelect, genreLoading, categoryId, selectedId = null) {
    console.log('fetchGenresByCategory called');
	try {
		const endpoint = categorySelect.getAttribute('data-endpoint');
		if (genreLoading) genreLoading.classList.remove('hidden');
		genreSelect.disabled = true;
		genreSelect.innerHTML = '<option value="">Select Genre...</option>';

		const resp = await fetch(`${endpoint}?category_id=${encodeURIComponent(categoryId)}`, {
			headers: { 'Accept': 'application/json' }
		});
		if (!resp.ok) throw new Error('Failed to fetch genres');

		const data = await resp.json();

		// Normalize different response shapes into an array of { id, name }
		let genres = [];
		if (Array.isArray(data)) {
			genres = data;
		} else if (Array.isArray(data.data)) {
			genres = data.data;
		} else if (Array.isArray(data.genres)) {
			genres = data.genres;
		} else if (data && typeof data === 'object') {
			const entries = Object.entries(data);
			if (entries.length && typeof entries[0][1] === 'string') {
				genres = entries.map(([id, name]) => ({ id, name }));
			} else {
				const firstArrayProp = Object.values(data).find(v => Array.isArray(v));
				if (firstArrayProp) genres = firstArrayProp;
			}
		}else if (Array.isArray(data.data?.genres)) {
            genres = data.data.genres;
        }

		(genres || []).forEach(g => {
			const opt = document.createElement('option');
			opt.value = g.id ?? g.value ?? '';
			opt.textContent = g.name ?? g.label ?? g.title ?? String(g);
			if (selectedId && String(g.id ?? g.value) === String(selectedId)) opt.selected = true;
			genreSelect.appendChild(opt);
		});

		genreSelect.disabled = genreSelect.options.length <= 1;
	} catch (e) {
		console.error('loadGenresByCategory error', e);
		showError('Something went wrong', 'Failed to fetch genres. Please try again later.');
	} finally {
		if (genreLoading) genreLoading.classList.add('hidden');
		genreSelect.disabled = genreSelect.options.length <= 1;
	}
}