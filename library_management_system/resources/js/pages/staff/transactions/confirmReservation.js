// import { showBookSelectionContent, restoreProfileContent } from '../bookSelection.js';
import { clearInputError } from '../../../helpers.js';
import { returnToBookSelection } from '../confirmBorrow.js';
import { addReservation } from '../../../ajax/transactions/reservationHandler.js';
import { openBorrowerProfileModal } from '../borrower/borrowerProfileModal.js';
import { populateRoleBadge } from '../../librarian/utils/popupDetailsModal.js';

let currentReserver = null;
let currentBook = null;

export async function initializeConfirmReservationModal(modal, reserver, book) {

    currentReserver = reserver;
    currentBook = book;

    // Reserver info
    if (reserver) {
        const reserverName = modal.querySelector('#confirm-reserver-name');
        const reserverId = modal.querySelector('#confirm-reserver-id');
        const reserverInitials = modal.querySelector('#confirm-reserver-initials');

        if (reserverName) reserverName.textContent = reserver.fullname || 'N/A';
        if (reserverId) {
            const idNumber = reserver.role === 'student'
                ? reserver.students?.student_number
                : reserver.teachers?.employee_number;
            reserverId.textContent = idNumber || 'N/A';
        }
        if (reserverInitials) {
            const initials = (reserver.firstname?.charAt(0) || '') + (reserver.lastname?.charAt(0) || '');
            reserverInitials.textContent = initials || '--';
        }
    }

    populateRoleBadge(modal, '#reserver-role-badge', reserver);

    // Book info
    if (book) {
        modal.querySelector('#confirm-reserve-book-cover').src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        modal.querySelector('#confirm-reserve-book-title').textContent = book.title || 'Untitled';
        modal.querySelector('#confirm-reserve-book-title').title = book.title || 'Untitled';
        modal.querySelector('#confirm-reserve-book-author').textContent = `by ${(book.author?.firstname || '') + ' ' + (book.author?.lastname || '')}`;
        modal.querySelector('#confirm-reserve-book-isbn').textContent = book.isbn || 'N/A';
        modal.querySelector('#confirm-reserve-book-year').textContent = book.publication_year || 'N/A';
        modal.querySelector('#confirm-reserve-book-category').textContent = book.genre?.category?.name || 'N/A';
        modal.querySelector('#confirm-reserve-book-genre').textContent = book.genre?.name || 'N/A';

        const queuePosition = book.next_queue_position;
        const reserverName = reserver.fullname || 'the user';
        let queueText = '';
        
        if (queuePosition === 1) {
            queueText = `${reserverName} will be first in line for this reservation.`;
        } else {
            const ahead = queuePosition - 1;
            queueText = `${reserverName} will be #${queuePosition} in the queue. There ${ahead === 1 ? 'is 1 reservation' : `are ${ahead} reservations`} ahead of them.`;
        }

        // Add it to modal
        modal.querySelector('#reservation-queue-position').textContent = queueText;


    }

    // Confirm button
    const confirmBtn = modal.querySelector('#confirm-reserve-button');
    if (confirmBtn) {
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleConfirmReservation();
        });
    }

    // Cancel button
    const cancelBtn = modal.querySelector('#cancel-reserve-button');
    if (cancelBtn) {
        cancelBtn.onclick = closeConfirmReservationModal;
    }

    // Back button
    const backBtn = modal.querySelector('#back-to-reserve-book-button');
    
    if (backBtn) {
        backBtn.onclick = () => returnToBookSelection('reservation', currentReserver);
    }

    // Close button
    const closeBtn = modal.querySelector('#close-confirm-reserve-modal');
    if (closeBtn) {
        closeBtn.onclick = closeConfirmReservationModal;
    }

    // Click outside modal to close
    document.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeConfirmReservationModal();
        }
    });
}

export function openConfirmReservationModal(reserver, book) {
    currentReserver = reserver;
    currentBook = book;
    
    const modal = document.getElementById('confirm-reservation-modal');
    const modalContent = modal.querySelector('#confirm-reservation-content');

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

    initializeConfirmReservationModal(modal, currentReserver, currentBook);
}

export function closeConfirmReservationModal(withAnimation = true) {
    const modal = document.getElementById('confirm-reservation-modal');
    const modalContent = document.getElementById('confirm-reservation-content');
    clearInputError(document.getElementById('pickup_deadline'));
    clearInputError(document.getElementById('reserve_notes'));
    
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

async function handleConfirmReservation() {
    // Prepare form data
    const formData = new FormData();
    formData.append('book_id', currentBook.id);
    formData.append('reserver_id', currentReserver.id);

    // Call the reservation handler
    const result = await addReservation(formData);
    
    if (result) {
        closeConfirmReservationModal();
        setTimeout(() => {
            openBorrowerProfileModal(currentReserver.id, true, 'reservations-tab');
        }, 160); 
    } 
}
