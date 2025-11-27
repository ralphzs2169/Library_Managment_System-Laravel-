import { updatePenalty } from '../../api/borrowerHandler.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js';
import { fetchBorrowerDetails } from '../../api/borrowerHandler.js';
import { clearInputError } from '../../helpers.js';
import { renderPenaltyStatusBadge } from './borrower/tablePenalties.js';

function renderPenaltyReasonBadge(type) {
    switch (type) {
        case 'late_return':
            return `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Late Return
                </span>
            `;
        case 'lost_book':
            return `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Lost
                </span>
            `;
        case 'damaged_book':
            return `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700">
                    <img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">
                    Damaged
                </span>
            `;
        default:
            return '';
    }
}

export function attachPaymentActions(tbody, borrower) {
    tbody.querySelectorAll('button.pay-penalty-button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const transaction = JSON.parse(this.dataset.transaction);
         
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
    content.querySelector('#confirm-payment-borrower-name').textContent = borrower.fullname || '';
    let idNumber = borrower.role === 'student'
        ? borrower.students?.student_number
        : borrower.teachers?.employee_number;
    content.querySelector('#confirm-payment-borrower-id').textContent = idNumber || '';

    // Book & penalty info
    const penalty = transaction.penalty || (transaction.penalties?.[0] || {});
    const bookCopy = transaction.book_copy || {};
    const book = bookCopy.book || {};
    
    const amount = (transaction.penalty.status === 'partially_paid')
        ? Number(penalty.remaining_amount || 0).toFixed(2)
        : Number(penalty.amount || 0).toFixed(2);
    console.log('Opening payment modal for penalty amount:', transaction.penalty);
    content.querySelector('#confirm-payment-book-cover').src = book.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
    content.querySelector('#confirm-payment-book-title').textContent = book.title || '';
    content.querySelector('#confirm-payment-book-author').textContent = book.author ? `${book.author.firstname} ${book.author.lastname}` : '';
    content.querySelector('#confirm-payment-book-isbn').textContent = book.isbn || '';
    content.querySelector('#confirm-payment-copy-number').textContent = `#${bookCopy.copy_number}` || '';
    content.querySelector('#confirm-payment-penalty-amount').textContent = `₱${amount}`;

    const amountLabel = content.querySelector('#amount-label');

    if(transaction.penalty.status === 'partially_paid'){
        amountLabel.textContent = 'REMAINING AMOUNT';
    } else {
        amountLabel.textContent = 'AMOUNT';
    }
    // Reason badge
    content.querySelector('#confirm-payment-penalty-type').innerHTML = renderPenaltyReasonBadge(penalty.type);

    // Status badge
    document.getElementById('confirm-payment-penalty-status').innerHTML = renderPenaltyStatusBadge(penalty.status);

    const amountInput = content.querySelector('#amount');
    const paymentForm = content.querySelector('#confirm-payment-form');

    // Remove previous submit listeners by cloning the form
    const newPaymentForm = paymentForm.cloneNode(true);
    paymentForm.parentNode.replaceChild(newPaymentForm, paymentForm);

    // Add submit listener to the new form
    newPaymentForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        amountInput.blur();
        newPaymentForm.querySelector('#amount').blur();
        await processPenaltyPayment(transaction.penalty.id, borrower);
    });

    // Remove previous button onclick to avoid duplicate calls
    newPaymentForm.querySelector('#confirm-payment-button').onclick = null;

    // Button actions
    const closeBtn = content.querySelector('#close-confirm-payment-modal');
    const cancelBtn = newPaymentForm.querySelector('#cancel-payment-button');
    
    closeBtn.onclick = () => closePaymentModal();
    cancelBtn.onclick = () => closePaymentModal();

    document.addEventListener('click', function(event) {
        if (event.target === modal) {
            closePaymentModal(borrower);
        }
    });

    // Add input/focus listeners for error clearing
    ['input', 'focus'].forEach(event =>
        newPaymentForm.querySelector('#amount').addEventListener(event, () => {
            clearInputError(newPaymentForm.querySelector('#amount'));
            console.log('Cleared input error on amount input');
        })
    );
}

export function closePaymentModal() {
    const modal = document.getElementById('confirm-payment-modal');
    const content = document.getElementById('confirm-payment-content');

    const amountInput = content.querySelector('#amount');
    amountInput.value = '';
    clearInputError(amountInput);

    if (!modal || !content) return;

    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 150);
}

async function processPenaltyPayment(penaltyId, currentBorrower) {
    const form = document.getElementById('confirm-payment-form');
    const formData = new FormData(form);

    formData.set('penalty_id', penaltyId);
    formData.set('amount', formData.get('amount') || '');
    
    const result = await updatePenalty(formData, form.id);
    if (result) {
            const modal = document.getElementById('borrower-profile-modal');
            const freshBorrower = await fetchBorrowerDetails(currentBorrower.id);
            await initializeBorrowerProfileUI(modal, freshBorrower, true, 'penalties-tab');
            closePaymentModal(freshBorrower);
        }
}
