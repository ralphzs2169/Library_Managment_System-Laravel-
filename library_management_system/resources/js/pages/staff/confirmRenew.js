import { clearInputError } from '../../helpers.js';
import { fetchSettings } from '../../ajax/settingsHandler.js';
import { renewBook } from '../../ajax/transactions/borrowingHandler.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js';
import { fetchBorrowerDetails } from '../../ajax/borrowerHandler.js';
import { showWarning } from '../../utils/alerts.js';
import { getBorrowingStatusBadge } from '../../utils/statusBadge.js';

let currentRenewer = null;
let currentTransaction = null;
let confirmRenewModalListenersInitialized = false;

export async function initializeConfirmRenewModal(modal, renewer, transaction) {

    currentRenewer = renewer;
    currentTransaction = transaction;

    // renewer info
    const renewerName = modal.querySelector('#renewer-name');
    const renewerId = modal.querySelector('#renewer-id');
    const renewerInitials = modal.querySelector('#renewer-initials');
    if (renewerName) renewerName.textContent = renewer.fullname || 'N/A';
    if (renewerId) {
        const idNumber = renewer.role === 'student'
            ? renewer.students?.student_number
            : renewer.teachers?.employee_number;
        renewerId.textContent = idNumber || 'N/A';
    }
    if (renewerInitials) {
        const initials = (renewer.firstname?.charAt(0) || '') + (renewer.lastname?.charAt(0) || '');
        renewerInitials.textContent = initials || '--';
    }

    // Book info
    const book = transaction.book_copy.book;
    modal.querySelector('#renew-book-cover').src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
    modal.querySelector('#renew-book-title').textContent = book.title || 'Untitled';
    modal.querySelector('#renew-book-title').title = book.title || 'Untitled';
    modal.querySelector('#renew-book-author').textContent = `by ${(book.author?.firstname || '') + ' ' + (book.author?.lastname || '')}`;
    modal.querySelector('#renew-book-isbn').textContent = book.isbn || 'N/A';
    modal.querySelector('#renew-copy-number').textContent = transaction.book_copy.copy_number || 'N/A';
    modal.querySelector('#renew-initial-borrow-date').textContent = transaction.borrowed_at ? new Date(transaction.borrowed_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
    modal.querySelector('#renew-current-due-date').textContent = transaction.due_at ? new Date(transaction.due_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';

    // Due date input
    const dueDateInput = modal.querySelector('#new-due-date');
    const renewDurationMsg = modal.querySelector('#renew-borrow-duration');

   
    const settings = await fetchSettings();

    if(!settings) {
        showWarning('Something went wrong', 'Please try again later.');
        return;
    }

    // Standard renew duration info
    const renewDuration = (renewer.role === 'student')
        ? settings['renewing.student_duration']
        : settings['renewing.teacher_duration'];
   
    if (renewDurationMsg) {
        renewDurationMsg.textContent = `Standard: ${renewDuration} days from today`;
    }

    if (dueDateInput) {
        // Use the current due date as the base (NOT today)
        const base = new Date(transaction.due_at);

        // Add the renewal duration
        base.setDate(base.getDate() + parseInt(renewDuration, 10));

        // Format YYYY-MM-DD
        const year = base.getFullYear();
        const month = String(base.getMonth() + 1).padStart(2, '0');
        const day = String(base.getDate()).padStart(2, '0');

        // Set the input's value
        dueDateInput.value = `${year}-${month}-${day}`;
        // Attach clear error logic to input
        dueDateInput.addEventListener('change', () => clearInputError(dueDateInput));
        dueDateInput.addEventListener('input', () => clearInputError(dueDateInput));
    }

    // Confirm button
    const renewForm = document.getElementById('confirm-renew-form');
    if (renewForm) {
        renewForm.onsubmit = async function(e) {
            e.preventDefault();
            await handleConfirmRenew(currentRenewer, currentTransaction);
        };
    }

    // Attach close/cancel/back listeners only once
    if (!confirmRenewModalListenersInitialized) {
        confirmRenewModalListenersInitialized = true;

        // Cancel button
        const cancelBtn = modal.querySelector('#renew-cancel-button');
        if (cancelBtn) {
            cancelBtn.onclick = closeConfirmRenewModal;
        }

        // Back button
        const backBtn = modal.querySelector('#renew-back-to-borrower-profile');
        if (backBtn) {
            backBtn.onclick = returnToBorrowerProfileModal;
        }

        // Close button (X icon)
        const closeBtn = modal.querySelector('#renew-close-confirm-modal');
        if (closeBtn) {
            closeBtn.onclick = closeConfirmRenewModal;
        }

        // Click outside modal to close
        document.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeConfirmRenewModal();
            }
        });
    }

    // Status badge logic (consistent with borrowed books status: due_soon, on_time, overdue)
    const statusBadge = modal.querySelector('#confirm-renew-status-badge');
   statusBadge.innerHTML = getBorrowingStatusBadge(transaction);
}

export function openConfirmRenewModal(renewer, transaction) {
    const modal = document.getElementById('confirm-renew-modal');
    const modalContent = document.getElementById('confirm-renew-content');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    });
    initializeConfirmRenewModal(modal, renewer, transaction);
}

// Add close/cancel/back logic as needed, similar to confirmBorrow.js

export function closeConfirmRenewModal() {
    const modal = document.getElementById('confirm-renew-modal');
    const modalContent = document.getElementById('confirm-renew-content');
    clearInputError(document.getElementById('confirm-renew-due-date'));
    // Start closing animation
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        // Optionally restore profile content if needed
    }, 150);
}

function returnToBorrowerProfileModal() {
    closeConfirmRenewModal();
    setTimeout(() => {
        const borrowerModal = document.getElementById('borrower-profile-modal');
        const borrowerModalContent = document.getElementById('borrower-profile-content');
        if (borrowerModal) {
            borrowerModal.classList.remove('hidden');
            requestAnimationFrame(() => {
                borrowerModal.classList.remove('bg-opacity-0');
                borrowerModal.classList.add('bg-opacity-50');
                borrowerModalContent.classList.remove('scale-95', 'opacity-0');
                borrowerModalContent.classList.add('scale-100', 'opacity-100');
            });
        }
    }, 150);
}

async function handleConfirmRenew(currentRenewer, currentTransaction) {

    const renewForm = document.getElementById('confirm-renew-form');
    
    // Prepare form data
    const formData = new FormData(renewForm);
    formData.append('transaction_id', currentTransaction.id);
    formData.append('renewer_id', currentRenewer.id);    
    formData.append('previous_due_date', currentTransaction.due_at);    
    
    // Call the borrow handler
    const result = await renewBook(formData);
    if (result) {
        const modal = document.getElementById('borrower-profile-modal');
        console.log('currentRenewer:', currentRenewer);
        const freshBorrowerDetails = await fetchBorrowerDetails(currentRenewer.id);
        await initializeBorrowerProfileUI(modal, freshBorrowerDetails, true);
        closeConfirmRenewModal();
    }
}