import { attachPaymentActions } from "../confirmPayment";
import { formatDate } from '../../../utils.js';

export function populateActivePenalties(modal, borrower) {
    const tbody = modal.querySelector('#active-penalties-tbody');
    const penaltiesCountElem = modal.querySelector('#penalties-count');
    const penaltiesHeaderCountElem = modal.querySelector('#penalties-header-count');
    
    if (!tbody) return;

    const penaltyTransactions = (borrower.transactions_with_penalties || []);
    const penaltiesCount = penaltyTransactions.length;

    // Set penalties count in tab badge
    if (penaltiesCountElem) {
        penaltiesCountElem.textContent = `${penaltiesCount}`;
    }

    // Set penalties count in header badge (dynamic)
    if (penaltiesHeaderCountElem) {
        penaltiesHeaderCountElem.textContent = `${penaltiesCount}`;
    }
    
    tbody.innerHTML = '';
    
    if (penaltyTransactions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-10 text-center text-gray-500 text-sm">
                    No active penalties
                </td>
            </tr>
        `;
        return;
    }

    penaltyTransactions.forEach((transaction, index) => {

        const book = transaction.book_copy?.book;
        const copyNumber = transaction.book_copy?.copy_number;
        const author = book?.author;
        const returnedDate = new Date(transaction.returned_at);
        
        // Reason badge (unchanged)
        let reasonBadge = '';
        const penaltyType = transaction.penalty.type;
        const status = transaction.penalty.status;

        if (penaltyType === 'late_return') {
            reasonBadge = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Late Return
                </span>
            `;
        } else if (penaltyType === 'lost_book') {
            reasonBadge = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Lost
                </span>
            `;
        } else if (penaltyType === 'damaged_book') {
            reasonBadge = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700">
                    <img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">
                    Damaged
                </span>
            `;
        }

        // Border color based on penalty status
        let borderClass = '';
        switch (status) {
            case 'unpaid':
                borderClass = 'border-l-4 border-l-red-500';
                break;
            case 'partially_paid':
                borderClass = 'border-l-4 border-l-orange-400';
                break;
            case 'paid':
                borderClass = 'border-l-4 border-l-green-500';
                break;
            case 'cancelled':
                borderClass = 'border-l-4 border-l-gray-400';
                break;
            default:
                borderClass = '';
        }

        const coverImage = book?.cover_image ? `/storage/${book.cover_image}` : '/images/no-cover.png';
        const authorName = author ? `${author.firstname} ${author.lastname}` : 'Unknown Author';
        const returnedDateText = returnedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const statusBadge = renderPenaltyStatusBadge(status);

        let amount = 0;
        if (status === 'unpaid') {
            amount = transaction.penalty.amount;
        } else if (status === 'partially_paid') {
            amount = transaction.penalty.remaining_amount;
        }
        
        const row = `
            <tr class="hover:bg-gray-50 transition-colors ${borderClass}">
                <td class="py-3 px-4 text-gray-600 font-medium">${index + 1}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3 ">
                        <img src="${coverImage}" class="w-10 h-14 rounded-md object-cover shadow-sm flex-shrink-0 border border-gray-200">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 truncate">${book?.title || 'Unknown'}</p>
                            <p class="text-xs text-gray-500">Copy #${copyNumber || 'N/A'} • ${authorName}</p>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4 text-gray-700 text-sm">${returnedDateText}</td>
                <td class="py-3 px-4">${reasonBadge}</td>
                <td class="py-3 px-4">
                    <span class="text-red-700 font-bold text-sm">₱${Number(amount).toFixed(2)}</span>
                </td>
                <td class="py-3 px-4">
                    ${statusBadge}
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        <button class="pay-penalty-button cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs tracking-wide font-medium transition-all shadow-sm" data-transaction="${JSON.stringify(transaction).replace(/"/g, '&quot;')}">
                            <img src="/build/assets/icons/pay.svg" alt="Pay Fine Icon" class="w-4 h-4">
                            Settle
                        </button>
                    </div>
                    
                </td>
            </tr>
        `;
        
        tbody.innerHTML += row;
    });
    
    attachPaymentActions(tbody, borrower);
}

export function renderPenaltyStatusBadge(status) {
    let badgeHTML = '';
    switch (status) {
        case 'unpaid':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                    Unpaid
                </span>
            `;
            break;
        case 'paid':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-700 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Paid
                </span>
            `;
            break;
        case 'partially_paid':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-orange-200 text-orange-700 whitespace-nowrap">
                    <img src="/build/assets/icons/partially-paid.svg" alt="Partially Paid Icon" class="w-3.5 h-3.5">
                    Partially Paid
                </span>
            `;
            break;
        case 'cancelled':
            badgeHTML = `
                <span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancelled
                </span>
            `;
            break;
        default:
            badgeHTML = `<span class="text-gray-500">Unknown</span>`;
    }
    return badgeHTML;
}
