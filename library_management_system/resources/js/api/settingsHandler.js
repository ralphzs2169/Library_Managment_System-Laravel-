import { SETTINGS_ROUTES, VALIDATION_ERROR } from "../config.js";
import { displayInputErrors } from "../helpers.js";
import { showSuccessWithRedirect, showWarning, showConfirmation, showInfo } from "../utils.js";

export async function updateSettingsHandler(settingsData, form) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    settingsData.append('_token', csrfToken);
    settingsData.append('_method', 'PUT');
    console.log('Settings Data:', Array.from(settingsData.entries())); // Debug log
    // Step 1: Validate only
    settingsData.append('validate_only', 1);
    let response = await fetch(SETTINGS_ROUTES.UPDATE, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: settingsData
    });

    const result = await response.json();

    if (!response.ok) {
        if (response.status === VALIDATION_ERROR) {
            displayInputErrors(result.errors, form); // Pass form element, not string
            return;
        }
        showWarning('Something went wrong', 'Please try again.');
        return;
    }

    // Check if no changes detected
    if (result.status === 'unchanged') {
        showInfo('No changes detected', 'You didn\'t make any modifications to update.');
        return;
    }

    // Step 2: Show confirmation
    const isConfirmed = await showConfirmation(
        'Save Settings?',
        'Are you sure you want to save these changes?',
        'Yes, save'
    );

    if (!isConfirmed) return;
    
    settingsData.delete('validate_only');

    // Step 3: Submit update
    response = await fetch(SETTINGS_ROUTES.UPDATE, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: settingsData
    });

    if (!response.ok) {
        showWarning('Something went wrong', 'Please try again.');
        return;
    }

    showSuccessWithRedirect('Success', 'Settings updated successfully!', window.location.href);
}
