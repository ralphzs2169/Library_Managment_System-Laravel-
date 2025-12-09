import { fetchSettings } from '../../../ajax/settingsHandler.js';
import { showWarning } from '../../../utils/alerts.js';
import { openConfirmReturnModal } from '../confirmReturn.js';
import { openConfirmRenewModal } from '../confirmRenew.js';
import { closeBorrowerModal } from './borrowerProfileModal.js';
import { formatDate, formatTime } from '../../../utils.js';
import { getBorrowingStatusBadge } from '../../../utils/statusBadge.js';

export async function populateCurrentlyBorrowedBooks(modal, borrower) {
    const tbody = modal.querySelector('#currently-borrowed-tbody');
    const borrowedCountElem = modal.querySelector('#borrowed-count');
    const borrowedHeaderCountElem = modal.querySelector('.bg-accent\\/10.text-accent.font-bold'); // header count badge

    const settings = await fetchSettings(); 

    if (!settings) {
        showWarning('Something went wrong', 'Unable to load application settings. Some features may not work as expected.');
        return;
    }
    
    const dueReminderThreshold = parseInt(settings['notifications.reminder_days_before_due']);
    if (!dueReminderThreshold) {
        showWarning('Configuration Issue', 'Due reminder threshold is not properly configured. Please check application settings.');
        closeBorrowerModal();
        return;
    }

    if (!tbody) return;
    
    const borrowedBooks = borrower.active_borrows || [];
    const borrowedCount = borrowedBooks.length;

    // Set borrowed count in tab badge
    if (borrowedCountElem) {
        borrowedCountElem.textContent = `${borrowedCount}`;
    }

    // Set borrowed count in header badge (dynamic)
    if (borrowedHeaderCountElem) {
        if (borrower.role === 'student') {
            borrowedHeaderCountElem.textContent = `${borrowedCount} / 3`;
        } else {
            borrowedHeaderCountElem.textContent = `${borrowedCount}`;
        }
    }
    
    tbody.innerHTML = '';
    
    if (borrowedBooks.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="py-10 text-center">
                        <p class="text-gray-500 text-md font-medium mb-2">No currently borrowed books</p>
                </td>
            </tr>
        `;
        return;
    }
    
    borrowedBooks.forEach((transaction, index) => {
   
        const book = transaction.book_copy.book;
        const copyNumber = transaction.book_copy.copy_number;
        const author = book?.author;
        const dueDate = new Date(transaction.due_at);

        const status = transaction.status;
        const daysUntilDue = transaction.days_until_due;
        const isReturned = transaction.returned_at !== null;

        let borderClass = '';

        if (status === 'overdue') {
            borderClass = 'border-l-4 border-l-red-500';
        } else if (status === 'borrowed' && daysUntilDue <= dueReminderThreshold) {
            borderClass = 'border-l-4 border-l-yellow-400';
        } else if (status === 'borrowed') {
            borderClass = 'border-l-4 border-l-blue-500';
        }

        const coverImage = book?.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        const authorName = author ? `${author.firstname} ${author.lastname}` : 'Unknown Author';

        const { renewButton, returnButton } = generateTransactionButtons(transaction, isReturned);

        const row = `
            <tr class="hover:bg-gray-50 transition-colors ${borderClass}">
                <td class="py-3 px-4 text-gray-600 font-medium">${index + 1}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        <img src="${coverImage}" class="w-10 h-14 rounded-md object-cover shadow-sm flex-shrink-0 border border-gray-200">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 line-clamp-2">${book?.title || 'Unknown'}</p>
                            <p class="text-xs text-gray-700">Copy #${copyNumber || 'N/A'}</p>
                            <p class="text-xs text-gray-500 mt-1">by ${authorName}</p>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-700 text-sm">
                    ${formatDate(transaction.borrowed_at)}
                    <div class="text-xs text-gray-500">${formatTime(transaction.created_at)}</div>
                    </td>
                <td class="py-3 px-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-sm text-gray-800">${formatDate(dueDate)}</span>
                    </div>
                </td>
                <td class="py-3">
                    <span class="inline-flex items-center w-full gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold">
                        ${getBorrowingStatusBadge(transaction)}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        ${renewButton}
                        ${returnButton}
                    </div>
                </td>
            </tr>
        `;

        tbody.innerHTML += row;
    });


    attachTransactionActions(tbody, borrowedBooks, borrower);
}

// Generate Renew and Return buttons based on transaction status
function generateTransactionButtons(transaction, isReturned) {
    let renewButton = '';
    let returnButton = '';

    // Can renew if not returned, status is 'borrowed', and not overdue
    if (transaction.can_renew.result === 'success') {
        renewButton = `
            <button class="renew-button cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-transaction-id="${transaction.id}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Renew
            </button>
        `;
    } else {
        const tooltip = transaction.can_renew.message;
        renewButton = `
            <div class="relative inline-block group">
                <button disabled class="cursor-not-allowed inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-300 text-gray-500 rounded-lg text-xs font-medium opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Renew
                </button>
                <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                    ${tooltip}
                    <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                </div>
            </div>
        `;
    }

    // Return button logic
    if (!isReturned) {
        returnButton = `
            <button class="return-button cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition-all shadow-sm" data-transaction-id="${transaction.id}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Return
            </button>
        `;
    } else {
        const tooltip = 'Book already returned';
        returnButton = `
            <div class="relative group">
                <button disabled class="cursor-not-allowed inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-300 text-gray-500 rounded-lg text-xs font-medium opacity-50" title="${tooltip}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                    Return
                </button>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    ${tooltip}
                </div>
            </div>
        `;
    }

    return { renewButton, returnButton };
}


// Attach event listeners to Renew and Return buttons
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
