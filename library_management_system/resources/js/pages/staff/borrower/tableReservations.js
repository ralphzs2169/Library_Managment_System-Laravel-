import { openConfirmBorrowModal } from "../confirmBorrow";
import { formatDate, formatTime } from '../../../utils.js';
import { cancelReservation } from "../../../ajax/transactions/reservationHandler.js";
import { fetchBorrowerDetails } from '../../../ajax/borrowerHandler.js';
import { initializeBorrowerProfileUI } from './borrowerProfilePopulators.js';
import { closeConfirmReturnModal } from '../confirmReturn.js';
import { getReservationStatusBadge } from '../../../utils/statusBadge.js';


export function populateReservationsTable(modal, borrower) {
    const tbody = modal.querySelector('#active-reservations-tbody');
    const reservationsCountElem = modal.querySelector('#reservations-count');
    const reservationsHeaderCountElem = modal.querySelector('#reservations-header-count');

    if (!tbody) return;

    const reservations = borrower.active_reservations || [];
    const reservationsCount = reservations.length;

    // Set reservations count in tab badge
    if (reservationsCountElem) {
        reservationsCountElem.textContent = `${reservationsCount}`;
    }

    // Set reservations count in header badge (dynamic)
    if (reservationsHeaderCountElem) {
        reservationsHeaderCountElem.textContent = `${reservationsCount}`;
    }

    tbody.innerHTML = '';

    if (reservations.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-10 text-center text-gray-500 text-sm">
                    No active reservations
                </td>
            </tr>
        `;
        return;
    }

    reservations.forEach((reservation, index) => {
        // Find book details from borrower's active_borrows or fallback to reservations array
        let book = null;
        let authorName = '';
        let coverImage = '/images/no-cover.png';

        book = reservation.book;
        authorName = book.author ? `${book.author.firstname} ${book.author.lastname}` : 'Unknown Author';
        coverImage = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        
        const dateExpired = reservation.pickup_deadline_date ? formatDate(reservation.pickup_deadline_date) : '—';

        // Reservation queue position (if available)
        let queuePosition = '';

        if (reservation.status === 'pending') {
            // For pending reservations, we can use the reservation's position in the array + 1
             queuePosition = '#' + (reservation.queue_position || 'N/A');
        } else {
            queuePosition = '—';
        }

        // Actions: Pickup and Cancel buttons
        let pickupButton = '';
   
        if (reservation.status === 'ready_for_pickup') {
            if (borrower.can_borrow.result === 'success'){
                pickupButton = `
                    <button class="pickup-reservation-button cursor-pointer tracking-wide inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition-all shadow-sm" 
                        data-reservation='${JSON.stringify(reservation).replace(/'/g, "&#39;")}'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Checkout
                    </button>
                `; 
                
            } else {
              pickupButton = `
                <div class="relative inline-block group">
                    <button disabled class="cursor-not-allowed tracking-wider inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-300 text-gray-500 rounded-lg text-xs font-medium opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Checkout
                    </button>
                    <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                        ${borrower.can_borrow.message}
                        <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                    </div>
                </div>
            `;
            }
        } else {
            pickupButton = `
                <div class="relative inline-block group">
                    <button disabled class="cursor-not-allowed inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-300 text-gray-500 rounded-lg text-xs font-medium opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Pickup
                    </button>
                    <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                        This book is not yet ready for pickup
                        <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                    </div>
                </div>
            `;
        }

        const cancelButton = `
            <button class="cancel-reservation-button cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium transition-all shadow-sm" 
                    data-reservation-id="${reservation.id}" 
                    data-borrower-id="${borrower.id}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </button>
        `;

        const row = `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">${index + 1}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        <img src="${coverImage}" class="w-10 h-14 rounded-md object-cover shadow-sm flex-shrink-0 border border-gray-200">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 line-clamp-2">${book?.title || 'Unknown'}</p>
                            <p class="text-xs text-gray-500 mt-1">by ${authorName}</p>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-700  text-sm">${queuePosition}</td>
                <td class="py-3 px-4">${getReservationStatusBadge(reservation.status)}</td>
                <td class="py-3 px-4">
                    ${formatDate(reservation.created_at) }
                    <div class="text-xs text-gray-500">${formatTime(reservation.created_at)}</div>    
                </td>
                <td class="py-3 px-4">${dateExpired}</td>
           
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        ${pickupButton}
                        ${cancelButton}
                    </div>
                </td>
            </tr>
        `;

        tbody.innerHTML += row;
    });

    attachReservationActions(tbody, borrower);
}

// Attach event listeners to Pickup and Cancel buttons
export function attachReservationActions(tbody, borrower) {
    tbody.querySelectorAll('button.pickup-reservation-button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const reservationData = JSON.parse(this.dataset.reservation);
            const bookData = {...reservationData.book, book_copy: reservationData.book_copy};   
    
            openConfirmBorrowModal(borrower, bookData, true);
        });
    });

    tbody.querySelectorAll('button.cancel-reservation-button').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const reservationId = this.dataset.reservationId;
            const borrowerId = this.dataset.borrowerId;
           
            const result = await cancelReservation(reservationId);

            if (result) {
                // returnToBorrowerProfile();
                const modal = document.getElementById('borrower-profile-modal');
                const freshBorrowerDetails = await fetchBorrowerDetails(borrowerId);
                await initializeBorrowerProfileUI(modal, freshBorrowerDetails, true, 'reservations-tab');
                closeConfirmReturnModal();
            }
        });
    });
}
