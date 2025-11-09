import { showError, showInfo, showWarning, showConfirmation, showSuccessWithRedirect } from "../utils.js";
import { SEMESTER_ROUTES, VALIDATION_ERROR } from "../config.js";
import { displayInputErrors } from "../helpers.js";
import { highlightSearchMatches } from "../utils.js";

export async function getCreateSemesterForm() {
    try {
        const response = await fetch(SEMESTER_ROUTES.CREATE, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (!response.ok) {
            showError('Something went wrong', 'Unable to access create form. Please try again later.');
            return { success: false };
        }

        return { success: true, data: data };
    } catch (error) {
        showError('Something went wrong', 'Unable to access create form. Please try again later.');
        return { success: false };
    }
}

export async function storeNewSemester(semesterData, form) {
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    semesterData.append('_token', csrfToken);

    // Step 1: Validate only
    semesterData.append('validate_only', 1);
    let response = await fetch(SEMESTER_ROUTES.STORE, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: semesterData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) { 
            displayInputErrors(result.errors, form.id, false); 
            return;
        }
        showError('Something went wrong', 'Please try again.');
        return;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Add Semester?',
        `Are you sure you want to add this semester?`,
        'Yes, add it!'
    );
    if (!isConfirmed) return;
    
    semesterData.delete('validate_only');

    // Step 3: Add Semester
    response = await fetch(SEMESTER_ROUTES.STORE, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: semesterData
    });

    if (!response.ok) {
        showError('Something went wrong', 'Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Semester added successfully!', window.location.href);
}

export async function activateSemester(semesterId, semesterName) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const formData = new FormData();
    formData.append('_token', csrfToken);

    // Step 1: Validate only
    formData.append('validate_only', 1);
    let response = await fetch(SEMESTER_ROUTES.ACTIVATE(semesterId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData
    });

    const result = await response.json();


     if (!response.ok) {
        if (response.status === VALIDATION_ERROR) { 
            showWarning('Action not allowed', 'Cannot activate this semester because another one is currently active.');
            return;
        }
        showError('Something went wrong', 'Please try again.');
        return;
    }


    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Activate Semester?',
        `Are you sure you want to activate "${semesterName}"?`,
        'Yes, activate it!'
    );
    if (!isConfirmed) return;
    
    formData.delete('validate_only');

    // Step 3: Activate Semester
    response = await fetch(SEMESTER_ROUTES.ACTIVATE(semesterId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    });

    if (!response.ok) {
        showError('Something went wrong', 'Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Semester activated successfully!', window.location.href);
}

export async function fetchSemesterDetails(semesterId) {
    try {
        const response = await fetch(SEMESTER_ROUTES.EDIT(semesterId), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const result = await response.json();

        if (!response.ok) {
            showError('Something went wrong', 'Failed to load semester details. Please try again.');
            return null;
        }

        return result.data.semester;
    } catch (error) {
        showError('Something went wrong', 'Failed to load semester details. Please try again.');
        return null;
    }
}

export async function updateSemester(semesterId, semesterData, form) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    semesterData.append('_token', csrfToken);
    semesterData.append('_method', 'PUT');

    // Step 1: Validate only
    semesterData.append('validate_only', 1);
    let response = await fetch(SEMESTER_ROUTES.UPDATE(semesterId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: semesterData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) { 
            displayInputErrors(result.errors, 'edit-semester-form', false); 
            return;
        }
        showError('Something went wrong', 'Failed to update semester. Please try again.');
        return;
    }

    // Check if no changes detected
    if (result.status === 'unchanged') {
        showInfo('No changes detected', 'You didn\'t make any modifications to update.');
        return;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Update Semester?',
        `Are you sure you want to update this semester?`,
        'Yes, update it!'
    );
    if (!isConfirmed) return;
    
    semesterData.delete('validate_only');

    // Step 3: Update Semester
    response = await fetch(SEMESTER_ROUTES.UPDATE(semesterId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: semesterData
    });

    if (!response.ok) {
        showError('Something went wrong', 'Failed to update semester. Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Semester updated successfully!', window.location.href);
}

export async function deactivateSemester(semesterId, semesterName) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const formData = new FormData();
    formData.append('_token', csrfToken);

    // Step 1: Validate only
    formData.append('validate_only', 1);
    let response = await fetch(SEMESTER_ROUTES.DEACTIVATE(semesterId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) { 
            showWarning('Action not allowed', 'This semester cannot be deactivated.');
            return;
        }
        showError('Something went wrong', 'Please try again.');
        return;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Deactivate Semester?',
        `Are you sure you want to deactivate "${semesterName}"? No semester will be active after this action.`,
        'Yes, deactivate it!'
    );
    if (!isConfirmed) return;
    
    formData.delete('validate_only');

    // Step 3: Deactivate Semester
    response = await fetch(SEMESTER_ROUTES.DEACTIVATE(semesterId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    });

    if (!response.ok) {
        showError('Something went wrong', 'Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Semester deactivated successfully!', window.location.href);
}

export async function loadSemesters(page = 1) {
    try {
        const searchInput = document.querySelector('#semester-search');
        const sortSelect = document.querySelector('#semester-sort');
        const statusFilter = document.querySelector('#semester-status-filter');

        const params = new URLSearchParams({
            page,
            search: searchInput?.value || '',
            sort: sortSelect?.value || 'newest',
            status: statusFilter?.value || 'all'
        });

        const response = await fetch(`${SEMESTER_ROUTES.INDEX}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const html = await response.text();

        if (!response.ok) {
            showError('Something went wrong', 'Failed to load semesters.');
            return;
        }

        const container = document.getElementById('semesters-table-container');
        if (container) {
            container.innerHTML = html;
        }

        // Highlight search matches
        const searchTerm = searchInput?.value?.trim();
        if (searchTerm) {
            highlightSearchMatches(searchTerm, '#semesters-table-container', [1]);
        }
    } catch (error) {
        showError('Network Error', 'Unable to load semesters. Please try again later.');
    }
}