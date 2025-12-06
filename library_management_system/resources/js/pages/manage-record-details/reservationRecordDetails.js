import { openDetailsModal } from "../librarian/utils/popupDetailsModal";
import { showError } from "../../utils/alerts";
import { populateRoleBadge } from "../librarian/utils/popupDetailsModal";
import { cancelReservation } from "../../ajax/transactions/reservationHandler";
import { closeConfirmBorrowModal, openConfirmBorrowModal } from "../staff/confirmBorrow";
import { getReservationStatusBadge } from "../../utils/statusBadge";
import { formatDate } from "../../utils";


export function initReservationRecordDetailListeners() {
    // Always re-attach listeners after table updates (AJAX)
    const detailButtons = document.querySelectorAll('.open-reservation-record-details');
    if (!detailButtons.length) return; // No buttons found

    detailButtons.forEach(button => {
        // Remove previous listener if any
        button.onclick = null;
        button.addEventListener('click', async (e) => {
            e.preventDefault();
       
            const reservationRecord = JSON.parse(button.getAttribute('data-reservation-record'));
            
            if (!reservationRecord) {
                showError('Data Error', 'Reservation record data is missing or invalid.');
                return;
            }
            const modal = document.getElementById('reservation-record-details-modal');
            const modalContent = document.getElementById('reservation-record-content');
            if (!modal || !modalContent) {
                showError('UI Error', 'Reservation details modal not found in DOM.');
                return;
            }
            // Scroll to top before showing
            const scrollableContainer = modalContent.querySelector('.px-6.py-5.overflow-y-auto');
            if (scrollableContainer) {
                scrollableContainer.scrollTop = 0;
            }
            await openDetailsModal(modal, 
                                   modalContent, 
                                   { reservationRecord }, 
                                   initializeReservationRecordDetailsModal);
        });
    });
}

// If you load table via AJAX, make sure to call initializeReservationRecordDetailListeners()
// after every table update (see your AJAX handler).

let reservationRecordModalListenersInitialized = false;

function initializeReservationRecordDetailsModal(modal, data) {
    const reservation = data.reservationRecord;
    const borrower = reservation.borrower;
    const book = reservation.book ?? {};

    // Borrower Info
    const borrowerInitials = document.getElementById('reservation-borrower-initials');
    const borrowerName = document.getElementById('reservation-borrower-name');
    const borrowerIdNumber = document.getElementById('reservation-borrower-id-number');
    borrowerInitials.textContent = (borrower.firstname?.[0] ?? '') + (borrower.lastname?.[0] ?? '');
    borrowerName.textContent = borrower.fullname ?? 'N/A';
    borrowerIdNumber.textContent = borrower.role === 'student' ? borrower.students.student_number : borrower.teachers.employee_number;

    // Populate role badge
    populateRoleBadge(modal, '#reservation-borrower-role-badge', borrower);

    // Book Info
    const bookCover = document.getElementById('reservation-record-book-cover');
    const bookTitle = document.getElementById('reservation-record-book-title');
    const bookAuthor = document.getElementById('reservation-record-book-author');
    bookCover.src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
    bookTitle.textContent = book.title ?? 'Unknown Title';
    bookAuthor.textContent = book.author ? `by ${book.author.firstname} ${book.author.lastname}` : 'Unknown Author';

    // Reservation Info
    const reservedAt = document.getElementById('reservation-record-reserved-at');
    const queuePosition = document.getElementById('reservation-record-queue-position');
    const confirmedCopyNumber = document.getElementById('reservation-record-confirmed-copy-number');
    const reservationCreator = document.getElementById('reservation-record-created-by');

    reservedAt.textContent = formatDate(reservation.created_at);
    queuePosition.textContent = reservation.queue_position ? `#${reservation.queue_position}` : '--';
    confirmedCopyNumber.textContent = reservation.book_copy ? `Copy No.${reservation.book_copy.copy_number}` : '-- (Pending Confirmation)';
    const creatorRole = reservation.created_by.role;
    const creatorName = `${reservation.created_by.lastname}, ${reservation.created_by.firstname}`;
    console.log('Creator Role:', creatorRole);
    console.log('Creator Name:', creatorName);
    reservationCreator.innerHTML = creatorRole === 'staff' 
        ? `<span class="font-bold text-gray-800">(Staff)</span>
           <span class="text-xs text-gray-800 font-light">${creatorName}</span>`
        : `Borrower`;

    // Status badge
    const statusBadge = document.getElementById('reservation-record-status-badge');
    statusBadge.innerHTML = getReservationStatusBadge(reservation.status);    

    // Timeline & Deadlines
    const completedAtElem = document.getElementById('reservation-record-completed-at');
    const completedAtContainer = document.getElementById('reservation-completion-date-container');
    const pickupDeadline = document.getElementById('reservation-record-pickup-deadline');
    const cancelledAtElem = document.getElementById('reservation-record-cancelled-at');
    const staffName = document.getElementById('reservation-record-staff-name');

    if (reservation.status === 'cancelled') {
        completedAtContainer.classList.add('hidden');
        completedAtElem.textContent = '--';
    } else {
        completedAtContainer.classList.remove('hidden');
        completedAtElem.textContent = reservation.completed_at ? formatDate(reservation.completed_at) : '-- (Pending)';
    }

    pickupDeadline && (pickupDeadline.textContent = reservation.pickup_deadline_date ? formatDate(reservation.pickup_deadline_date) : '-- (N/A)');

    // Cancelled At logic
    let cancelledAtContainer = null;
    if (cancelledAtElem) {
        cancelledAtContainer = cancelledAtElem.parentElement;
    }
    if (reservation.status === 'cancelled') {
        cancelledAtContainer.classList.remove('hidden');
        cancelledAtElem.textContent = reservation.cancelled_at ? formatDate(reservation.cancelled_at) : '--';
    } else {
        cancelledAtContainer.classList.add('hidden');
        cancelledAtElem.textContent = '--';
    }

    staffName && (staffName.textContent = reservation.staff ? reservation.staff.fullname : '--');

    // Transaction ID in header
    const transactionId = document.getElementById('reservation-transaction-id');
    transactionId.textContent = `#${reservation.id}`;

    const checkoutBtn = document.getElementById('checkout-reserved-book-button');
    const cancelBtn = document.getElementById('cancel-reservation-button');
 
    // Hide all by default
    checkoutBtn.classList.add('hidden');
    checkoutBtn.classList.remove('inline-flex');
    cancelBtn.classList.add('hidden');

    if (reservation.status === 'pending') {
        // Only show cancel for pending
        cancelBtn && cancelBtn.classList.remove('hidden');
    } else if (reservation.status === 'ready_for_pickup') {
        // Show both cancel and checkout for ready_for_pickup
        cancelBtn && cancelBtn.classList.remove('hidden');
        checkoutBtn && checkoutBtn.classList.remove('hidden');
        checkoutBtn && checkoutBtn.classList.add('inline-flex');
    }

    // Attach close modal listeners only once
    if (!reservationRecordModalListenersInitialized) {
        reservationRecordModalListenersInitialized = true;

        cancelBtn && (cancelBtn.onclick = async () => {
                
            const reservationId = reservation.id;

            const result = await cancelReservation(reservationId );
            if (result) {
                closeReservationRecordModal();
            }
        });

        checkoutBtn && (checkoutBtn.onclick = () => {
            const modal = document.getElementById('reservation-record-details-modal');
            const modalContent = document.getElementById('reservation-record-content');
            
            // Fade out reservation modal first
            modal.classList.remove('bg-opacity-50');
            modal.classList.add('bg-opacity-0');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                book.book_copy = reservation.book_copy;
                const context = 'main_table_view';
                openConfirmBorrowModal(borrower, book, true, context);
            }, 50);
        });

        // Close button
        const closeBtn = document.getElementById('close-reservation-record-modal');
        if (closeBtn) {
            closeBtn.onclick = closeReservationRecordModal;
        }

        // X icon
        const xBtn = document.getElementById('close-reservation-details-button');
        if (xBtn) {
            xBtn.onclick = closeReservationRecordModal;
        }
        // Click outside modal to close
        document.addEventListener('click', function(event) {
            const modalEl = document.getElementById('reservation-record-details-modal');
            if (event.target === modalEl) {
                closeReservationRecordModal();
            }
        });
    }
}

// Close modal function
function closeReservationRecordModal(withAnimation = true) {
    const modal = document.getElementById('reservation-record-details-modal');
    const modalContent = document.getElementById('reservation-record-content');
    
    const cancelledAtContainer = document.getElementById('reservation-record-cancelled-at').parentElement;
    const completedAtContainer = document.getElementById('reservation-completion-date-container');

    const cleanup = () => {
        cancelledAtContainer.classList.add('hidden');
        completedAtContainer.classList.remove('hidden');
    }
    
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    if (withAnimation) {
        setTimeout(() => {
            cleanup();
            modal.classList.add('hidden');
        }, 150); 
    } else {
        cleanup();
        modal.classList.add('hidden');
    }
}

initReservationRecordDetailListeners();