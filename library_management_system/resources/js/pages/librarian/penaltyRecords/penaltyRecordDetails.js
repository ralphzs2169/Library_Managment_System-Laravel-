import { openDetailsModal } from "../utils/popupDetailsModal";
import { showError } from "../../../utils/alerts";
import { populateRoleBadge } from "../utils/popupDetailsModal";
import { cancelPenalty } from "../../../ajax/penaltyHandler";
import { openPaymentModal } from "../../staff/confirmPayment";

export function initPenaltyRecordsListeners() {
    const detailButtons = document.querySelectorAll('.open-borrow-record-details');
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
            openPaymentModal(user, penaltyRecord, false);
            closePenaltyRecordModal(false);
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
    penaltyReason.innerHTML = generatePenaltyReasonBadge(penalty);

    // Status badge
    penaltyStatus.innerHTML = generatePenaltyStatusBadge(penalty);

    // Amount
    penaltyAmount.textContent = `₱ ${Number(penalty.amount).toFixed(2)}`;
    if (penalty.status === 'partially_paid' && penaltyRemaining) {
        penaltyRemaining.textContent = `(₱ ${Number(penalty.remaining_amount).toFixed(2)} Left)`;

    } else if (penaltyRemaining) {
        penaltyRemaining.textContent = 'N/A';
 
    }

    // Status badge
    let statusBadgeHTML = '';
    if (penalty.status === 'unpaid') {
        statusBadgeHTML = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
            </svg>
            Unpaid
        </span>`;
    } else if (penalty.status === 'partially_paid') {
        statusBadgeHTML = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700">
            <img src="/build/assets/icons/partially-paid.svg" alt="Partially Paid Icon" class="w-3.5 h-3.5">
            Partially Paid
        </span>`;
    } else if (penalty.status === 'paid') {
        statusBadgeHTML = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Paid
        </span>`;
    } else if (penalty.status === 'cancelled') {
        statusBadgeHTML = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Cancelled
        </span>`;
    } else {
        statusBadgeHTML = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Unknown</span>`;
    }
    penaltyStatus.innerHTML = statusBadgeHTML;

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

// Generate reason badge HTML
function generatePenaltyReasonBadge(penalty) {
    if (penalty.type === 'late_return') {
        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-red-50 text-red-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Late Return
        </span>`;
    } else if (penalty.type === 'lost_book') {
        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-purple-50 text-purple-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Lost
        </span>`;
    } else if (penalty.type === 'damaged_book') {
        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-orange-50 text-orange-700">
            <img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">
            Damaged
        </span>`;
    } else {
        return `<span class="text-gray-500">${penalty.type ? penalty.type.replace('_', ' ') : 'Unknown'}</span>`;
    }
}

// Generate status badge HTML
function generatePenaltyStatusBadge(penalty) {
    if (penalty.status === 'unpaid') {
        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
            </svg>
            Unpaid
        </span>`;
    } else if (penalty.status === 'partially_paid') {
        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700">
            <img src="/build/assets/icons/partially-paid.svg" alt="Partially Paid Icon" class="w-3.5 h-3.5">
            Partially Paid
        </span>`;
    } else if (penalty.status === 'paid') {
        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Paid
        </span>`;
    } else if (penalty.status === 'cancelled') {
        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Cancelled
        </span>`;
    } else {
        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Unknown</span>`;
    }
}

// Helper: format date as 'MMM DD, YYYY'
function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return 'N/A';
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
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

    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');

    if (withAnimation) {    
        setTimeout(() => {
            cleanup();
            modal.classList.add('hidden');
        }, 150);
    } else {       
        cleanup();
        modal.classList.add('hidden');
        modal.classList.remove('bg-opacity-0'); 
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100'); 
    }
}

initPenaltyRecordsListeners();