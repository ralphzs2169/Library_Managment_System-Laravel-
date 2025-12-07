import { apiRequest, showLoader, hideLoader } from '../../utils.js';
import { clearInputError, autoCapitalizeWords, blurActiveElement, autoCapitalizeOnBlur } from '../../helpers.js';
import { showToast } from '../../utils/alerts.js';
import { signupNewPersonnelHandler } from '../../ajax/authHandler.js';

document.addEventListener('DOMContentLoaded', function() {
    // --- Modal Logic ---
    const modal = document.getElementById('add-personnel-modal');
    const backdrop = document.getElementById('add-personnel-backdrop');
    const panel = document.getElementById('add-personnel-panel');
    const openBtn = document.getElementById('open-add-personnel-modal');
    const closeBtns = document.querySelectorAll('.close-modal');

    if (!modal || !backdrop || !panel) return;

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling

        // Small delay to ensure display:block is applied before transition starts
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');

            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        });
    }

    function closeModal() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');

        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');

        // Wait for transition to finish before hiding
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            // Optional: Reset form on close
            const form = document.getElementById('add-personnel-form');
            if (form) {
                form.reset();
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => clearInputError(input));
            }
        }, 300); // Matches duration-300
    }

    if (openBtn) {
        openBtn.addEventListener('click', openModal);
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (!panel.contains(e.target)) {
            closeModal();
        }
    });

    // --- Form Logic ---
    const form = document.getElementById('add-personnel-form');
    const firstnameField = document.getElementById('firstname');
    const lastnameField = document.getElementById('lastname');
    const middleInitialField = document.getElementById('middle_initial');

    // Apply auto-capitalization
    if (firstnameField && lastnameField && middleInitialField) {
        [firstnameField, lastnameField, middleInitialField].forEach(autoCapitalizeWords);
        autoCapitalizeOnBlur([firstnameField, lastnameField, middleInitialField]);
    }

    // Clear errors on focus
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            clearInputError(input);
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        blurActiveElement();

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const success = await signupNewPersonnelHandler(data, form);
        if (success) {
            closeModal();
            showToast('Personnel account created successfully.', 'success');
        }
    });
});
