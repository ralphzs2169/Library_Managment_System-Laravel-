import { clearInputError } from '../../helpers.js';
import { fetchSettings } from '../../api/settingsHandler.js';

let currentRenewer = null;
let currentTransaction = null;

export async function initializeConfirmRenewModal(modal, borrower, transaction, borrowDurationSetting) {
    currentRenewer = borrower;
    currentTransaction = transaction;

    // Borrower info
    const borrowerName = modal.querySelector('#confirm-renew-borrower-name');
    const borrowerId = modal.querySelector('#confirm-renew-borrower-id');
    const borrowerInitials = modal.querySelector('#confirm-renew-borrower-initials');
    if (borrowerName) borrowerName.textContent = borrower.full_name || 'N/A';
    if (borrowerId) {
        const idNumber = borrower.role === 'student'
            ? borrower.students?.student_number
            : borrower.teachers?.employee_number;
        borrowerId.textContent = idNumber || 'N/A';
    }
    if (borrowerInitials) {
        const initials = (borrower.firstname?.charAt(0) || '') + (borrower.lastname?.charAt(0) || '');
        borrowerInitials.textContent = initials || '--';
    }

    // Book info
    const book = transaction.book_copy.book;
    modal.querySelector('#confirm-renew-book-cover').src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
    modal.querySelector('#confirm-renew-book-title').textContent = book.title || 'Untitled';
    modal.querySelector('#confirm-renew-book-author').textContent = `by ${(book.author?.firstname || '') + ' ' + (book.author?.lastname || '')}`;
    modal.querySelector('#confirm-renew-book-isbn').textContent = book.isbn || 'N/A';
    modal.querySelector('#confirm-renew-copy-number').textContent = transaction.book_copy.copy_number || 'N/A';
    modal.querySelector('#confirm-renew-initial-borrow-date').textContent = transaction.borrowed_at ? new Date(transaction.borrowed_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
    modal.querySelector('#confirm-renew-current-due-date').textContent = transaction.due_at ? new Date(transaction.due_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';

    // Due date input
    const dueDateInput = modal.querySelector('#confirm-renew-due-date');
    const borrowDurationMsg = modal.querySelector('#confirm-renew-borrow-duration');
    let borrowDuration = borrowDurationSetting;
    if (!borrowDuration) {
        const settings = await fetchSettings();
        borrowDuration = settings['borrowing.borrow_duration'] || 10;
    }
    if (borrowDurationMsg) {
        borrowDurationMsg.textContent = `Standard: ${borrowDuration} days from today`;
    }
    if (dueDateInput) {
        const today = new Date();
        today.setDate(today.getDate() + parseInt(borrowDuration));
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        dueDateInput.value = `${year}-${month}-${day}`;
        // Attach clear error logic to input
        dueDateInput.addEventListener('change', () => clearInputError(dueDateInput));
        dueDateInput.addEventListener('input', () => clearInputError(dueDateInput));
    }

    // Confirm button
    const confirmBtn = modal.querySelector('#confirm-renew-confirm-borrow-button');
    if (confirmBtn) {
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
        newBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            await handleConfirmRenew();
        });
    }

    // Cancel button
    const cancelBtn = modal.querySelector('#confirm-renew-cancel-borrow-button');
    if (cancelBtn) {
        cancelBtn.onclick = closeConfirmRenewModal;
    }

    // Back button
    const backBtn = modal.querySelector('#confirm-renew-back-to-borrow-book-button');
    if (backBtn) {
        backBtn.onclick = returnToBorrowerProfileModal;
    }

    // Close button
    const closeBtn = modal.querySelector('#confirm-renew-close-confirm-modal');
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
        } else if (transaction.status === 'borrowed' && daysUntilDue <= (borrower.due_reminder_threshold ?? 3)) {
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

export function openConfirmRenewModal(borrower, transaction, borrowDurationSetting) {
    const modal = document.getElementById('confirm-renew-modal');
    const modalContent = document.getElementById('confirm-renew-content');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    });
    initializeConfirmRenewModal(modal, borrower, transaction, borrowDurationSetting);
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


