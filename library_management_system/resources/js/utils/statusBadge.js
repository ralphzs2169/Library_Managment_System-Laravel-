
export function getBorrowingStatusBadge(transaction) {
    const status = transaction.status;
    const daysUntilDue = transaction.days_until_due;
    const daysOverdue = transaction.days_overdue;
    // Assuming dueReminderThreshold is available in the transaction object or a global setting
    const dueReminderThreshold = transaction.due_reminder_threshold ?? 3; 
    console.log('Due Reminder Threshold:', dueReminderThreshold);
    let badgeBg = '';
    let svg = '';
    let label = '';

    // Helper to format labels (e.g., 'borrowed' -> 'Borrowed')

    // --- 1. Returned Statuses (Final States) ---
    if (transaction.returned_at) {
        const returnedDate = new Date(transaction.returned_at);
        const dueDate = new Date(transaction.due_at);
        
        if (returnedDate <= dueDate) {
            // Returned On Time (Green Check)
            badgeBg = 'bg-green-200 text-green-700 border border-green-100';
            svg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
            label = 'Returned On Time';
        } else {
            // Returned Late (Orange Alert Icon)
            badgeBg = 'bg-orange-200 text-orange-700 border border-orange-100';
            svg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
            label = 'Returned Late';
            
            if (typeof daysOverdue === 'number' && daysOverdue > 0) {
                const dayText = daysOverdue === 1 ? 'day' : 'days';
                // Note: The inner HTML must be escaped/formatted carefully in JS string
                label += `<div class="mt-1 text-[11px] text-red-700">(${daysOverdue} ${dayText} late)</div>`;
            }
        }
    } 
    // --- 2. Overdue Status ---
    else if (status === 'overdue') {
        badgeBg = 'bg-red-200 text-red-700 border border-red-100';
        svg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        
        const dayText = daysOverdue === 1 ? 'day' : 'days';
        label = `${daysOverdue} ${dayText} overdue`;
    } 
    // --- 3. Borrowed Status (Active Loans) ---
    else if (status === 'borrowed') {
        const isDueSoon = typeof daysUntilDue === 'number' && daysUntilDue <= dueReminderThreshold;
        
        const timerSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;

        if (isDueSoon) {
            // Due Soon (Orange Alert Icon, Timer icon)
            badgeBg = 'bg-orange-200 text-orange-700 border border-orange-100';
            svg = timerSvg;
            
            if (daysUntilDue === 0) {
                label = 'Due Today';
            } else if (daysUntilDue === 1) {
                label = 'Due in 1 day';
            } else {
                label = 'Due in ' + daysUntilDue + ' days';
            }
        } else {
            // Borrowed (Standard Blue, Timer icon)
            badgeBg = 'bg-blue-200 text-blue-700 border border-blue-100';
            svg = timerSvg;
            label = 'Due in ' + daysUntilDue + ' days';
        }
    } 
    // --- 4. Default/Other Statuses ---
    else {
        // Fallback for unexpected statuses
        badgeBg = 'bg-gray-100 text-gray-700 border border-gray-200';
        svg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" /></svg>`;
        label = formatLabel(status);
    }

    // Construct the final HTML badge
    const badgeHtml =  `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold ${badgeBg}">
        ${svg}
        ${label}
    </span>`;

    return badgeHtml;
}

export function getReservationStatusBadge(status) {

    let badgeHtml = '';
    let badgeIcon = '';
    let badgeClass = '';
    let formattedStatus = formatLabel(status);
    let label = '';
    
    switch (formattedStatus) {
        case 'pending':
            badgeClass = 'bg-yellow-200 text-yellow-800';
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            label = 'Pending';
            break;

        case 'ready for pickup':
            badgeClass = 'bg-blue-100 text-blue-700'; 
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 384 512">
                    <path fill="#1D4ED8" d="M368 48h4c6.627 0 12-5.373 12-12V12c0-6.627-5.373-12-12-12H12C5.373 0 0 5.373 0 12v24c0 6.627 5.373 12 12 12h4c0 80.564 32.188 165.807 97.18 208C47.899 298.381 16 383.9 16 464h-4c-6.627 0-12 5.373-12 12v24c0 6.627 5.373 12 12 12h360c6.627 0 12-5.373 12-12v-24c0-6.627-5.373-12-12-12h-4c0-80.564-32.188-165.807-97.18-208C336.102 213.619 368 128.1 368 48zM64 48h256c0 101.62-57.307 184-128 184S64 149.621 64 48zm256 416H64c0-101.62 57.308-184 128-184s128 82.38 128 184z" />
                </svg>`;
            label = 'Ready for Pickup';
            break;

        case 'completed':
            badgeClass = 'bg-green-200 text-green-700';
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            label = 'Completed';
            break;

        case 'cancelled':
        case 'expired': 
            badgeClass = 'bg-gray-200 text-gray-700';
            badgeIcon = `
               <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>`;
            label = formattedStatus === 'expired' ? 'Expired' : 'Cancelled';
            break;

        default:
            // Fallback for any other unexpected status
            return `<span class="text-gray-500 text-xs">${formattedStatus}</span>`;
    }

    // Construct the final HTML badge
    badgeHtml = `
        <span class="inline-flex items-center gap-1 w-full px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}">
            ${badgeIcon}
            ${label}
        </span>
    `;

    return badgeHtml;
}

export function getPenaltyStatusBadge(status) {
    const formattedStatus = formatLabel(status);
    let badgeBg = '';
    let svgOrImg = '';
    let label = '';

    switch (formattedStatus) {
        case 'unpaid':
            badgeBg = 'bg-red-100 text-red-700';
            // Alert/Warning Icon SVG for Unpaid
            svgOrImg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
            </svg>`;
            label = 'Unpaid';
            break;

        case 'partially paid':
            badgeBg = 'bg-orange-200 text-orange-700';
            // Placeholder for the partially-paid SVG/Image
            svgOrImg = `<img src="/build/assets/icons/partially-paid.svg" alt="Partially Paid Icon" class="w-3.5 h-3.5">`;
            label = 'Partially Paid';
            break;

        case 'paid':
            badgeBg = 'bg-green-200 text-green-700';
            // Checkmark Icon SVG for Paid
            svgOrImg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`;
            label = 'Paid';
            break;

        case 'cancelled':
            badgeBg = 'bg-gray-200 text-gray-700';
            // X / Cancel Icon SVG
            svgOrImg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>`;
            label = 'Cancelled'; // Override default formatting to match original Blade logic
            break;
            
        default:
            // Fallback for UNKNOWN status
            badgeBg = 'bg-gray-100 text-gray-500';
            svgOrImg = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" /></svg>`;
            label = 'Unknown';
            break;
    }

    // Construct the final HTML badge
    return `<span class="w-full inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold ${badgeBg} whitespace-nowrap">
        ${svgOrImg}
        ${label}
    </span>`;
}

export function getPenaltyTypeBadge(type) {
    
    // Standardize key for comparison
    const formattedType = formatLabel(type);

    let badgeClass = '';
    let iconHtml = '';
    let label = '';

    switch (formattedType) {
        case 'late return':
            badgeClass = 'text-red-700';
            label = 'Late Return';
            iconHtml = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>`;
            break;

        case 'lost book':
            badgeClass = 'text-purple-700';
            label = 'Lost';
            iconHtml = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>`;
            break;

        case 'damaged book':
            badgeClass = 'text-orange-700';
            label = 'Damaged';
            iconHtml = `<img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">`;
            break;

        default:
            // Fallback for unknown type
            return `<span class="text-gray-500">${formatLabel(type)}</span>`;
    }

    return `<span class="w-full inline-flex items-center gap-1 rounded-full text-xs font-semibold ${badgeClass}">
        ${iconHtml}
        ${label}
    </span>`;
}

function formatLabel(s) {
    if (!s) {
        return 'Unknown';
    }
    // Convert to lowercase, split by underscores, , and rejoin
    return s.toLowerCase()
        .split('_')
        .map(word => word.charAt(0).toLowerCase() + word.slice(1))
        .join(' ');
}
export function getClearanceStatusBadge(status) {
    const formattedStatus = formatLabel(status); 
   
    let badgeClass = '';
    let badgeIcon = '';
    let label = '';
    
    switch (formattedStatus) {
        case 'approved':
            badgeClass = 'bg-green-200 text-green-700 border border-green-100';
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>`;
            label = 'Approved';
            break;

        case 'rejected':
            badgeClass = 'bg-red-200 text-red-700 border border-red-100';
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>`;
            label = 'Rejected';
            break;
            
        case 'pending':
        default: // Use pending as the default if status is unexpected/unformatted
            badgeClass = 'bg-yellow-100 text-yellow-700 border border-yellow-200';
            badgeIcon = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            label = 'Pending';
            break;
    }

    // Construct the final HTML badge, consistent with getBorrowingStatusBadge structure
    const badgeHtml = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}">
        ${badgeIcon}
        ${label}
    </span>`;

    return badgeHtml;
}

export function renderBookCopyStatusBadge(status) {
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
            icon: `<img src="/build/assets/icons/damaged-badge.svg" alt="Damaged Icon" class="w-3.5 h-3.5">`,
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