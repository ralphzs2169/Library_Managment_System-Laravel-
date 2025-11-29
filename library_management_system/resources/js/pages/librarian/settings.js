import { updateSettingsHandler } from '../../ajax/settingsHandler.js';
import { clearInputError } from '../../helpers.js';

const settingsForm = document.querySelector('#settings-form'); // Changed from 'form'

if (settingsForm) {
    settingsForm.addEventListener('submit', async function(e) {
        e.preventDefault(); 
        
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
    });
}
