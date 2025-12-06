import { processPenalty } from '../../ajax/penaltyHandler.js';
import { initializeBorrowerProfileUI } from './borrower/borrowerProfilePopulators.js';
import { fetchBorrowerDetails } from '../../ajax/borrowerHandler.js';
import { clearInputError } from '../../helpers.js';
import { getPenaltyStatusBadge, getPenaltyTypeBadge } from '../../utils/statusBadge.js';
import { populateRoleBadge } from '../librarian/utils/popupDetailsModal.js';

let confirmPaymentModalListenersInitialized = false;

export function openPaymentModal(borrower, transaction, context = 'profile_view') {
    const modal = document.getElementById('confirm-payment-modal');
    const content = document.getElementById('confirm-payment-content');
    if (!modal || !content) return;

    // Handle Back Button Visibility and Context
    const backBtn = modal.querySelector('#payment-back-to-penalty-details');
    if (backBtn) {
        backBtn.dataset.context = context;
        if (context === 'main_table_view') {
            backBtn.classList.remove('hidden');
        } else {
            backBtn.classList.add('hidden');
        }
    }

    // Reset to initial hidden state before showing
    modal.classList.add('bg-opacity-0');
    modal.classList.remove('bg-opacity-50');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');

    // Show modal
    modal.classList.remove('hidden');
    
    // Use double requestAnimationFrame for smoother animation
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });
    });

    // Borrower info
    const initials = (borrower.firstname?.charAt(0) || '') + (borrower.lastname?.charAt(0) || '');
    content.querySelector('#confirm-payment-borrower-initials').textContent = initials || '--';
    content.querySelector('#confirm-payment-borrower-name').textContent = borrower.fullname || '';
    let idNumber = borrower.role === 'student'
        ? borrower.students?.student_number
        : borrower.teachers?.employee_number;
    content.querySelector('#confirm-payment-borrower-id').textContent = idNumber || '';

    // Populate role badge
    populateRoleBadge(modal, '#confirm-payment-borrower-role-badge', borrower);

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
    content.querySelector('#confirm-payment-book-title').title = book.title || '';
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
    content.querySelector('#confirm-payment-penalty-type').innerHTML = getPenaltyTypeBadge(penalty.type);

    // Status badge
    document.getElementById('confirm-payment-penalty-status').innerHTML = getPenaltyStatusBadge(penalty.status);

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
        processPenaltyPayment(transaction.penalty.id, borrower);
    });

    // Remove previous button onclick to avoid duplicate calls
    newPaymentForm.querySelector('#confirm-payment-button').onclick = null;

    // Initialize listeners once
    if (!confirmPaymentModalListenersInitialized) {
        confirmPaymentModalListenersInitialized = true;

        // Back button
        if (backBtn) {
            backBtn.onclick = returnToPreviousModal;
        }

        // Button actions
        const closeBtn = content.querySelector('#close-confirm-payment-modal');
        if (closeBtn) {
            closeBtn.onclick = () => closePaymentModal();
        }

        document.addEventListener('click', function(event) {
            if (event.target === modal) {
                closePaymentModal();
            }
        });
    } else {
        // If listeners already initialized, just update the context on the back button
        if (backBtn) {
            backBtn.dataset.context = context;
        }
    }

    const cancelBtn = newPaymentForm.querySelector('#cancel-payment-button');
    if (cancelBtn) {
        cancelBtn.onclick = () => closePaymentModal();
    }

    // Add input/focus listeners for error clearing
    ['input', 'focus'].forEach(event =>
        newPaymentForm.querySelector('#amount').addEventListener(event, () => {
            clearInputError(newPaymentForm.querySelector('#amount'));
            console.log('Cleared input error on amount input');
        })
    );
}

export function closePaymentModal(withAnimation = true) {
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

    if (withAnimation) {
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 50);
    } else {
        modal.classList.add('hidden');
    }
}

function returnToPreviousModal() {
    const backBtn = document.getElementById('payment-back-to-penalty-details');
    const context = backBtn.dataset.context || 'profile_view';

    if (context === 'main_table_view') {
        const modal = document.getElementById('confirm-payment-modal');
        const modalContent = document.getElementById('confirm-payment-content');
        
        // Clear amount input
        const amountInput = modalContent.querySelector('#amount');
        if (amountInput) {
            amountInput.value = '';
            clearInputError(amountInput);
        }
        
        // Fade out confirm payment modal
        modal.classList.remove('bg-opacity-50');
        modal.classList.add('bg-opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            
            const detailsModal = document.getElementById('penalty-record-details-modal');
            const detailsModalContent = document.getElementById('penalty-record-content');

            if (detailsModal && detailsModalContent) {
                // Reset to initial hidden state
                detailsModal.classList.add('bg-opacity-0');
                detailsModal.classList.remove('bg-opacity-50');
                detailsModalContent.classList.add('scale-95', 'opacity-0');
                detailsModalContent.classList.remove('scale-100', 'opacity-100');
                
                detailsModal.classList.remove('hidden');

                // Trigger animation with double rAF
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        detailsModal.classList.remove('bg-opacity-0');
                        detailsModal.classList.add('bg-opacity-50');
                        detailsModalContent.classList.remove('scale-95', 'opacity-0');
                        detailsModalContent.classList.add('scale-100', 'opacity-100');
                    });
                });
            }
        },50);
    } else {
        closePaymentModal();
    }
}

async function processPenaltyPayment(penaltyId, currentBorrower) {
    const form = document.getElementById('confirm-payment-form');
    const formData = new FormData(form);

    formData.set('penalty_id', penaltyId);
    formData.set('amount', formData.get('amount') || '');
    
    const performedBy = await processPenalty(formData, form.id);

    if (performedBy === 'staff') {
        const modal = document.getElementById('borrower-profile-modal');
        const freshBorrower = await fetchBorrowerDetails(currentBorrower.id);
        await initializeBorrowerProfileUI(modal, freshBorrower, true, 'penalties-tab');
        closePaymentModal();
    } else if (performedBy === 'librarian') {
        const backBtn = document.getElementById('payment-back-to-penalty-details');
        const context = backBtn.dataset.context || 'profile_view';

        if (context === 'main_table_view') {
            closePaymentModal();
            // Dashboard reloads automatically via processPenalty
        } else {
            closePaymentModal();
        }
    }
}
