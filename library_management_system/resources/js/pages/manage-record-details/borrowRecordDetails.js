import { fetchSettings } from "../../ajax/settingsHandler.js";
import { showError } from "../../utils/alerts.js";
import { openDetailsModal } from "../librarian/utils/popupDetailsModal.js";
import { populateRoleBadge } from "../librarian/utils/popupDetailsModal.js";
import { formatDate } from "../../utils.js";
import { getBorrowingStatusBadge } from "../../utils/statusBadge.js";
import { openConfirmReturnModal } from "../staff/confirmReturn.js";
import { openConfirmRenewModal } from "../staff/confirmRenew.js";

export function initBorrowRecordDetailListeners() {

    const detailButtons = document.querySelectorAll('.open-borrow-record-details');
    
    detailButtons.forEach(button => {
   
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const borrowRecord = JSON.parse(button.getAttribute('data-borrow-record'));
            console.log(borrowRecord);
            if (!borrowRecord) {
                showError('Data Error', 'Borrow record data is missing or invalid.');
                return;
            }
            
            const modal = document.getElementById('borrow-record-details-modal');
            const modalContent = document.getElementById('borrow-record-content');
            // Always scroll to top before showing
            modalContent.scrollTo({ top: 0, behavior: 'auto' });
            await openDetailsModal(modal, modalContent, { borrowRecord }, initializeBorrowRecordDetailsModal);
        });
    });
}

// Initial attach on DOMContentLoaded

let borrowRecordModalListenersInitialized = false;

async function initializeBorrowRecordDetailsModal(modal, data) {
  
    const borrower = data.borrowRecord.user;
    const borrowRecord = data.borrowRecord;
  
    const borrowerInitials = document.getElementById('borrower-initials');
    const borrowerName = document.getElementById('borrower-name');
    const borrowerIdNumber = document.getElementById('borrower-id-number');
  
    borrowerInitials.textContent = (borrower.firstname?.[0] ?? '') + (borrower.lastname?.[0] ?? '');
    borrowerName.textContent = borrower.fullname ?? 'N/A';
    borrowerIdNumber.textContent = borrower.role === 'student' ? borrower.students.student_number : borrower.teachers.employee_number;

    // Populate role badge
    populateRoleBadge(modal, '#borrower-role-badge', borrower);

        // Handle Return and Renew Buttons
    const returnBtn = document.getElementById('borrow-record-return-btn');
    const renewBtn = document.getElementById('borrow-record-renew-btn');

    if (returnBtn) returnBtn.classList.add('hidden');
    if (renewBtn) renewBtn.classList.add('hidden');
        // Only show if the book is currently borrowed (not returned)
    if (!borrowRecord.returned_at) {
        if (returnBtn) {
            returnBtn.classList.remove('hidden');
            const newReturnBtn = returnBtn.cloneNode(true);
            returnBtn.parentNode.replaceChild(newReturnBtn, returnBtn);
            newReturnBtn.addEventListener('click', () => {
                // First fade out the current modal
                const modal = document.getElementById('borrow-record-details-modal');
                const modalContent = document.getElementById('borrow-record-content');
                
                modal.classList.remove('bg-opacity-50');
                modal.classList.add('bg-opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    const context = 'main_table_view';
                    openConfirmReturnModal(borrower, borrowRecord, context);
                }, 50);
            });
        }

        if (renewBtn) {
            renewBtn.classList.remove('hidden');
            const newRenewBtn = renewBtn.cloneNode(true);
            renewBtn.parentNode.replaceChild(newRenewBtn, renewBtn);
            newRenewBtn.addEventListener('click', () => {
                // First fade out the current modal
                const modal = document.getElementById('borrow-record-details-modal');
                const modalContent = document.getElementById('borrow-record-content');
                
                modal.classList.remove('bg-opacity-50');
                modal.classList.add('bg-opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    const context = 'main_table_view';
                    openConfirmRenewModal(borrower, borrowRecord, context);
                }, 50);
            });
        }
    }

    const settings = await fetchSettings(); 

    if (!settings) {
        showWarning('Something went wrong', 'Unable to load application settings. Some features may not work as expected.');
        return;
    }
    
    const dueReminderThreshold = parseInt(settings['notifications.reminder_days_before_due']);
    if (!dueReminderThreshold) {
        showWarning('Configuration Issue', 'Due reminder threshold is not properly configured. Please check application settings.');
        closeBorrowRecordModal();
        return;
    }

    // Book Info
    const bookCopy = borrowRecord.book_copy;
    const book = bookCopy.book ?? {};

    const bookCover = document.getElementById('record-book-cover');
    const bookTitle = document.getElementById('record-book-title');
    const bookAuthor = document.getElementById('record-book-author');
    const bookCopyId = document.getElementById('record-book-copy-id');
    const bookCategory = document.getElementById('record-book-category');
    const bookGenre = document.getElementById('record-book-genre');
    const semester = document.getElementById('record-semester-name');

    bookCover.src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
    bookTitle.textContent = book.title ?? 'Unknown Title';
    bookAuthor.textContent = book.author ? `${book.author.firstname} ${book.author.lastname}` : 'Unknown Author';
    bookCopyId.textContent = `#${bookCopy.copy_number}`;
    bookCategory.textContent = book.genre?.category?.name ?? 'N/A';
    bookGenre.textContent = book.genre?.name ?? 'N/A';
    semester.textContent = borrowRecord.semester.name ? `${borrowRecord.semester.name}` : 'Not tied to an academic semester';

    // Transaction Timeline
    const borrowedAt = document.getElementById('record-borrowed-at');
    const dueAt = document.getElementById('record-due-at');
    const returnedAt = document.getElementById('record-returned-at');
    const renewedCount = document.getElementById('record-renewed-count');

    borrowedAt.textContent = formatDate(borrowRecord.borrowed_at);
    dueAt.textContent = formatDate(borrowRecord.due_at);
    returnedAt.textContent = borrowRecord.returned_at ? formatDate(borrowRecord.returned_at) : '-- (Pending)';
    renewedCount.textContent = borrowRecord.times_renewed ?? 0;

    // Status badge
    const statusBadge = document.getElementById('record-status-badge');
    statusBadge.className = 'absolute top-3 right-3'; // Ensure correct position

    data.borrowRecord.due_reminder_threshold = dueReminderThreshold;

    statusBadge.innerHTML = getBorrowingStatusBadge(data.borrowRecord);

    // Transaction ID in header
    const transactionId = document.getElementById('transaction-id');
    transactionId.textContent = `#${borrowRecord.id}`;

    // Attach close modal listeners only once
    if (!borrowRecordModalListenersInitialized) {
        borrowRecordModalListenersInitialized = true;


        const closeBtn = document.getElementById('close-borrow-details-button');
        if (closeBtn) {
            closeBtn.onclick = () => closeBorrowRecordModal(true);
        }

        const xBtn = document.getElementById('close-borrow-record-modal');
        if (xBtn) {
            xBtn.onclick = () => closeBorrowRecordModal(true);
        }

        // Click outside modal to close
        document.addEventListener('click', function(event) {
            const modalEl = document.getElementById('borrow-record-details-modal');
            if (event.target === modalEl) {
                closeBorrowRecordModal(true);
            }
        });
    }
}

function closeBorrowRecordModal(withAnimation = true) {
    const modal = document.getElementById('borrow-record-details-modal');
    const modalContent = document.getElementById('borrow-record-content');

    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    if (withAnimation) {
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 50);
    } else {
        modal.classList.add('hidden');
    }
}

initBorrowRecordDetailListeners();

