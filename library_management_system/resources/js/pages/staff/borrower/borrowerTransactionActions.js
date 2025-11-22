import { openConfirmReturnModal } from '../confirmReturn.js';
import { openConfirmRenewModal } from '../confirmRenew.js';

export function attachTransactionActions(tbody, borrowedBooks, borrower) {
    // Attach Return button listeners
    tbody.querySelectorAll('button.return-button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const txId = this.dataset.transactionId;
            const transaction = borrowedBooks.find(t => String(t.id) === String(txId));
            if (!transaction) return;
            // closeBorrowerModal(false);
            openConfirmReturnModal(borrower, transaction);
        });
    });

    // Attach Renew button listeners
    tbody.querySelectorAll('button.renew-button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const txId = this.dataset.transactionId;
            const transaction = borrowedBooks.find(t => String(t.id) === String(txId));
            if (!transaction) return;
            // closeBorrowerModal(false);
            openConfirmRenewModal(borrower, transaction);
            
        });
    });
}
