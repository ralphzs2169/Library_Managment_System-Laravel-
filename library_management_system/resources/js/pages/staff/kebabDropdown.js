import { openConfirmReturnModal } from './confirmReturn.js';
import { openConfirmRenewModal } from './confirmRenew.js';
import { openPaymentModal } from './confirmPayment.js';
import { cancelPenalty } from '../../ajax/penaltyHandler.js';
import { openConfirmBorrowModal } from './confirmBorrow.js';
import { cancelReservation } from '../../ajax/reservationHandler.js';

document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', function (e) {
        // Kebab button click
        console.log('Kebab button clicked');
        if (e.target.closest('.kebab-menu-btn')) {
            const btn = e.target.closest('.kebab-menu-btn');
            const wrapper = btn.parentElement;
            const dropdown = wrapper.querySelector('.kebab-menu-dropdown');
            const arrow = dropdown.querySelector('.kebab-menu-arrow');

            // Close all other kebab menus
            document.querySelectorAll('.kebab-menu-dropdown').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });

            // Temporarily show dropdown for measurement
            dropdown.classList.remove('hidden');
            dropdown.style.visibility = 'hidden';
            dropdown.style.display = 'block';

            // Measure dropdown height (for 3 options)
            const dropdownHeight = dropdown.offsetHeight || 120;
            const btnRect = btn.getBoundingClientRect();
            const spaceBelow = window.innerHeight - btnRect.bottom;
            const spaceAbove = btnRect.top;

            // Restore visibility
            dropdown.style.visibility = '';
            dropdown.style.display = '';

            // Reset styles
            dropdown.style.top = '';
            dropdown.style.bottom = '';

            // Arrow logic
            if (arrow) {
                if (spaceBelow >= dropdownHeight + 50 || spaceBelow > spaceAbove) {
                    // Show below, arrow points up (border-b)
                    dropdown.style.top = '110%';
                    dropdown.style.bottom = 'auto';
                    arrow.classList.add('border-b-gray-900');
                    arrow.classList.remove('top-full');
                    arrow.classList.add('bottom-full');
                    arrow.classList.remove('border-t-gray-900');
                } else {
                    // Show above, arrow points down (border-t)
                    dropdown.style.top = 'auto';
                    dropdown.style.bottom = '110%';
                    arrow.classList.add('top-full');
                    arrow.classList.remove('bottom-full');
                    arrow.classList.add('border-t-gray-900');
                    arrow.classList.remove('border-b-gray-900');
                }
            }

            // Actually show dropdown
            dropdown.classList.remove('hidden');
            return;
        }

        // Kebab action click
        if (e.target.closest('.kebab-action-btn')) {
            const actionBtn = e.target.closest('.kebab-action-btn');
            const action = actionBtn.dataset.action;

            const memberDataString = actionBtn.dataset.member ?? '{}';
            const member = JSON.parse(memberDataString);

            // Close all kebab menus
            document.querySelectorAll('.kebab-menu-dropdown').forEach(d => d.classList.add('hidden'));

            // TODO: Implement modal logic for each action
            if (action === 'renew') {
                const transaction = JSON.parse(actionBtn.dataset.transaction);
                openConfirmRenewModal(member, transaction);
            } else if (action === 'return') {
                const transaction = JSON.parse(actionBtn.dataset.transaction);
                openConfirmReturnModal(member, transaction);
            } else if (action === 'lost') {
                // openReportLostModal(transactionId);
            } else if (action === 'settle') {
                const transaction = JSON.parse(actionBtn.dataset.penalty); 
                openPaymentModal(member, transaction);  
            } else if (action === 'cancelPenalty') {
                const penaltyId =   actionBtn.dataset.penaltyId;
                const memberId = actionBtn.dataset.memberId;
                cancelPenalty(penaltyId, memberId);
            } else if (action === 'fulfill') {
                const bookCopy = JSON.parse(actionBtn.dataset.bookCopy);
                const book = JSON.parse(actionBtn.dataset.book);
                book.book_copy = { ...bookCopy };
                
                openConfirmBorrowModal(member, book, true);
            } else if (action === 'cancelReservation') {
                const reservationId = actionBtn.dataset.reservationId;
                const memberId = actionBtn.dataset.memberId;
                cancelReservation(reservationId, memberId);
            }
            return;
        }


        // Click outside closes all kebab menus
        document.querySelectorAll('.kebab-menu-dropdown').forEach(d => d.classList.add('hidden'));
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.kebab-menu-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });
});