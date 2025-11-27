import { clearInputError } from '../../helpers.js';
import { fetchSettings } from '../../api/settingsHandler.js';
import { renewBook } from '../../api/staffTransactionHandler.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js';
import { fetchBorrowerDetails } from '../../api/borrowerHandler.js';
import { showWarning } from '../../utils/alerts.js';

let currentRenewer = null;
let currentTransaction = null;

export async function initializeConfirmRenewModal(modal, renewer, transaction) {
    currentRenewer = renewer;
    currentTransaction = transaction;
    console.table(renewer);
    // renewer info
    const renewerName = modal.querySelector('#renewer-name');
    const renewerId = modal.querySelector('#renewer-id');
    const renewerInitials = modal.querySelector('#renewer-initials');
    if (renewerName) renewerName.textContent = renewer.full_name || 'N/A';
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

    // Close button
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

    // Status badge logic (consistent with borrowed books status: due_soon, on_time, overdue)
    const statusBadge = modal.querySelector('#confirm-renew-status-badge');
    if (statusBadge) {
        let badgeHTML = '';
        const daysUntilDue = transaction.days_until_due;
        const daysOverdue = transaction.days_overdue;
        if (transaction.status === 'overdue') {
            badgeHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-200 text-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    ${daysOverdue} day${daysOverdue !== 1 ? 's' : ''} overdue
                </span>
            `;
        } else if (transaction.status === 'borrowed' && daysUntilDue <= (renewer.due_reminder_threshold ?? 3)) {
            badgeHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Due in ${daysUntilDue} day${daysUntilDue !== 1 ? 's' : ''}
                </span>
            `;
        } else if (transaction.status === 'borrowed') {
            badgeHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    On time
                </span>
            `;
        } else {
            badgeHTML = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                    Unknown
                </span>
            `;
        }
        statusBadge.innerHTML = badgeHTML;
    }
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