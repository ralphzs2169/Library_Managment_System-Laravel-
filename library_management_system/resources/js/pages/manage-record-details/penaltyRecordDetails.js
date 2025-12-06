import { openDetailsModal } from "../librarian/utils/popupDetailsModal";
import { showError } from "../../utils/alerts";
import { populateRoleBadge } from "../librarian/utils/popupDetailsModal";
import { cancelPenalty } from "../../ajax/penaltyHandler";
import { openPaymentModal } from "../staff/confirmPayment";
import { getPenaltyStatusBadge, getPenaltyTypeBadge } from "../../utils/statusBadge";
import { formatDate } from "../../utils";

export function initPenaltyRecordsListeners() {

    const detailButtons = document.querySelectorAll('.open-penalty-record-details');
    detailButtons.forEach(button => {

        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const penaltyRecord = JSON.parse(button.getAttribute('data-penalty-record'));

            if (!penaltyRecord) {
                showError('Data Error', 'Penalty record data is missing or invalid.');
                return;
            }
            
            const modal = document.getElementById('penalty-record-details-modal');
            const modalContent = document.getElementById('penalty-record-content');
            // Scroll to top before showing
            const scrollableContainer = modalContent.querySelector('.px-6.py-5.overflow-y-auto');
            if (scrollableContainer) {
                scrollableContainer.scrollTop = 0;
            }
            await openDetailsModal(modal, modalContent, { penaltyRecord }, initializePenaltyRecordDetailsModal);
        });
    });
}

let penaltyRecordModalListenersInitialized = false;

function initializePenaltyRecordDetailsModal(modal, data) {

    const penaltyRecord = data.penaltyRecord;
    const user = penaltyRecord.user;
    const penalty = penaltyRecord.penalty;
    const bookCopy = penaltyRecord.book_copy;
    const book = bookCopy.book ?? {};

    const cancelledAtContainer = document.getElementById('penalty-record-cancelled-at-container');
    const cancelledAt = document.getElementById('penalty-record-cancelled-at');

    if (penalty.status === 'unpaid' || penalty.status === 'partially_paid') {

        const processPaymentBtn = document.getElementById('process-payment-button');
        const cancelPenaltyBtn = document.getElementById('cancel-penalty-button');
        
        processPaymentBtn.classList.remove('hidden');
        cancelPenaltyBtn.classList.remove('hidden'); 

        cancelPenaltyBtn.onclick = async () => {
            const result = await cancelPenalty(penalty.id, user.id, false);
            if (result) {
                closePenaltyRecordModal();
            }
        }

        processPaymentBtn.onclick = async () => {
            const modal = document.getElementById('penalty-record-details-modal');
            const modalContent = document.getElementById('penalty-record-content');
            
            // Fade out penalty modal first
            modal.classList.remove('bg-opacity-50');
            modal.classList.add('bg-opacity-0');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                const context = 'main_table_view';
                openPaymentModal(user, penaltyRecord, context);
            }, 50);
        }

    } else if (penalty.status === 'cancelled') {
        cancelledAtContainer.classList.remove('hidden');
        cancelledAt.textContent = penalty.cancelled_at ? formatDate(penalty.cancelled_at) : '--';
    }
    // Borrower Info
    const borrowerInitials = document.getElementById('penalty-borrower-initials');
    const borrowerName = document.getElementById('penalty-borrower-name');
    const borrowerIdNumber = document.getElementById('penalty-borrower-id-number');
    borrowerInitials.textContent = (user.firstname?.[0] ?? '') + (user.lastname?.[0] ?? '');
    borrowerName.textContent = user.fullname ?? 'N/A';
    borrowerIdNumber.textContent = user.role === 'student' ? user.students.student_number : user.teachers.employee_number;

    // Populate role badge
    populateRoleBadge(modal, '#penalty-borrower-role-badge', user);

    // Book Info
    const bookCover = document.getElementById('penalty-record-book-cover');
    const bookTitle = document.getElementById('penalty-record-book-title');
    const bookAuthor = document.getElementById('penalty-record-book-author');
    const bookCopyId = document.getElementById('penalty-record-book-copy-id');
    bookCover.src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
    bookTitle.textContent = book.title ?? 'Unknown Title';
    bookAuthor.textContent = book.author ? `${book.author.firstname} ${book.author.lastname}` : 'Unknown Author';
    bookCopyId.textContent = `#${bookCopy.copy_number}`;

    // Penalty Info

    const penaltyAmount = document.getElementById('penalty-record-amount');
    const penaltyStatus = document.getElementById('penalty-record-status-badge');
    const penaltyIssuedAt = document.getElementById('penalty-record-issued-at');
    const penaltyReason = document.getElementById('penalty-record-reason');
    const penaltyRemaining = document.getElementById('penalty-record-remaining-amount');
    const penaltyAssocBorrowId = document.getElementById('penalty-record-assoc-borrow-id');

    // Associated Borrow ID
    penaltyAssocBorrowId.textContent = `#${penaltyRecord.penalty.borrow_transaction_id}`;

    // Reason badge
    penaltyReason.innerHTML = getPenaltyTypeBadge(penalty.type);

    // Status badge
    penaltyStatus.innerHTML = getPenaltyStatusBadge(penalty.status);

    // Amount
    penaltyAmount.textContent = `₱ ${Number(penalty.amount).toFixed(2)}`;
    if (penalty.status === 'partially_paid' && penaltyRemaining) {
        penaltyRemaining.textContent = `(₱ ${Number(penalty.remaining_amount).toFixed(2)} Left)`;

    } else if (penaltyRemaining) {
        penaltyRemaining.textContent = 'N/A';
 
    }

    // Issued at
    penaltyIssuedAt.textContent = formatDate(penalty.created_at);

    // Transaction ID in header
    const transactionId = document.getElementById('penalty-transaction-id');
    transactionId.textContent = `#${penaltyRecord.id}`;

    // Last Payment Date for partially paid
    const lastPaymentDateEl = document.getElementById('penalty-record-paid-at');
    if (penalty.status === 'partially_paid' && Array.isArray(penalty.payments) && penalty.payments.length > 0) {
        // Find latest payment by created_at
        const lastPayment = penalty.payments.reduce((latest, payment) => {
            return (!latest || new Date(payment.created_at) > new Date(latest.created_at)) ? payment : latest;
        }, null);
        if (lastPayment && lastPaymentDateEl) {
            lastPaymentDateEl.textContent = formatDate(lastPayment.created_at);
        }
    } else if (lastPaymentDateEl) {
        lastPaymentDateEl.textContent = '-- (Pending)';
    }

    // Attach close modal listeners only once
    if (!penaltyRecordModalListenersInitialized) {
        penaltyRecordModalListenersInitialized = true;

        // Close button
        const closeBtn = document.getElementById('close-penalty-record-modal');
        if (closeBtn) {
            closeBtn.onclick = closePenaltyRecordModal;
        }

        // X icon
        const xBtn = document.getElementById('close-penalty-details-button');
        if (xBtn) {
            xBtn.onclick = closePenaltyRecordModal;
        }

        // Click outside modal to close
        document.addEventListener('click', function(event) {
            const modalEl = document.getElementById('penalty-record-details-modal');
            if (event.target === modalEl) {
                closePenaltyRecordModal();
            }
        });
    }
}



// Close modal function
function closePenaltyRecordModal(withAnimation = true) {
    const modal = document.getElementById('penalty-record-details-modal');
    const modalContent = document.getElementById('penalty-record-content');
    const cancelButton = document.getElementById('cancel-penalty-button');
    const processPaymentButton = document.getElementById('process-payment-button');
    const cancelledAtContainer = document.getElementById('penalty-record-cancelled-at-container');

    const cleanup = () => {
        cancelButton.classList.add('hidden');
        processPaymentButton.classList.add('hidden');
        cancelledAtContainer.classList.add('hidden');
    };

    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    if (withAnimation) {    
        setTimeout(() => {
            cleanup();
            modal.classList.add('hidden');
        }, 50);
    } else {       
        cleanup();
        modal.classList.add('hidden');
    }
}

initPenaltyRecordsListeners();