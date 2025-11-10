import { updateSettingsHandler } from '../../api/settingsHandler.js';
import { clearInputError } from '../../helpers.js';

const settingsForm = document.querySelector('#settings-form'); // Changed from 'form'

if (settingsForm) {
    settingsForm.addEventListener('submit', async function(e) {
        e.preventDefault(); // Prevents page refresh - this was missing
        
        const formData = new FormData(settingsForm);
        await updateSettingsHandler(formData, settingsForm);
    });

    // Clear errors on input change
    const inputs = settingsForm.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', () => clearInputError(input));
        input.addEventListener('change', () => clearInputError(input));
    });
}
