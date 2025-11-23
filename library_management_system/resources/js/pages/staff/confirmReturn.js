import { returnBook } from '../../api/staffTransactionHandler.js';
import { clearInputError } from '../../helpers.js';
import { restoreProfileContent } from './borrowBook.js';
import { fetchBorrowerDetails } from '../../api/borrowerHandler.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js';
import { fetchSettings } from '../../api/settingsHandler.js';
const confirmReturnModal = document.getElementById('confirm-return-modal');

// Store current data for navigation
let currentBorrower = null;
let currentTransaction = null;

export async function initializeConfirmReturnModal(modal, borrower, transaction) {
    currentBorrower = borrower;
    currentTransaction = transaction;
    
    const settings = await fetchSettings();
    const dueReminderThreshold = parseInt(settings['notifications.reminder_days_before_due']);
    
    // Populate borrower info
    if (borrower) {
        const borrowerName = modal.querySelector('#confirm-return-borrower-name');
        const borrowerId = modal.querySelector('#confirm-return-borrower-id');
        const borrowerInitials = modal.querySelector('#confirm-return-borrower-initials');
        
        if (borrowerName) borrowerName.textContent = borrower.full_name || 'N/A';
        if (borrowerId) {
            const idNumber = borrower.role === 'student' 
                ? borrower.students?.student_number 
                : borrower.teachers?.employee_number;
            borrowerId.textContent = idNumber || 'N/A';
        }
        if (borrowerInitials) {
            const names = (borrower.full_name || '').split(' ').filter(Boolean);
            const initials = names.length ? (names[0][0] + (names[1]?.[0] || '')).toUpperCase() : '--';
            borrowerInitials.textContent = initials;
        }
    }

    if (transaction) {
        const book = transaction.book_copy?.book;
        const bookCopy = transaction.book_copy;
        
        const bookCover = modal.querySelector('#confirm-return-book-cover');
        const bookTitle = modal.querySelector('#confirm-return-book-title');
        const bookAuthor = modal.querySelector('#confirm-return-book-author');
        const bookIsbn = modal.querySelector('#confirm-return-book-isbn');
        const copyNumber = modal.querySelector('#confirm-return-copy-number');
        const borrowedDate = modal.querySelector('#confirm-return-borrowed-date');
        const dueDate = modal.querySelector('#confirm-return-due-date');
        const statusBadge = modal.querySelector('#confirm-return-status-badge');
        
        if (bookCover && book) bookCover.src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        if (bookTitle && book) bookTitle.textContent = book.title || 'Untitled';
        if (bookAuthor && book) bookAuthor.textContent = `by ${(book.author?.firstname || '') + ' ' + (book.author?.lastname || '')}`;
        if (bookIsbn && book) bookIsbn.textContent = book.isbn || 'N/A';
        if (copyNumber && bookCopy) copyNumber.textContent = `#${bookCopy.copy_number || 'N/A'}`;
        
        if (borrowedDate) {
            const date = transaction.borrowed_at ? new Date(transaction.borrowed_at) : null;
            borrowedDate.textContent = date ? date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        }
        
        if (dueDate) {
            const date = transaction.due_at ? new Date(transaction.due_at) : null;
            dueDate.textContent = date ? date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        }
        console.log(transaction);
        const daysUntilDue = transaction.days_until_due;
        // Populate status badge (overdue or on-time)
        if (statusBadge) {
            if (transaction.status === 'overdue') {
                statusBadge.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-200 text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Overdue
                    </span>
                `;
            } else if(transaction.status === 'borrowed' && daysUntilDue <= dueReminderThreshold) {
                 statusBadge.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Due in ${daysUntilDue} day${daysUntilDue !== 1 ? 's' : ''}
                    </span>
                `;
            } else {
                statusBadge.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        On Time
                    </span>
                `;
            }
        }
    }

    // Attach confirm button listener
    const confirmReturnBtn = modal.querySelector('#confirm-return-button');
    if (confirmReturnBtn) {
        const newBtn = confirmReturnBtn.cloneNode(true);
        confirmReturnBtn.parentNode.replaceChild(newBtn, confirmReturnBtn);
        
        newBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            await handleConfirmReturn();
        });
    }
}

const backToProfileButton = document.getElementById('back-to-profile-button');
if (backToProfileButton) {
    backToProfileButton.addEventListener('click', function(e) {
        e.preventDefault();
        console.log(currentBorrower);
       returnToBorrowerProfile();
    });
}

const closeConfirmModalBtn = document.getElementById('close-confirm-return');
if (closeConfirmModalBtn) {
    closeConfirmModalBtn.addEventListener('click', function(e) {
        e.preventDefault();
        closeConfirmReturnModal();
    });
}

const cancelReturnBtn = document.getElementById('cancel-return-button');
if (cancelReturnBtn) {
    cancelReturnBtn.addEventListener('click', function(e) {
        e.preventDefault();
        closeConfirmReturnModal();
    });
}

document.addEventListener('click', function(event) {
    if (event.target === confirmReturnModal) {
        closeConfirmReturnModal();
    }
});

export function openConfirmReturnModal(borrower, transaction) {
    currentBorrower = borrower;
    currentTransaction = transaction;
    
    const modal = document.getElementById('confirm-return-modal');
    const modalContent = document.getElementById('confirm-return-content');
    
    modal.classList.remove('hidden');
    
    requestAnimationFrame(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    });
    
    initializeConfirmReturnModal(modal, borrower, transaction);
}

export function closeConfirmReturnModal() {
    const modalContent = document.getElementById('confirm-return-content');
    const modal = document.getElementById('confirm-return-modal');

    const reportDamagedCheckbox = document.getElementById('report_damaged');
    if (reportDamagedCheckbox) reportDamagedCheckbox.checked = false;

    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        // restoreProfileContent(document.getElementById('borrower-profile-modal'), null);
    }, 150);
}

function returnToBorrowerProfile() {
    closeConfirmReturnModal();
    
    setTimeout(() => {
        const borrowerModal = document.getElementById('borrower-profile-modal');
        const borrowerModalContent = document.getElementById('borrower-profile-content');
        
        if (borrowerModal && currentBorrower) {
            borrowerModal.classList.remove('hidden');
            
            requestAnimationFrame(() => {
                borrowerModal.classList.remove('bg-opacity-0');
                borrowerModal.classList.add('bg-opacity-50');
                borrowerModalContent.classList.remove('scale-95', 'opacity-0');
                borrowerModalContent.classList.add('scale-100', 'opacity-100');
            });
            
            restoreProfileContent(borrowerModal, currentBorrower);
        }
    }, 150);
}

async function handleConfirmReturn() {
    const reportDamagedCheckbox = document.getElementById('report_damaged');
    const reportDamaged = reportDamagedCheckbox?.checked || false;
    
    const formData = new FormData();
    formData.append('book_copy_id', currentTransaction.book_copy_id);
    formData.append('borrower_id', currentBorrower.id);
    if (reportDamaged) {
        formData.append('report_damaged', '1');
    }
    
    const result = await returnBook(formData);
    if (result) {
        // returnToBorrowerProfile();
        const modal = document.getElementById('borrower-profile-modal');
        const { borrower, dueReminderThreshold } = await fetchBorrowerDetails(currentBorrower.id)
        await initializeBorrowerProfileUI(modal, borrower, dueReminderThreshold);
        closeConfirmReturnModal();
    }
}
