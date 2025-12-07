import { returnBook } from '../../ajax/transactions/borrowingHandler.js';
import { restoreProfileContent } from './bookSelection.js';
import { fetchBorrowerDetails } from '../../ajax/borrowerHandler.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js';
import { fetchSettings } from '../../ajax/settingsHandler.js';
import { getBorrowingStatusBadge } from '../../utils/statusBadge.js';
import { populateRoleBadge } from '../librarian/utils/popupDetailsModal.js';

const confirmReturnModal = document.getElementById('confirm-return-modal');

// Store current data for navigation
let currentBorrower = null;
let currentTransaction = null;
let confirmReturnModalListenersInitialized = false;

export async function initializeConfirmReturnModal(modal, borrower, transaction, context = 'profile_view') {
    currentBorrower = borrower;
    currentTransaction = transaction;
    
    // Handle Back Button Visibility and Context
    const backBtn = modal.querySelector('#return-back-to-borrower-profile');
    if (backBtn) {
        backBtn.dataset.context = context;
        if (context === 'main_table_view') {
            backBtn.classList.remove('hidden');
        } else {
            backBtn.classList.add('hidden');
        }
    }

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

        // Populate role badge
        populateRoleBadge(modal, '#confirm-return-borrower-role-badge', borrower);
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

    // Initialize listeners once
    if (!confirmReturnModalListenersInitialized) {
        confirmReturnModalListenersInitialized = true;

        // Back button
        if (backBtn) {
            backBtn.onclick = returnToPreviousModal;
        }

        const closeConfirmModalBtn = document.getElementById('close-confirm-return');
        if (closeConfirmModalBtn) {
            closeConfirmModalBtn.onclick = () => closeConfirmReturnModal();
        }

        const cancelReturnBtn = document.getElementById('cancel-return-button');
        if (cancelReturnBtn) {
            cancelReturnBtn.onclick = () => closeConfirmReturnModal();
        }

        document.addEventListener('click', function(event) {
            if (event.target === confirmReturnModal) {
                closeConfirmReturnModal();
            }
        });
    } else {
        // If listeners already initialized, just update the context on the back button
        if (backBtn) {
            backBtn.dataset.context = context;
        }
    }
}

export function openConfirmReturnModal(borrower, transaction, context = 'profile_view') {
    currentBorrower = borrower;
    currentTransaction = transaction;
    
    const modal = document.getElementById('confirm-return-modal');
    const modalContent = document.getElementById('confirm-return-content');
    
    // Reset to initial hidden state before showing
    modal.classList.add('bg-opacity-0');
    modal.classList.remove('bg-opacity-50');
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    
    modal.classList.remove('hidden');
    
    // Use double requestAnimationFrame for smoother animation
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        });
    });
    
    initializeConfirmReturnModal(modal, borrower, transaction, context);
}

export function closeConfirmReturnModal(withAnimation = true) {
    const modalContent = document.getElementById('confirm-return-content');
    const modal = document.getElementById('confirm-return-modal');

    const reportDamagedCheckbox = document.getElementById('report_damaged');
    if (reportDamagedCheckbox) reportDamagedCheckbox.checked = false;

    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    if (withAnimation) {
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    } else {
        modal.classList.add('hidden');
    }
}

function returnToPreviousModal() {
    const backBtn = document.getElementById('return-back-to-borrower-profile');
    const context = backBtn.dataset.context || 'profile_view';

    if (context === 'main_table_view') {
        const modal = document.getElementById('confirm-return-modal');
        const modalContent = document.getElementById('confirm-return-content');
        
        // Fade out confirm return modal
        modal.classList.remove('bg-opacity-50');
        modal.classList.add('bg-opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            
            const detailsModal = document.getElementById('borrow-record-details-modal');
            const detailsModalContent = document.getElementById('borrow-record-content');

            if (detailsModal && detailsModalContent) {
                // Reset to initial hidden state
                detailsModal.classList.add('bg-opacity-0');
                detailsModal.classList.remove('bg-opacity-50');
                detailsModalContent.classList.add('scale-95', 'opacity-0');
                detailsModalContent.classList.remove('scale-100', 'opacity-100');
                
                detailsModal.classList.remove('hidden');

                // Trigger animation with double rAF
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        detailsModal.classList.remove('bg-opacity-0');
                        detailsModal.classList.add('bg-opacity-50');
                        detailsModalContent.classList.remove('scale-95', 'opacity-0');
                        detailsModalContent.classList.add('scale-100', 'opacity-100');
                    });
                });
            }
        }, 150);
    } else {
        returnToBorrowerProfile();
    }
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
        const backBtn = document.getElementById('return-back-to-borrower-profile');
        const context = backBtn.dataset.context || 'profile_view';

        if (context === 'main_table_view') {
            closeConfirmReturnModal();
            // Dashboard reloads automatically via returnBook -> reloadStaffDashboardData
        } else {
            const modal = document.getElementById('borrower-profile-modal');
            const { borrower, actionPerformer }= await fetchBorrowerDetails(currentBorrower.id)
            await initializeBorrowerProfileUI(modal, borrower, true, 'currently-borrowed-tab', actionPerformer);
            closeConfirmReturnModal();
        }
    }
}
