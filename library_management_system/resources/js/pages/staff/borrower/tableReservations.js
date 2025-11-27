import { openConfirmBorrowModal } from "../confirmBorrow";
import { formatDate } from '../../../utils.js';
import { cancelReservation } from '../../../api/staffTransactionHandler.js';
import { fetchBorrowerDetails } from '../../../api/borrowerHandler.js';
import { initializeBorrowerProfileUI } from './borrowerProfilePopulators.js';
import { closeConfirmReturnModal } from '../confirmReturn.js';

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
        
        const dateExpired = reservation.date_expired ? formatDate(reservation.date_expired) : '—';

        // Reservation queue position (if available)
        let queuePosition = '';

        if (reservation.status === 'pending') {
            // For pending reservations, we can use the reservation's position in the array + 1
             queuePosition = '#' + (reservation.queue_position || 'N/A');
        } else {
            queuePosition = '—';
        }
        // Status badge
        let statusBadge = '';
        if (reservation.status === 'pending') {
            statusBadge = `
                <span class="inline-flex items-center gap-1 w-full px-2 py-1 rounded-full text-xs font-semibold bg-yellow-200 text-yellow-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pending
                </span>
            `;
        } else if (reservation.status === 'ready_for_pickup') {
              statusBadge = `
                <span class="inline-flex items-center gap-1 w-full px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    Ready for Pickup
                </span>
            `;
        } else {
            statusBadge = `<span class="text-gray-500 text-xs">${reservation.status}</span>`;
        }

        // Actions: Pickup and Cancel buttons
        const pickupButton = `
            <button class="pickup-reservation-button cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-secondary hover:bg-secondary/90 text-white rounded-lg text-xs font-medium transition-all shadow-sm" 
                            data-reservation='${JSON.stringify({
                                reservation_id: reservation.id,
                                book_id: reservation.book.id,
                                book_title: reservation.book.title,
                                cover_image: reservation.book.cover_image,
                                author_firstname: reservation.book.author.firstname,
                                author_lastname: reservation.book.author.lastname,
                                genre_name: reservation.book.genre.name,
                                category_name: reservation.book.genre.category.name,
                                isbn: reservation.book.isbn,
                                publication_year: reservation.book.publication_year
                            }).replace(/'/g, "&#39;")}'>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Pickup
            </button>
        `;
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
                            <p class="font-semibold text-gray-800 truncate">${book?.title || 'Unknown'}</p>
                            <p class="text-xs text-gray-500">by ${authorName}</p>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-700  text-sm">${queuePosition}</td>
                <td class="py-3 px-4">${statusBadge}</td>
                <td class="py-3 px-4">${formatDate(reservation.created_at) }</td>
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
            
            const bookData = {
                id: reservationData.book_id,
                title: reservationData.book_title,
                cover_image: reservationData.cover_image,
                author_firstname: reservationData.author_firstname,
                author_lastname: reservationData.author_lastname,
                isbn: reservationData.isbn,
                publication_year: reservationData.publication_year,
                genre : {
                    name: reservationData.genre_name,
                    category: {
                        name: reservationData.category_name
                    }
                }
            };
         
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
