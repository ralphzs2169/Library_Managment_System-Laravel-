import { openConfirmReservationModal } from "../staff/transactions/confirmReservation";
import { cancelReservation } from "../../ajax/transactions/reservationHandler";
const reserveBookButton = document.getElementById('reserve-book-button');
reserveBookButton?.addEventListener('click', function() {
    let book = JSON.parse(this.getAttribute('data-book'));
    const reserver = JSON.parse(this.getAttribute('data-reserver'));
    const queuePosition = this.getAttribute('data-queue-position');

    book = { ...book, next_queue_position: queuePosition ? parseInt(queuePosition) : null };
    openConfirmReservationModal(reserver, book);
});

const cancelReservationButton = document.getElementById('cancel-reservation-button');

cancelReservationButton?.addEventListener('click', async function() {
    const reservationId = this.getAttribute('data-reservation-id');
    const borrowerId = this.getAttribute('data-borrower-id');
    await cancelReservation(reservationId, borrowerId);
});