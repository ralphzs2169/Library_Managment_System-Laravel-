import { fetchBorrowerDetails } from '../../../api/borrowerHandler.js';
import { initializeBorrowerProfileUI } from './borrowerProfilePopulators.js';
import { restoreProfileContent } from '../borrowBook.js';
import { initializeBorrowerTabs } from './tabbedContainer.js';
import { resetButton, showSkeleton } from '../../../utils.js';

const closeBorrowerModalBtn = document.getElementById('close-borrower-modal');
const borrowerModal = document.getElementById('borrower-profile-modal');

// Prevent duplicate event listener attachment
if (!window._borrowerEventsInitialized) {
    window._borrowerEventsInitialized = true;

    // Use event delegation for dynamically loaded content
    document.addEventListener('click', async function(e) {
        const borrowerBtn = e.target.closest('.open-borrower-modal');
        if (borrowerBtn) {
            e.preventDefault();
            const userId = borrowerBtn.getAttribute('data-user-id');
            openBorrowerProfileModal(userId);
        }
    });

    if (closeBorrowerModalBtn) {
        closeBorrowerModalBtn.addEventListener('click', function (e) {
            e.preventDefault();
            closeBorrowerModal();
        });
    }

    document.addEventListener('click', function(event) {
        if (event.target === borrowerModal) {
            closeBorrowerModal();
        }
    });
}

export async function openBorrowerProfileModal(userId) {
    const modal = document.getElementById('borrower-profile-modal');
    const modalContent = document.getElementById('borrower-profile-content');
    
    // Show modal immediately but invisible
    modal.classList.remove('hidden');
    
    // Trigger animation on next frame
    requestAnimationFrame(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    });

    const borrower = await fetchBorrowerDetails(userId)
    await initializeBorrowerProfileUI(modal, borrower, true);

    // Ensure modal and its scrollable content are scrolled to top after showing
    const scrollableContainer = document.getElementById('borrower-profile-scrollable-content');
    if (scrollableContainer) {
        scrollableContainer.scrollTo({ top: 0, behavior: 'auto' });
    }
}


export function closeBorrowerModal(withAnimation = true, reloadProfileTabs = false) {
    const modalContent = document.getElementById('borrower-profile-content');

    if (withAnimation) {
        borrowerModal.classList.remove('bg-opacity-50');
        borrowerModal.classList.add('bg-opacity-0');
    } else {
        borrowerModal.classList.add('hidden');
        // restoreProfileContent(borrowerModal, null);
    }
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        borrowerModal.classList.add('hidden');
        // showSkeleton(borrowerModal, '#borrower-profile-skeleton', '#borrower-profile-real');
        restoreProfileContent(borrowerModal, null, false);
        showSkeleton(borrowerModal, '#borrower-profile-skeleton', '#borrower-profile-real');
        if (reloadProfileTabs) {
            const { resetTabsToDefault } = initializeBorrowerTabs();
            resetTabsToDefault();
        }
    }, 150);

}
