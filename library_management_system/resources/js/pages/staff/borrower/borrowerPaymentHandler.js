export function attachPaymentActions(tbody) {
    tbody.querySelectorAll('button.pay-penalty-button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const txId = this.dataset.transactionId;
            // TODO: Implement pay penalty functionality
            handlePayPenalty(txId);
        });
    });
}

function handlePayPenalty(transactionId) {
    console.log('Pay penalty for transaction:', transactionId);
    // TODO: Open payment modal/flow
    // - Show payment confirmation
    // - Process payment
    // - Update penalty status
    // - Refresh borrower profile
}
