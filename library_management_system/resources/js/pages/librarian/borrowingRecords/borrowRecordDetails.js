import { fetchSettings } from "../../../ajax/settingsHandler.js";
import { showError } from "../../../utils/alerts.js";
import { openDetailsModal } from "../utils/popupDetailsModal.js";
import { populateRoleBadge } from "../utils/popupDetailsModal.js";
import { formatDate } from "../../../utils.js";
import { getBorrowingStatusBadge } from "../../../utils/statusBadge.js";

export function initializeBorrowRecordDetailListeners() {
    
    const detailButtons = document.querySelectorAll('.open-borrow-record-details');
    detailButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const borrowRecord = JSON.parse(button.getAttribute('data-borrow-record'));

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
document.addEventListener('DOMContentLoaded', initializeBorrowRecordDetailListeners);

let borrowRecordModalListenersInitialized = false;

async function initializeBorrowRecordDetailsModal(modal, data) {
  
    const borrower = data.borrowRecord.user;
    const borrowRecord = data.borrowRecord;
    console.log(borrower);
    const borrowerInitials = document.getElementById('borrower-initials');
    const borrowerName = document.getElementById('borrower-name');
    const borrowerIdNumber = document.getElementById('borrower-id-number');
  
    borrowerInitials.textContent = (borrower.firstname?.[0] ?? '') + (borrower.lastname?.[0] ?? '');
    borrowerName.textContent = borrower.fullname ?? 'N/A';
    borrowerIdNumber.textContent = borrower.role === 'student' ? borrower.students.student_number : borrower.teachers.employee_number;

    // Populate role badge
    populateRoleBadge(modal, '#borrower-role-badge', borrower);

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

        // Close button
        const closeBtn = document.getElementById('close-borrow-details-button');
        if (closeBtn) {
            closeBtn.onclick = closeBorrowRecordModal;
        }

        // X icon
        const xBtn = document.getElementById('close-borrow-record-modal');
        if (xBtn) {
            xBtn.onclick = closeBorrowRecordModal;
        }

        // Click outside modal to close
        document.addEventListener('click', function(event) {
            const modalEl = document.getElementById('borrow-record-details-modal');
            if (event.target === modalEl) {
                closeBorrowRecordModal();
            }
        });
    }
}

// Close modal function
function closeBorrowRecordModal() {
    const modal = document.getElementById('borrow-record-details-modal');
    const modalContent = document.getElementById('borrow-record-content');

    // Start closing animation
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 150);
}



