import { updatePenalty } from '../../../api/borrowerHandler.js';
import { initializeBorrowerProfileUI } from './borrowerProfilePopulators.js';
import { fetchBorrowerDetails } from '../../../api/borrowerHandler.js';

export function attachPaymentActions(tbody, borrower) {
    tbody.querySelectorAll('button.pay-penalty-button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const txId = this.dataset.transactionId;
            const transaction = borrower.transactions_with_penalties.find(tx => String(tx.id) === String(txId));
            if (transaction) {
                // Close borrower profile modal before opening payment modal
         
                openPaymentModal(borrower, transaction);
            }
        });
    });
}

function openPaymentModal(borrower, transaction) {
    const modal = document.getElementById('confirm-payment-modal');
    const content = document.getElementById('confirm-payment-content');
    if (!modal || !content) return;

    // Show modal
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    });

    // Borrower info
    const initials = (borrower.firstname?.charAt(0) || '') + (borrower.lastname?.charAt(0) || '');
    content.querySelector('#confirm-payment-borrower-initials').textContent = initials || '--';
    content.querySelector('#confirm-payment-borrower-name').textContent = borrower.full_name || '';
    let idNumber = borrower.role === 'student'
        ? borrower.students?.student_number
        : borrower.teachers?.employee_number;
    content.querySelector('#confirm-payment-borrower-id').textContent = idNumber || '';

    // Book & penalty info
    const penalty = transaction.penalty || (transaction.penalties?.[0] || {});
    const bookCopy = transaction.book_copy || {};
    const book = bookCopy.book || {};
    console.log(penalty);
    content.querySelector('#confirm-payment-book-cover').src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
    content.querySelector('#confirm-payment-book-title').textContent = book.title || '';
    content.querySelector('#confirm-payment-book-author').textContent = book.author ? `${book.author.firstname} ${book.author.lastname}` : '';
    content.querySelector('#confirm-payment-book-isbn').textContent = book.isbn || '';
    content.querySelector('#confirm-payment-copy-number').textContent = `#${bookCopy.copy_number}` || '';
    content.querySelector('#confirm-payment-penalty-type').innerHTML = renderPenaltyTypeBadge(penalty.type);
    content.querySelector('#confirm-payment-penalty-amount').textContent = `₱${parseFloat(penalty.remaining_amount || 0).toFixed(2)}`;
    document.getElementById('confirm-payment-penalty-status').innerHTML = renderPenaltyStatusBadge(penalty.status);

    // Button actions
    content.querySelector('#close-confirm-payment-modal').onclick = () => closePaymentModal(borrower);
    content.querySelector('#cancel-payment-button').onclick = () => closePaymentModal(borrower);
    content.querySelector('#back-to-borrower-profile-button').onclick = () => closePaymentModal(borrower);
    document.addEventListener('click', function(event) {
        if (event.target === modal) {
            closePaymentModal(borrower);
        }
    });

    content.querySelector('#confirm-payment-button').onclick = async function () {
        processPenaltyPayment(transaction.penalty.id, borrower);
        
        // Optionally, refresh borrower profile
        // location.reload();
    };
}

function renderPenaltyStatusBadge(status) {
    switch (status) {
        case 'unpaid':
            return `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Unpaid
                </span>
            `;

        case 'paid':
            return `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M5 13l4 4L19 7"/>
                    </svg>
                    Paid
                </span>
            `;

        case 'partially_paid':
            return `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M12 6v6m0 6h.01M4 12h16"/>
                    </svg>
                    Partially Paid
                </span>
            `;

        case 'cancelled':
            return `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelled
                </span>
            `;

        default:
            return `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                    Unknown
                </span>
            `;
    }
}

function renderPenaltyTypeBadge(type) {
    const label = formatPenaltyType(type);

    return `
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
            ${label}
        </span>
    `;
}

export function closePaymentModal(borrower) {
    const modal = document.getElementById('confirm-payment-modal');
    const content = document.getElementById('confirm-payment-content');
    if (!modal || !content) return;
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        // Reopen borrower profile modal
        // const borrowerModal = document.getElementById('borrower-profile-modal');
        // if (borrowerModal && borrower) {
        //     borrowerModal.classList.remove('hidden');
        // }
    }, 150);
}

function formatPenaltyType(type) {
    switch (type) {
        case 'late_return': return 'Late Return';
        case 'lost_book': return 'Lost Book';
        case 'damaged_book': return 'Damaged Book';
        default: return type ? type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : '';
    }
}

async function processPenaltyPayment(penaltyId, currentBorrower) {
    const form = document.getElementById('confirm-payment-form');
    const formData = new FormData(form);

    formData.set('penalty_id', penaltyId);
    formData.set('amount', formData.get('amount') || '');
    
    const result = await updatePenalty(formData, form.id);
    if (result) {
            const modal = document.getElementById('borrower-profile-modal');
            const { borrower, dueReminderThreshold } = await fetchBorrowerDetails(currentBorrower.id)
            await initializeBorrowerProfileUI(modal, borrower, dueReminderThreshold, 'penalties-tab');
            closePaymentModal(borrower);
        }
}
