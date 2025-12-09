import { suspendUser, liftSuspension } from '../../ajax/suspensionHandler.js';
import { fetchBorrowerDetails } from '../../ajax/borrowerHandler.js';
import { initializeBorrowerProfileUI } from '../staff/borrower/borrowerProfilePopulators.js';
import { clearInputError } from '../../helpers.js';

export function openSuspensionModal(borrower, actionPerformer) {
    const modal = document.getElementById('suspend-user-modal');
    
    if (!modal) {
        console.error('Suspend user modal not found in DOM');
        return;
    }

    // Fix: Move modal to body to avoid nesting issues with transforms (scale-100)
    // and ensure it's on top of other modals
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
    
    // Ensure z-index is higher than borrower profile modal (usually z-50)
    modal.style.zIndex = '60';

    const content = document.getElementById('suspend-user-content');
    const reasonInput = document.getElementById('reason');
    
    // Populate info
    const initialsEl = document.getElementById('suspend-user-initials');
    if (initialsEl) {
        initialsEl.textContent = (borrower.firstname?.[0] ?? '') + (borrower.lastname?.[0] ?? '');
    }

    const nameEl = document.getElementById('suspend-user-name');
    if (nameEl) {
        nameEl.textContent = borrower.fullname;
    }

    if (reasonInput) {
        reasonInput.value = '';
        // Reset error state
        reasonInput.classList.remove('border-red-500', 'ring-2', 'ring-red-500');
        reasonInput.classList.add('border-gray-300');
        
        // Use clearInputError for interaction events
        const clearError = () => clearInputError(reasonInput);
        reasonInput.oninput = clearError;
        reasonInput.onfocus = clearError;
    }
    
    // Show modal
    modal.classList.remove('hidden');
    // Force reflow
    void modal.offsetWidth;
    
    setTimeout(() => {
        modal.classList.remove('bg-opacity-0');
        if (content) {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }
    }, 10);

    // Handlers
    const closeBtn = document.getElementById('close-suspend-user-modal');
    const cancelBtn = document.getElementById('cancel-suspend-btn');
    const confirmBtn = document.getElementById('confirm-suspend-btn');

    const closeModal = () => {
        modal.classList.add('bg-opacity-0');
        if (content) {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
        }
        setTimeout(() => modal.classList.add('hidden'), 150);
    };

    if(closeBtn) closeBtn.onclick = closeModal;
    if(cancelBtn) cancelBtn.onclick = closeModal;

    // Click outside to close
    modal.onclick = (e) => {
        if (e.target === modal) {
            closeModal();
        }
    };

    if(confirmBtn) {
        confirmBtn.onclick = async () => {
            const reason = reasonInput ? reasonInput.value.trim() : '';

            const success = await suspendUser(borrower.id, reason, actionPerformer?.id);
            if (success) {
                closeModal();
                // Refresh borrower profile
                const profileModal = document.getElementById('borrower-profile-modal');
                if (profileModal) {
                    const { borrower: updatedBorrower } = await fetchBorrowerDetails(borrower.id);
                    initializeBorrowerProfileUI(profileModal, updatedBorrower, false, 'currently-borrowed-tab', actionPerformer);
                }
            }
        };
    }
}

export function openLiftSuspensionModal(borrower, actionPerformer) {
    const modal = document.getElementById('lift-suspension-modal');
    
    if (!modal) {
        console.error('Lift suspension modal not found in DOM');
        return;
    }

    // Fix: Move modal to body
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
    
    // Ensure z-index
    modal.style.zIndex = '60';

    const content = document.getElementById('lift-suspension-content');
    const reasonInput = document.getElementById('lift-suspension-reason');
    
    // Populate info
    const initialsEl = document.getElementById('lift-suspension-initials');
    if (initialsEl) {
        initialsEl.textContent = (borrower.firstname?.[0] ?? '') + (borrower.lastname?.[0] ?? '');
    }

    const nameEl = document.getElementById('lift-suspension-name');
    if (nameEl) {
        nameEl.textContent = borrower.fullname;
    }

    if (reasonInput) {
        reasonInput.value = '';
    }

    // Show modal
    modal.classList.remove('hidden');
    // Force reflow
    void modal.offsetWidth;

    setTimeout(() => {
        modal.classList.remove('bg-opacity-0');
        if (content) {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }
    }, 10);

    // Handlers
    const closeBtn = document.getElementById('close-lift-suspension-modal');
    const cancelBtn = document.getElementById('cancel-lift-btn');
    const confirmBtn = document.getElementById('confirm-lift-btn');

    const closeModal = () => {
        modal.classList.add('bg-opacity-0');
        if (content) {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
        }
        setTimeout(() => modal.classList.add('hidden'), 150);
    };

    if(closeBtn) closeBtn.onclick = closeModal;
    if(cancelBtn) cancelBtn.onclick = closeModal;

    // Click outside to close
    modal.onclick = (e) => {
        if (e.target === modal) {
            closeModal();
        }
    };

    if(confirmBtn) {
        confirmBtn.onclick = async () => {
            const reason = reasonInput ? reasonInput.value.trim() : '';
            const success = await liftSuspension(borrower.id, reason, actionPerformer?.id);
            if (success) {
                closeModal();
                // Refresh borrower profile
                const profileModal = document.getElementById('borrower-profile-modal');
                if (profileModal) {
                    const { borrower: updatedBorrower } = await fetchBorrowerDetails(borrower.id);
                    initializeBorrowerProfileUI(profileModal, updatedBorrower, false, 'currently-borrowed-tab', actionPerformer);
                }
            }
        };
    }
}
