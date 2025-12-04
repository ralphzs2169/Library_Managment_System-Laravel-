import { openIssueDetailsModal } from "./viewIssueHelpers";

export function populateSummaryHeader(book, editFormContainer) {
    const sTitle = editFormContainer.querySelector('#edit-summary-title');
    const sIsbn = editFormContainer.querySelector('#edit-summary-isbn');
    const sAuthor = editFormContainer.querySelector('#edit-summary-author');
    const sCover = editFormContainer.querySelector('#edit-summary-cover');
    const sCoverPlaceholder = editFormContainer.querySelector('#edit-summary-cover-placeholder');
    const sPrice = editFormContainer.querySelector('#edit-summary-price');
    const priceInput = editFormContainer.querySelector('#price');

    if (sTitle) sTitle.textContent = book.title || '—';
    if (sIsbn) sIsbn.textContent = book.isbn || 'N/A';
    if (sAuthor) {
        const parts = [book.author?.firstname, book.author?.middle_initial ? book.author.middle_initial + '.' : '', book.author?.lastname];
        sAuthor.textContent = parts.filter(Boolean).join(' ') || 'N/A';
    }
    
    if (sCover && sCoverPlaceholder) {
        if (book.cover_image) {
            sCover.src = `/storage/${book.cover_image}`;
            sCover.classList.remove('hidden');
            sCoverPlaceholder.classList.add('hidden');
        } else {
            sCover.src = '';
            sCover.classList.add('hidden');
            sCoverPlaceholder.classList.remove('hidden');
        }
    }

    if (sPrice) {
        sPrice.textContent = book.price !== undefined && book.price !== null
            ? Number(book.price).toFixed(2)
            : '—';
    }
    if (priceInput) {
        priceInput.value = book.price !== undefined && book.price !== null
            ? Number(book.price).toFixed(2)
            : '';
    }
}

const COPIES_PER_PAGE = 5;
let currentCopiesPage = 1;
export let allCopiesData = [];
let nextNewCopyId = -1;

/**
 * Render status badge for a copy
 */
export function renderStatusBadge(status) {
    const statusConfig = {
        available: {
            class: 'bg-green-200 text-green-700 border-green-100',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
            label: 'Available'
        },
        borrowed: {
            class: 'bg-blue-200 text-blue-700 border-blue-100',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>`,
            label: 'Borrowed'
        },
        damaged: {
            class: 'bg-orange-200 text-orange-700 border-orange-100',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>`,
            label: 'Damaged'
        },
        lost: {
            class: 'bg-red-200 text-red-700 border-red-100',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>`,
            label: 'Lost'
        },
        withdrawn: {
            class: 'bg-gray-200 text-gray-700 border-gray-200',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>`,
            label: 'Withdrawn'
        },
        pending_issue_review: {
            class: 'bg-amber-300 text-amber-800 border-amber-200',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
            label: 'Pending Review'
        },
        on_hold_for_pickup: {
            class: 'bg-yellow-100 text-yellow-800 border-yellow-200', 
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
            label: 'On Hold For Pickup'
        }
    };

    const config = statusConfig[status] || statusConfig['available'];
    return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ${config.class}">
        ${config.icon}
        ${config.label}
    </span>`;
}

/**
 * Main function to render copies table
 */
export function renderCopiesTable(book, editFormContainer) {
    const tbody = editFormContainer.querySelector('#copies-table-body');
    if (!tbody) return;

    const copies = Array.isArray(book.copies) ? book.copies : [];
    allCopiesData = copies;
    
    const badge = editFormContainer.querySelector('#copies-count-badge');
    if (badge) badge.textContent = copies.length;

    if (!copies.length) {
        tbody.innerHTML = `
            <tr class="bg-white">
                <td colspan="4" class="py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-500 font-medium mb-1">No copies found</p>
                        <p class="text-gray-400 text-xs">Click "Add Copy" to add new copies</p>
                    </div>
                </td>
            </tr>`;
        return;
    }

    currentCopiesPage = 1;
    renderCopiesPage(editFormContainer);
}

/**
 * Render specific page of copies
 */
function renderCopiesPage(editFormContainer) {
    const tbody = editFormContainer.querySelector('#copies-table-body');
    if (!tbody) return;

    const startIndex = (currentCopiesPage - 1) * COPIES_PER_PAGE;
    const endIndex = startIndex + COPIES_PER_PAGE;
    const pageCopies = allCopiesData.slice(startIndex, endIndex);

    tbody.innerHTML = pageCopies.map(copy => renderCopyRow(copy)).join('');
    attachCopyEventListeners(tbody, editFormContainer);
    renderCopiesPagination(editFormContainer);
}

/**
 * Render pagination controls
 */
function renderCopiesPagination(editFormContainer) {
    const paginationContainer = editFormContainer.querySelector('#copies-pagination');
    if (!paginationContainer) return;

    const totalPages = Math.ceil(allCopiesData.length / COPIES_PER_PAGE);
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        paginationContainer.classList.add('hidden');
        return;
    }

    paginationContainer.classList.remove('hidden');
    paginationContainer.innerHTML = `
        <div class="flex justify-between items-center mt-4">
            <button ${currentCopiesPage === 1 ? 'disabled' : ''} 
                class="copies-pagination-btn text-sm text-accent font-medium hover:underline ${currentCopiesPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                data-page="${currentCopiesPage - 1}">
                Prev
            </button>
            <span class="text-sm text-gray-700">Page ${currentCopiesPage} of ${totalPages}</span>
            <button ${currentCopiesPage === totalPages ? 'disabled' : ''} 
                class="copies-pagination-btn text-sm text-accent font-medium hover:underline ${currentCopiesPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                data-page="${currentCopiesPage + 1}">
                Next
            </button>
        </div>
    `;

    paginationContainer.querySelectorAll('.copies-pagination-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            currentCopiesPage = parseInt(this.dataset.page);
            renderCopiesPage(editFormContainer);
        });
    });
}


function renderCopyRow(copy) {
    const current = copy.status || 'available';
    const isNew = copy.id < 0;
    const isPendingReview = current === 'pending_issue_review';
    const reviewAction = copy._reviewAction || null;

    const rowClasses = isPendingReview 
        ? 'bg-amber-50 border-l-4 border-l-amber-500 hover:bg-amber-100/60' 
        : (isNew ? 'bg-green-50/30' : 'hover:bg-gray-50');

    const validTransitions = {
        available: ['damaged', 'lost', 'withdrawn'],
        borrowed: [],
        damaged: ['withdrawn', 'available'],
        lost: ['available'],
        withdrawn: [],
        pending_issue_review: [],
        on_hold_for_pickup: ['available', 'damaged', 'withdrawn', 'lost']
    };

    const allowedOptions = ['available', 'borrowed', 'damaged', 'lost', 'withdrawn', 'pending_issue_review', 'on_hold_for_pickup'].filter(status => {
        return validTransitions[current]?.includes(status) || status === current;
    });

    const isDisabled = allowedOptions.length <= 1 || isNew;

    const options = allowedOptions
        .map(status => {
            let label = status === 'pending_issue_review' ? 'Pending Review' : status.charAt(0).toUpperCase() + status.slice(1);
            label = status === 'on_hold_for_pickup' ? 'On Hold for Pickup' : status.charAt(0).toUpperCase() + status.slice(1);
            return `<option value="${status}" ${current === status ? 'selected' : ''}>${label}</option>`;
        })
        .join('');

    const tooltipMessage = getTooltipMessage(isNew, isPendingReview, current);
    const statusSelectHtml = renderStatusSelectColumn(isPendingReview, isDisabled, options, tooltipMessage, copy, current, isNew);
    const actionsHtml = renderActionsColumn(isPendingReview, reviewAction, isNew, copy.id);
    const reviewBadge = renderReviewBadge(reviewAction);

    return `
        <tr class="transition-colors relative ${rowClasses}" data-copy-id="${copy.id}" data-original-status="${current}">
            <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-accent/10 to-teal-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-bold text-accent">#${copy.copy_number}</span>
                    </div>
                    <span class="text-gray-800 font-medium">Copy ${copy.copy_number}</span>
                    ${isNew ? '<span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">NEW</span>' : ''}
                    ${reviewBadge}
                </div>
            </td>
            <td class="px-4 py-3 current-status-badge" data-copy-id="${copy.id}">${renderStatusBadge(current)}</td>
            <td class="px-4 py-3">${statusSelectHtml}</td>
            <td class="px-4 py-3 text-center">${actionsHtml}</td>
        </tr>
    `;
}

/**
 * Get tooltip message based on copy state
 */
function getTooltipMessage(isNew, isPendingReview, current) {
    if (isNew) return "New copies are added as 'Available' by default. Save to modify.";
    if (isPendingReview) return "Status is locked pending issue review approval/rejection.";
    
    switch(current) {
        case 'borrowed': return "Cannot manually change a borrowed book. Use the return or report process.";
        case 'withdrawn': return "Withdrawn books are permanently removed from circulation and cannot be reactivated.";
        case 'lost':
        case 'damaged': return "This book's status can only be changed to withdrawn or available.";
        default: return "No other status available to select.";
    }
}

/**
 * Render status select column
 */
function renderStatusSelectColumn(isPendingReview, isDisabled, options, tooltipMessage, copy, current, isNew) {

    const copyId = copy.id;
    const reportType = (copy.pending_issue_report?.report_type === 'damage') ? 'damaged' : 'lost';
    if (isPendingReview) {
        return `
            <div class="px-3 rounded-lg text-xs font-large">
                Will be marked as <span class="font-bold">${reportType}</span> upon approval
            </div>
            <!-- Hidden input to include pending review copies in form submission -->
            <input type="hidden" name="copies[${copyId}]" value="${current}">
        `;
    }
    
    if (isDisabled) {
        return `
            <div class="relative inline-block w-full group">
                <select class="copy-status-select px-3 py-2 text-sm border-2 border-gray-200 rounded-lg bg-gray-100 cursor-not-allowed opacity-50 w-full" disabled>
                    ${options}
                </select>
                <!-- Hidden input to include disabled copies in form submission -->
                <div class="tooltip absolute bottom-full right-0 text-center transform mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 whitespace-nowrap">
                ${tooltipMessage}
                <div class="absolute top-full right-6 border-4 border-transparent border-t-gray-900"></div>
                </div>
                </div>
                <input type="hidden" name="copies[${copyId}]" value="${current}">
        `;
    }
    
    return `
        <select name="copies[${copyId}]" id="copy-status-${copyId}"
            class="copy-status-select px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition bg-white hover:border-gray-300 cursor-pointer w-full"
            data-original="${current}"
            data-copy-id="${copyId}"
            data-is-new="${isNew}">
            ${options}
        </select>
    `;
}

/**
 * Render actions column buttons
 */
function renderActionsColumn(isPendingReview, reviewAction, isNew, copyId) {
    if (isPendingReview && !reviewAction) {
        return `
            <div class="flex items-center justify-center gap-2 relative">
                <div class="absolute -top-2 -right-3 z-10 pointer-events-none">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-500 text-white rounded-full shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                </div>
                <button type="button" class="approve-issue-btn inline-flex items-center gap-1 px-3 py-1.5 text-xs text-white bg-green-500 hover:bg-green-400 cursor-pointer rounded-md transition font-medium" data-copy-id="${copyId}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Approve
                </button>
                <button type="button" class="reject-issue-btn inline-flex items-center gap-1 px-3 py-1.5 text-xs text-white bg-red-400 hover:bg-red-300 rounded-md cursor-pointer transition font-medium" data-copy-id="${copyId}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reject
                </button>
                <!-- Improved View Details icon-only button -->
                <button type="button"
                        class="view-issue-details-btn group relative inline-flex items-center justify-center w-8 h-8 rounded-full border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 text-gray-600 hover:text-gray-800 transition"
                        aria-label="View issue details"
                        title="View Details"
                        data-copy-id="${copyId}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z" />
                    </svg>
                    <span class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 rounded bg-gray-900 text-white text-[10px] font-medium opacity-0 group-hover:opacity-100 transition">
                        View details
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></span>
                    </span>
                </button>
            </div>
        `;
    }
    
    if (reviewAction || !isNew) {
        return `
            <button type="button" class="reset-copy-btn inline-flex items-center gap-1 px-3 py-1.5 text-xs text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-md transition font-medium" data-copy-id="${copyId}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </button>
        `;
    }
    
    return `
        <button type="button" class="remove-copy-btn inline-flex items-center gap-1 px-3 py-1.5 text-xs text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-md transition font-medium" data-copy-id="${copyId}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Remove
        </button>
    `;
}

/**
 * Render review action badge
 */
function renderReviewBadge(reviewAction) {
    if (reviewAction === 'approved') {
        return '<span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">Approved</span>';
    }
    if (reviewAction === 'rejected') {
        return '<span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full uppercase">Rejected</span>';
    }
    return '';
}


// Attach event listeners to copy rows
function attachCopyEventListeners(tbody, editFormContainer) {
    // Status change
    tbody.querySelectorAll('.copy-status-select').forEach(select => {
        select.addEventListener('change', function() {
            handleStatusChange(this, tbody, editFormContainer);
        });
    });

    // Reset button
    tbody.querySelectorAll('.reset-copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            handleReset(this.dataset.copyId, tbody, editFormContainer);
        });
    });

    // Remove button
    tbody.querySelectorAll('.remove-copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            handleRemove(this.dataset.copyId, editFormContainer);
        });
    });

    // Approve button
    tbody.querySelectorAll('.approve-issue-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            handleApprove(this.dataset.copyId, editFormContainer);
        });
    });

    // Reject button
    tbody.querySelectorAll('.reject-issue-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            handleReject(this.dataset.copyId, editFormContainer);
        });
    });

    // View issue details button
    tbody.querySelectorAll('.view-issue-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const copy = allCopiesData.find(c => String(c.id) === String(this.dataset.copyId));
            openIssueDetailsModal(copy.pending_issue_report, copy);
        });
    });
}

/**
 * Handle status change
 */
function handleStatusChange(select, tbody, editFormContainer) {
    const copyId = select.dataset.copyId;
    const newStatus = select.value;
    const originalStatus = select.dataset.original;
    
    const copyIndex = allCopiesData.findIndex(c => String(c.id) === String(copyId));
    if (copyIndex !== -1) {
        allCopiesData[copyIndex].status = newStatus;
    }
    
    const statusBadge = tbody.querySelector(`.current-status-badge[data-copy-id="${copyId}"]`);
    if (statusBadge) {
        statusBadge.innerHTML = renderStatusBadge(newStatus);
    }
    
    const isChanged = newStatus !== originalStatus;
    if (isChanged) {
        select.classList.add('border-accent', 'bg-accent/5');
        select.classList.remove('border-gray-200');
    } else {
        select.classList.remove('border-accent', 'bg-accent/5');
        select.classList.add('border-gray-200');
    }
    
    updateStatsAfterChange(tbody, editFormContainer);
}

/**
 * Handle reset button click
 */
function handleReset(copyId, tbody, editFormContainer) {
    const row = tbody.querySelector(`tr[data-copy-id="${copyId}"]`);
    const select = tbody.querySelector(`#copy-status-${copyId}`);
    
    if (!row) return;
    
    const copyIndex = allCopiesData.findIndex(c => String(c.id) === String(copyId));
    if (copyIndex === -1) return;

    const originalStatus = row.dataset.originalStatus;
 
    if (allCopiesData[copyIndex]._reviewAction) {
        allCopiesData[copyIndex].status = 'pending_issue_review';
        delete allCopiesData[copyIndex]._reviewAction;
        renderCopiesPage(editFormContainer);
        updateStatsAfterChange(tbody, editFormContainer);
        updatePendingIssueResolvedFlag(editFormContainer);
        return;
    }

    if (select) {
        select.value = originalStatus;
        allCopiesData[copyIndex].status = originalStatus;
        
        const statusBadge = tbody.querySelector(`.current-status-badge[data-copy-id="${copyId}"]`);
        if (statusBadge) {
            statusBadge.innerHTML = renderStatusBadge(originalStatus);
        }
        
        select.classList.remove('border-accent', 'bg-accent/5');
        select.classList.add('border-gray-200');
        
        const isPendingReview = originalStatus === 'pending_issue_review';
        if (isPendingReview) {
            row.classList.add('bg-amber-50', 'border-l-4', 'border-l-amber-500');
            row.classList.remove('hover:bg-gray-50');
        } else {
            row.classList.remove('bg-amber-50', 'border-l-4', 'border-l-amber-500');
            row.classList.add('hover:bg-gray-50');
        }
    }
    
    updateStatsAfterChange(tbody, editFormContainer);
    // Always update the flag after any reset operation
    updatePendingIssueResolvedFlag(editFormContainer);
}

/**
 * Handle remove button click
 */
function handleRemove(copyId, editFormContainer) {
    allCopiesData = allCopiesData.filter(c => String(c.id) !== String(copyId));
    
    const totalPages = Math.ceil(allCopiesData.length / COPIES_PER_PAGE);
    if (currentCopiesPage > totalPages) {
        currentCopiesPage = Math.max(1, totalPages);
    }
    
    renderCopiesPage(editFormContainer);
    const tbody = editFormContainer.querySelector('#copies-table-body');
    updateStatsAfterChange(tbody, editFormContainer);
}

/**
 * Handle approve button click
 */
function handleApprove(copyId, editFormContainer) {
    const copyIndex = allCopiesData.findIndex(c => String(c.id) === String(copyId));
    if (copyIndex !== -1) {
        allCopiesData[copyIndex].status = 'damaged';
        allCopiesData[copyIndex]._reviewAction = 'approved';
        renderCopiesPage(editFormContainer);
        const tbody = editFormContainer.querySelector('#copies-table-body');
        updateStatsAfterChange(tbody, editFormContainer);
        updatePendingIssueResolvedFlag(editFormContainer);
    }
}

/**
 * Handle reject button click
 */
function handleReject(copyId, editFormContainer) {
    const copyIndex = allCopiesData.findIndex(c => String(c.id) === String(copyId));
    if (copyIndex !== -1) {
        allCopiesData[copyIndex].status = 'available';
        allCopiesData[copyIndex]._reviewAction = 'rejected';
        renderCopiesPage(editFormContainer);
        const tbody = editFormContainer.querySelector('#copies-table-body');
        updateStatsAfterChange(tbody, editFormContainer);
        updatePendingIssueResolvedFlag(editFormContainer);
    }
}

/**
 * Update the pending_issue_resolved flag based on current state
 */
export function updatePendingIssueResolvedFlag(editFormContainer) {
    const hasResolvedIssues = allCopiesData.some(copy => copy._reviewAction);
    const flagInput = editFormContainer.querySelector('#pending_issue_resolved');
    if (flagInput) {
        flagInput.value = hasResolvedIssues ? 'true' : 'false';
    }
}

/**
 * Add new copy
 */
export function addNewCopy(editFormContainer) {
    const tbody = editFormContainer.querySelector('#copies-table-body');
    if (!tbody) return;

    const copyNumbers = allCopiesData.map(c => c.copy_number);
    const nextCopyNumber = Math.max(0, ...copyNumbers) + 1;

    const newCopy = {
        id: nextNewCopyId--,
        copy_number: nextCopyNumber,
        status: 'available'
    };

    allCopiesData.push(newCopy);
    
    const totalPages = Math.ceil(allCopiesData.length / COPIES_PER_PAGE);
    currentCopiesPage = totalPages;
    
    renderCopiesPage(editFormContainer);
    updateStatsAfterChange(tbody, editFormContainer);
}

/**
 * Update stats after changes
 */
export function updateStatsAfterChange(tbody, editFormContainer) {
    if (!tbody) return;
    
    const counts = { available: 0, borrowed: 0, damaged: 0, lost: 0, withdrawn: 0 };
    let total = 0;
    
    allCopiesData.forEach(copy => {
        const status = copy.status;
        if (counts[status] !== undefined) {
            counts[status] += 1;
        }
        total += 1;
    });
    
    const setText = (id, val) => {
        const el = editFormContainer.querySelector('#' + id);
        if (el) el.textContent = val;
    };
    
    setText('stat-available', counts.available);
    setText('stat-borrowed', counts.borrowed);
    setText('stat-damaged', counts.damaged);
    setText('stat-lost', counts.lost);
    setText('stat-total', total);

    // Update the copies count badge beside the title
    const badge = editFormContainer.querySelector('#copies-count-badge');
    if (badge) badge.textContent = total;
}

/**
 * Reset all copies to their original state (remove all _reviewAction flags)
 */
export function resetAllCopiesToOriginal(editFormContainer) {
    // Remove all _reviewAction flags
    allCopiesData.forEach(copy => {
        if (copy._reviewAction) {
            delete copy._reviewAction;
            // Restore original status if it was pending review
            const row = editFormContainer.querySelector(`tr[data-copy-id="${copy.id}"]`);
            if (row) {
                const originalStatus = row.dataset.originalStatus;
                copy.status = originalStatus || copy.status;
            }
        }
    });
    
    // Re-render the copies table
    renderCopiesPage(editFormContainer);
    
    // Update stats
    const tbody = editFormContainer.querySelector('#copies-table-body');
    updateStatsAfterChange(tbody, editFormContainer);
    
    // Update the pending issue resolved flag
    updatePendingIssueResolvedFlag(editFormContainer);
}



