import { returnBook } from '../../ajax/transactions/borrowingHandler.js';
import { restoreProfileContent } from './bookSelection.js';
import { fetchBorrowerDetails } from '../../ajax/borrowerHandler.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js';
import { fetchSettings } from '../../ajax/settingsHandler.js';
import { getBorrowingStatusBadge } from '../../utils/statusBadge.js';

const confirmReturnModal = document.getElementById('confirm-return-modal');

// Store current data for navigation
let currentBorrower = null;
let currentTransaction = null;

export async function initializeConfirmReturnModal(modal, borrower, transaction) {
    currentBorrower = borrower;
    currentTransaction = transaction;
    
    const settings = await fetchSettings();
    const dueReminderThreshold = parseInt(settings['notifications.reminder_days_before_due']);

    if (!dueReminderThreshold || isNaN(dueReminderThreshold)) {
            showWarning('Configuration Issue', 'Due reminder threshold is not properly configured. Please check application settings.');
            closeBorrowerModal();
            return;
    }

    // Populate borrower info
    if (borrower) {
        const borrowerName = modal.querySelector('#confirm-return-borrower-name');
        const borrowerId = modal.querySelector('#confirm-return-borrower-id');
        const borrowerInitials = modal.querySelector('#confirm-return-borrower-initials');
        
        if (borrowerName) borrowerName.textContent = borrower.fullname || 'N/A';
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
        
        bookCover.src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        bookTitle.textContent = book.title || 'Untitled';
        bookTitle.title = book.title || 'Untitled';
        bookAuthor.textContent = `by ${(book.author?.firstname || '') + ' ' + (book.author?.lastname || '')}`;
        bookIsbn.textContent = book.isbn || 'N/A';
        copyNumber.textContent = `#${bookCopy.copy_number || 'N/A'}`;
        
        if (borrowedDate) {
            const date = transaction.borrowed_at ? new Date(transaction.borrowed_at) : null;
            borrowedDate.textContent = date ? date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        }
        
        if (dueDate) {
            const date = transaction.due_at ? new Date(transaction.due_at) : null;
            dueDate.textContent = date ? date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        }

        statusBadge.innerHTML = getBorrowingStatusBadge(transaction);
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
        const freshBorrowerDetails = await fetchBorrowerDetails(currentBorrower.id)
        await initializeBorrowerProfileUI(modal, freshBorrowerDetails);
        closeConfirmReturnModal();
    }
}
