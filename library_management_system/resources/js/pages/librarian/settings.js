import { updateSettingsHandler } from '../../ajax/settingsHandler.js';
import { clearInputError } from '../../helpers.js';
import { initializeEditableFields, enableAllFields } from '../../utils/fieldEditor.js';

const settingsForm = document.querySelector('#settings-form'); // Changed from 'form'

if (settingsForm) {
    // Initialize click-to-edit behavior
    initializeEditableFields(settingsForm);

    settingsForm.addEventListener('submit', async function(e) {
        e.preventDefault(); 
        
        // Enable all fields so FormData can capture their values
        enableAllFields(settingsForm);

        const formData = new FormData(settingsForm);
        await updateSettingsHandler(formData, settingsForm);
    });

    // Clear errors on input change
    const inputs = settingsForm.querySelectorAll('input');

    inputs.forEach(input => {
        input.addEventListener('input', () => clearInputError(input));
        input.addEventListener('change', () => clearInputError(input));
        input.addEventListener('focus', () => clearInputError(input));
    });

    settingsForm.addEventListener('reset', function(e) {
        inputs.forEach(input => clearInputError(input));
        // Re-initialize to reset disabled states if needed, 
        // though reset usually restores values, not disabled state.
        // We might want to reload or just let the user edit.
        setTimeout(() => initializeEditableFields(settingsForm), 50);
    });
}
